<?php

namespace Korri\ComposerVersion;

class ComposerFile
{
    /** @var string */
    protected $path;

    /** @var Version */
    protected $version;

    /** @var \stdClass */
    protected $data;

    /** @var string */
    protected $indent;

    const JSON_FLAGS = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

    public function __construct(array $json = null, string $indent = '    ')
    {
        $this->data = $json;
        $this->indent = $indent;
    }

    public function parseFile(string $path): ComposerFile
    {
        if (!file_exists($path) || !is_readable($path)) {
            throw new \InvalidArgumentException('Could not read file ' . $path);
        }

        return $this->parseString(file_get_contents($path));
    }

    public function writeFile(string $path): bool
    {
        return (bool)file_put_contents($path, $this->__toString());
    }

    public function parseString(string $json): ComposerFile
    {
        $this->detectIndentation($json);
        $this->data = json_decode($json, true);

        return $this;
    }

    public function getVersion(): Version
    {
        if (!$this->version) {
            $version = $this->data['version'] ?? '1.0.0';

            $this->version = new Version($version);
        }

        return $this->version;
    }

    public function getData(): array
    {
        $data = $this->data;

        return $this->addVersionToArray($data, $this->getVersion()->__toString());
    }

    public function __toString(): string
    {
        $json = json_encode($this->getData(), static::JSON_FLAGS) . "\n";

        $json = str_replace('    ', $this->indent, $json);

        return $json;
    }

    private function addVersionToArray($data, string $version): array
    {
        if (array_key_exists('version', $data)) {
            $data['version'] = $version;
            return $data;
        }

        $afterKeys = ['name', 'description'];
        $max = 0;
        $i = 0;
        foreach ($data as $key => $value) {
            $i += 1;
            if (in_array($key, $afterKeys)) {
                $max = $i;
            }
        }

        return array_slice($data, 0, $max, true)
            + ['version' => $version]
            + array_slice($data, $max, count($data) - $max, true);
    }

    private function detectIndentation($string)
    {
        if (preg_match('/^(\s+)"/m', $string, $matches)) {
            $this->indent = $matches[1];
        }
    }
}