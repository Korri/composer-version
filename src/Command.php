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
    protected $arguments;

    /**
     * Options passed to the command
     * @var
     */
    protected $options;

    public function __construct(array $arguments)
    {
        $this->arguments = $arguments;
    }

    public function execute(): void
    {
        if ($this->option('help') !== null) {
            $this->showHelp();

            return;
        }
        if ($this->argument(0) === null) {
            $this->showHelp();
            exit(1);
        }

        $type = $this->argument(0);
        $file = $this->option('file', './composer.json');
        $dir = $this->option('dir', dirname($file));

        $composerFile = ComposerFile::fromFile($file);
        $composerFile->getVersion()->increment($type);

        file_put_contents($file, $composerFile);
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

    protected function argument(int $index, $default = null): string
    {
        if ($this->options === null) {
            $this->parseParameters();
        }

        return $this->arguments[ $index ] ?? $default;
    }

    protected function option(string $name, $default = null)
    {
        if ($this->options === null) {
            $this->parseParameters();
        }

        return $this->options[ $name ] ?? $default;
    }

    protected function parseParameters(): void
    {
        $rawOptions = getopt(implode('', array_keys(self::OPTIONS)), self::OPTIONS, $argumentIndex);
        $this->arguments = array_slice($this->arguments, $argumentIndex);
        $this->options = [];
        foreach ($rawOptions as $short => $long) {
            $cleanShort = rtrim($short, ':');
            $cleanLong = rtrim($long, ':');
            $this->options[ $cleanLong ] = $rawOptions[ $cleanShort ] ?? $rawOptions[ $cleanShort ] ?? null;
        }
    }
}
