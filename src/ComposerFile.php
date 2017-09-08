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

    /**
     * ComposerFile constructor.
     * @param array $json
     */
    public function __construct(array $json, string $indent = '    ')
    {
        $this->data = $json;
        $this->indent = $indent;
    }

    public static function fromFile(string $path): ComposerFile
    {
        return static::fromString(file_get_contents($path));
    }

    public static function fromString(string $json): ComposerFile
    {
        $indent = self::detectIndentation($json);
        return new static(json_decode($json, true), $indent);
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

    private static function detectIndentation($string)
    {
        if (preg_match('/^(\s+)"/m', $string, $matches)) {
            return $matches[1];
        }
        return '    ';
    }
}