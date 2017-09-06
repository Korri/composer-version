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

    const JSON_FLAGS = JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

    /**
     * ComposerFile constructor.
     * @param array $json
     */
    public function __construct(array $json)
    {
        $this->data = $json;
    }

    public static function fromFile(string $path): ComposerFile
    {
        return static::fromString(file_get_contents($path));
    }

    public static function fromString(string $json): ComposerFile
    {
        return new static(json_decode($json, true));
    }

    public function getVersion(): Version
    {
        if (!$this->version) {
            $version = $this->data['version'] ?? '1.0.0';

            $this->version = Version::fromString($version);
        }

        return $this->version;
    }

    public function getData(): array
    {
        return array_merge($this->data, [
            'version' => $this->getVersion()->__toString()
        ]);
    }

    public function __toString(): string
    {
        return json_encode($this->getData(), static::JSON_FLAGS) . "\n";
    }
}