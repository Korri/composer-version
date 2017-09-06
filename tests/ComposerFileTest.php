<?php

namespace Tests;

use Korri\ComposerVersion\ComposerFile;
use PHPUnit\Framework\TestCase;

class ComposerFileTest extends TestCase
{
    public function testFromFile()
    {
        $composer = ComposerFile::fromFile(__DIR__ . '/json_samples/basic.json');

        $this->assertEquals('1.1.1', $composer->getVersion()->__toString());
    }

    public function testFromString()
    {
        $composer = ComposerFile::fromString('{ "version": "1.1.1" }');

        $this->assertEquals('1.1.1', $composer->getVersion()->__toString());
    }

    public function testToString()
    {
        $composer = ComposerFile::fromFile(__DIR__ . '/json_samples/basic.json');

        $this->assertStringEqualsFile(__DIR__ . '/json_samples/basic.json', $composer->__toString());
    }

    public function testToStringUpdatesVersion()
    {
        $composer = ComposerFile::fromFile(__DIR__ . '/json_samples/basic.json');

        $composer->getVersion()->increment('major');

        $this->assertStringEqualsFile(__DIR__ . '/json_samples/basic_v2.json', $composer->__toString());
    }
}
