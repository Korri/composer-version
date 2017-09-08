<?php

namespace Tests;

use Korri\ComposerVersion\ComposerFile;
use PHPUnit\Framework\TestCase;

class ComposerFileTest extends TestCase
{
    const SAMPLE_DIR = __DIR__ . '/json_samples/';

    public function testFromString()
    {
        $composer = ComposerFile::fromString('{ "version": "1.1.1" }');

        $this->assertEquals('1.1.1', $composer->getVersion()->__toString());
    }

    public function testFromFileLoadsVersionProperly()
    {
        $composer = ComposerFile::fromFile(self::SAMPLE_DIR . '/basic/source.json');
        $this->assertEquals('1.1.1', $composer->getVersion()->__toString());
    }

    public function testDefaultsToVersionOne()
    {
        $composer = ComposerFile::fromFile(self::SAMPLE_DIR . '/no-version/source.json');
        $this->assertEquals('1.0.0', $composer->getVersion()->__toString());
    }

    public function indentationDataProvider()
    {

        $folders = scandir(self::SAMPLE_DIR);
        $folders = array_filter($folders, function ($folder) {
            return $folder[0] !== '.';
        });
        return array_combine($folders, array_map(function ($folder) {
            return [self::SAMPLE_DIR . $folder];
        }, $folders));
    }

    /** @dataProvider indentationDataProvider */
    public function testToStringKeepsIndentationIntact(string $folder)
    {
        $composer = ComposerFile::fromFile($folder . '/source.json');

        $composer->getVersion()->increment('major');

        $this->assertEquals('2.0.0', $composer->getVersion()->__toString());

        $this->assertStringEqualsFile(
            $folder . '/expected.json',
            $composer->__toString()
        );
    }

    public function testToStringShouldNotChangeAValidFile()
    {
        $composer = ComposerFile::fromFile(self::SAMPLE_DIR . '/basic/source.json');
        $this->assertStringEqualsFile(
            self::SAMPLE_DIR . '/basic/source.json',
            $composer->__toString()
        );
    }
}
