<?php

namespace Korri\ComposerVersion;

class Command
{
    const OPTIONS = [
        'f:' => 'file:',
        'h' => 'help',
        'd:' => 'dir:',
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

    public function __construct(ComposerFile $composerFile = null)
    {
        if ($composerFile === null) {
            $composerFile = new ComposerFile();
        }
        $this->composerFile = $composerFile;
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
        $dir = $this->option('dir', dirname($file));

        $this->composerFile->parseFile($file);
        $this->composerFile->getVersion()->increment($type);
        $this->composerFile->writeFile($file);

        return true;
    }

    public function showHelp()
    {
        echo <<<HELP
Usage: composer-version [options] <new-version> | major | minor | patch
    Options:
        -h, --help Show this help text
        -f, --file Path to composer.json file, default to current folder
        -d, --dir  Path to git repository, default to composer.json's folder
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
