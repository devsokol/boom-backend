<?php

namespace GrumPHP\Tasks;

use GrumPHP\Collection\FilesCollection;
use GrumPHP\Runner\TaskResult;
use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhpInsightsTask extends ExtendExternalTask
{
    /**
     * {@inheritdoc}
     */
    public static function getConfigurableOptions(): OptionsResolver
    {
        $resolver = new OptionsResolver();

        $insightsConfig = self::loadLaravelConfiguration('insights');

        $resolver->setDefaults([
            'config-path' => null,
            'min-quality' => $insightsConfig['requirements']['min-quality'] ?? 80,
            'min-architecture' => $insightsConfig['requirements']['min-architecture'] ?? 75,
            'min-complexity' => $insightsConfig['requirements']['min-complexity'] ?? 90,
            'min-style' => $insightsConfig['requirements']['min-style'] ?? 95,
        ]);

        $resolver->addAllowedTypes('config-path', ['null', 'string']);
        $resolver->addAllowedTypes('min-architecture', ['null', 'int']);
        $resolver->addAllowedTypes('min-complexity', ['null', 'int']);
        $resolver->addAllowedTypes('min-quality', ['null', 'int']);
        $resolver->addAllowedTypes('min-style', ['null', 'int']);

        return $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function canRunInContext(ContextInterface $context): bool
    {
        return $context instanceof GitPreCommitContext || $context instanceof RunContext;
    }

    /**
     * {@inheritdoc}
     */
    public function run(ContextInterface $context): TaskResultInterface
    {
        $uncommittedFileList = $this->getUncommittedFiles();

        $filteredUncommittedFiles = $this->filterPhpFiles($uncommittedFileList);

        if (0 === count($filteredUncommittedFiles)) {
            return TaskResult::createSkipped($this, $context);
        }

        $config = $this->getConfig()->getOptions();

        $arguments = $this->processBuilder->createArgumentsForCommand('php');
        $arguments->addSeparatedArgumentArray('artisan', ['insights', '--no-interaction', '--verbose']);
        $arguments->addOptionalArgument('--min-architecture=%s', $config['min-architecture']);
        $arguments->addOptionalArgument('--min-complexity=%s', $config['min-complexity']);
        $arguments->addOptionalArgument('--min-quality=%s', $config['min-quality']);
        $arguments->addOptionalArgument('--min-style=%s', $config['min-style']);

        $arguments->addFiles($filteredUncommittedFiles);

        $process = $this->processBuilder->buildProcess($arguments);
        //file_put_contents('storage/logs/laravel.log', print_r(, true));
        $process->run();

        $message = $this->formatter->format($process);

        if (! $process->isSuccessful() || $this->isContainsIssuesInMessage($message)) {
            $message .= "\n\n--->More details: "
                      . '<fg=green;options=bold>' . $this->formatCommandLine($filteredUncommittedFiles) . "</>\n"
                      . '--->Auto fix command: '
                      . '<fg=green;options=bold>' . $this->formatCommandLine($filteredUncommittedFiles, true) . '</>';

            return TaskResult::createFailed($this, $context, $message);
        }

        return TaskResult::createPassed($this, $context);
    }

    private function formatCommandLine(FilesCollection $files, bool $isAutoFix = false): string
    {
        if ($isAutoFix) {
            return 'php artisan insights --fix ' . $this->convertFilesToString($files);
        }

        return 'php artisan insights ' . $this->convertFilesToString($files);
    }

    private function isContainsIssuesInMessage(string $message): mixed
    {
        $pattern = '/(•\s\[Code|•\s\[Complexity|•\s\[Architecture|•\s\[Style)/mi';

        preg_match($pattern, $message, $matches);

        return count($matches);
    }
}
