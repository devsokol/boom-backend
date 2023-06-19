<?php

namespace GrumPHP\Tasks;

use GrumPHP\Collection\FilesCollection;
use GrumPHP\Runner\TaskResult;
use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PhpStanTask extends ExtendExternalTask
{
    public static function getConfigurableOptions(): OptionsResolver
    {
        return new OptionsResolver();
    }

    public function canRunInContext(ContextInterface $context): bool
    {
        return $context instanceof GitPreCommitContext || $context instanceof RunContext;
    }

    public function run(ContextInterface $context): TaskResultInterface
    {
        $uncommittedFileList = $this->getUncommittedFiles();

        $filteredUncommittedFiles = $this->filterPhpFiles($uncommittedFileList);

        if (count($filteredUncommittedFiles) === 0) {
            return TaskResult::createSkipped($this, $context);
        }

        $arguments = $this->processBuilder->createArgumentsForCommand('phpstan');
        $arguments->addSeparatedArgumentArray('analyse', ['--no-progress']);
        $arguments->addFiles($filteredUncommittedFiles);

        $process = $this->processBuilder->buildProcess($arguments);
        //file_put_contents('storage/logs/laravel.log', print_r($process->getCommandLine(), true));
        $process->run();

        if (! $process->isSuccessful()) {
            $message = sprintf("Pint command:\n<fg=yellow>%s</>\n\n", $process->getCommandLine());
            $message .= $this->formatter->format($process);

            $message .= "\n\n--->For more details run command: "
                      . '<fg=green;options=bold>' . $this->formatCommandLine($filteredUncommittedFiles) . "</>\n";

            return TaskResult::createFailed($this, $context, $message);
        }

        return TaskResult::createPassed($this, $context);
    }

    private function formatCommandLine(FilesCollection $files): string
    {
        return './vendor/bin/phpstan analyse ' . $this->convertFilesToString($files);
    }
}
