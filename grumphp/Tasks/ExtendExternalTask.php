<?php

namespace GrumPHP\Tasks;

use GrumPHP\Collection\FilesCollection;
use GrumPHP\Task\AbstractExternalTask;
use Symfony\Component\Process\Process;

abstract class ExtendExternalTask extends AbstractExternalTask
{
    protected $excludedDirectories = [
        "bootstrap",
        "config",
        "database",
        "docker",
        "grumphp",
        "lang",
        "public",
        "resources",
        "tests",
    ];

    protected static function loadLaravelConfiguration(string $confFile): array
    {
        $configPath = realpath(__DIR__ . '/../../config/' . $confFile . '.php');
        return file_exists($configPath) ? require $configPath : [];
    }

    protected function getUncommittedFiles(): array
    {
        $gitCommand = 'git diff --name-only --diff-filter=d HEAD';
        $gitProcess = Process::fromShellCommandline($gitCommand);

        $gitProcess->setWorkingDirectory(getcwd());

        $gitProcess->run();
        $output = $gitProcess->getOutput();

        return preg_split('/\s+/', trim($output), -1, PREG_SPLIT_NO_EMPTY);
    }

    protected function filterPhpFiles(array $fileList): FilesCollection
    {
        $fileList = array_map(function ($file) {
            return new \SplFileInfo($file);
        }, $fileList);

        $phpFiles = array_filter($fileList, function (\SplFileInfo $file) {
            return $file->getExtension() === 'php' && !$this->isExcluded($file);
        });

        return new FilesCollection($phpFiles);
    }

    protected function convertFilesToString(FilesCollection $files): string
    {
        return implode(' ', array_map(static function (\SplFileInfo $file) {
            return $file->getPathname();
        }, iterator_to_array($files)));
    }

    protected function isExcluded(\SplFileInfo $file): bool
    {
        foreach ($this->excludedDirectories as $excludedDirectory) {
            if (strpos($file->getPathname(), $excludedDirectory) === 0) {
                return true;
            }
        }
        return false;
    }
}
