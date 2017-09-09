<?php

namespace Korri\ComposerVersion;

class Command
{
    const OPTIONS = [
        'f:' => 'file:',
        'h' => 'help',
    ];

    /**
     * Arguments passed to the command
     * @var array
     */
    protected $arguments = [];

    /**
     * Options passed to the command
     * @var
     */
    protected $options = [];

    /**
     * Composer file parser
     * @var ComposerFile
     */
    protected $composerFile;

    /**
     * Git util
     * @var Git
     */
    protected $git;

    public function __construct(ComposerFile $composerFile = null, Git $git = null)
    {
        if ($composerFile === null) {
            $composerFile = new ComposerFile();
        }
        $this->composerFile = $composerFile;

        if ($git === null) {
            $git = new Git();
        }
        $this->git = $git;
    }

    public function execute(): bool
    {
        if ($this->option('help') !== null) {
            $this->showHelp();

            return true;
        }
        if ($this->argument(0) === null) {
            $this->showHelp();
            return false;
        }

        $type = $this->argument(0);
        $file = $this->option('file', 'composer.json');

        $this->composerFile->parseFile($file);
        $version = $this->composerFile->getVersion();
        $version->increment($type);
        $this->composerFile->writeFile($file);

        $stringVersion = "v{$version}";

        $this->git->commitFile($file, $stringVersion);
        $this->git->tagVersion($stringVersion);

        return true;
    }

    public function showHelp()
    {
        echo <<<HELP
Usage: composer-version [options] <new-version> | major | minor | patch
    Options:
        -h, --help Show this help text
        -f, --file Path to composer.json file, default to ./composer.json
HELP;
    }

    protected function argument(int $index, $default = null)
    {
        return $this->arguments[$index] ?? $default;
    }

    protected function option(string $name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }

    public function parseOptions(array $argv): void
    {
        $rawOptions = getopt(implode('', array_keys(self::OPTIONS)), self::OPTIONS, $argumentIndex);
        $this->arguments = array_slice($argv, $argumentIndex);
        $this->options = [];
        foreach ($rawOptions as $short => $long) {
            $cleanShort = rtrim($short, ':');
            $cleanLong = rtrim($long, ':');
            $this->options[$cleanLong] = $rawOptions[$cleanShort] ?? $rawOptions[$cleanShort] ?? null;
        }
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }
}
