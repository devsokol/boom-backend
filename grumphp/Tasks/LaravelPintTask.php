<?php

namespace GrumPHP\Tasks;

use GrumPHP\Runner\TaskResult;
use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\AbstractExternalTask;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LaravelPintTask extends AbstractExternalTask
{
    /**
     * {@inheritdoc}
     */
    public static function getConfigurableOptions(): OptionsResolver
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'preset' => 'laravel',
        ]);

        $resolver->addAllowedTypes('preset', ['string']);

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
        $config = $this->getConfig()->getOptions();

        $arguments = $this->processBuilder->createArgumentsForCommand('pint');
        $arguments->add('--test');
        $arguments->add('--dirty');
        $arguments->addOptionalArgument('--preset=%s', $config['preset']);

        $process = $this->processBuilder->buildProcess($arguments);
        $process->run();

        if (! $process->isSuccessful()) {
            $message = sprintf("Pint command:\n<fg=yellow>%s</>\n\n", $process->getCommandLine());
            $message .= $this->formatter->format($process);

            $message .= "\n\nMore details: <fg=green;options=bold>composer pint-dirty-test</>";
            $message .= "\nAuto fix: <fg=green;options=bold>composer pint-dirty-fix</>\n";

            return TaskResult::createFailed($this, $context, $message);
        }

        return TaskResult::createPassed($this, $context);
    }
}
