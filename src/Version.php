<?php

namespace Korri\ComposerVersion;

class Version
{
    /** @var array */
    protected $parts = [1, 0, 0];

    const TYPES = [
        'major' => 0,
        'minor' => 1,
        'patch' => 2,
    ];

    public function __construct(string $version = null)
    {
        if ($version !== null) {
            $this->parse($version);
        }
    }

    public function parse(string $versionString)
    {
        $matches = preg_match('/^v?(\d+)\.(\d+).(\d+)(?:-(dev|(?:p|patch|a|alpha|b|beta|RC))(\d+)?)?$/', $versionString, $parts);
        if (!$matches || isset($parts[4]) && $parts[4] === 'dev' && isset($parts[5])) {
            throw new \InvalidArgumentException('Invalid version string: ' . $versionString . ', see https://getcomposer.org/doc/04-schema.md#version');
        }
        $this->parts = array_slice($parts, 1);
    }

    public function getParts(): array
    {
        return $this->parts;
    }

    public function setParts(array $parts): void
    {
        $this->parts = $parts;
    }

    public function __toString(): string
    {
        $string = implode('.', array_slice($this->parts, 0, 3));
        if (isset($this->parts[3])) {
            $string .= '-' . implode('', array_slice($this->parts, 3));
        }
        return $string;
    }

    public function increment($type)
    {
        if (!isset(self::TYPES[$type])) {
            $this->parse($type);
            return;
        }
        $index = self::TYPES[$type];

        $this->parts = array_slice($this->parts, 0, 3);

        for ($i = 2; $i > $index; $i--) {
            $this->parts[$i] = 0;
        }

        $this->parts[$i]++;
    }
}