<?php

namespace Tests;

use Korri\ComposerVersion\ComposerFile;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamWrapper;
use PHPUnit\Framework\TestCase;

class ComposerFileTest extends TestCase
{
    const SAMPLE_DIR = __DIR__ . '/json_samples/';

    public function testParseString()
    {
        $composer = new ComposerFile();

        $composer->parseString('{ "version": "1.1.1" }');

        $this->assertEquals('1.1.1', $composer->getVersion()->__toString());
    }

    public function testParseFileLoadsVersionProperly()
    {
        $composer = new ComposerFile();

        $composer->parseFile(self::SAMPLE_DIR . '/basic/source.json');

        $this->assertEquals('1.1.1', $composer->getVersion()->__toString());
    }

    public function testDefaultsToVersionOne()
    {
        $composer = new ComposerFile();

        $composer->parseFile(self::SAMPLE_DIR . '/no-version/source.json');

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
        $composer = new ComposerFile();

        $composer->parseFile($folder . '/source.json');

        $composer->getVersion()->increment('major');

        $this->assertEquals('2.0.0', $composer->getVersion()->__toString());

        $this->assertStringEqualsFile(
            $folder . '/expected.json',
            $composer->__toString()
        );
    }

    public function testToStringShouldNotChangeAValidFile()
    {
        $composer = new ComposerFile();

        $composer = $composer->parseFile(self::SAMPLE_DIR . '/basic/source.json');

        $this->assertStringEqualsFile(
            self::SAMPLE_DIR . '/basic/source.json',
            $composer->__toString()
        );
    }

    public function testWriteFileShouldSaveFile()
    {

        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('test'));

        $json = <<<JSON
{
    "version": "1.1.1"
}

JSON;

        $composer = new ComposerFile();

        $composer->parseString($json);

        $composer->writeFile('test.json');

        $this->assertEquals($json, file_get_contents('test.json'));
    }
}
