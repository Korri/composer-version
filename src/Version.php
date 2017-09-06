<?php

namespace Korri\ComposerVersion;

class Version
{
    /** @var array */
    protected $parts;

    const TYPES = [
        'major' => 0,
        'minor' => 1,
        'patch' => 2,
    ];

    public function __construct(array $parts)
    {
        $this->parts = $parts;
    }

    public static function fromString($versionString)
    {
        $matches = preg_match('/^v?(\d+)\.(\d+).(\d+)(?:-(dev|(?:p|patch|a|alpha|b|beta|RC))(\d+)?)?$/', $versionString, $parts);
        if (!$matches || isset($parts[4]) && $parts[4] === 'dev' && isset($parts[5])) {
            throw new \InvalidArgumentException('Invalid version string: ' . $versionString . ', see https://getcomposer.org/doc/04-schema.md#version');
        }
        $parts = array_slice($parts, 1);
        return new static($parts);
    }

    public function getParts(): array
    {
        return $this->parts;
    }

    public function __toString(): string
    {
        $string = implode('.', array_slice($this->parts, 0, 3));
        if (isset($this->parts[3])) {
            $string .= '-' . implode('', array_slice($this->parts, 3));
        }
        return $string;
    }

    public function increment($type): Version
    {
        if (!isset(self::TYPES[$type])) {
            throw new \InvalidArgumentException('Invalid increment type: ' . $type);
        }
        $index = self::TYPES[$type];

        $this->parts = array_slice($this->parts, 0, 3);

        for ($i = 2; $i > $index; $i--) {
            $this->parts[$i] = 0;
        }

        $this->parts[$i]++;

        return $this;
    }
}