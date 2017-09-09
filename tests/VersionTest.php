<?php

namespace Tests;

use Korri\ComposerVersion\Version;
use PHPUnit\Framework\TestCase;

/**
 * Parse composer-valid packages
 * @see https://getcomposer.org/doc/04-schema.md#version
 */
class VersionTest extends TestCase
{
    public function validVersionsProvider(): array
    {
        return [
            '1.1.1' => ['1.1.1', ['1', '1', '1']],
            '41.21.99' => ['41.21.99', ['41', '21', '99']],
            '1.1.1-dev' => ['1.1.1-dev', ['1', '1', '1', 'dev']],
            '1.1.1-patch' => ['1.1.1-patch', ['1', '1', '1', 'patch']],
            '1.1.1-patch1' => ['1.1.1-patch1', ['1', '1', '1', 'patch', '1']],
            '1.1.1-p' => ['1.1.1-p', ['1', '1', '1', 'p']],
            '1.1.1-alpha' => ['1.1.1-alpha', ['1', '1', '1', 'alpha']],
            '1.1.1-alpha1' => ['1.1.1-alpha1', ['1', '1', '1', 'alpha', '1']],
            '1.1.1-a' => ['1.1.1-a', ['1', '1', '1', 'a']],
            '1.1.1-beta' => ['1.1.1-beta', ['1', '1', '1', 'beta']],
            '1.1.1-beta1' => ['1.1.1-beta1', ['1', '1', '1', 'beta', '1']],
            '1.1.1-b' => ['1.1.1-b', ['1', '1', '1', 'b']],
            '1.1.1-RC' => ['1.1.1-RC', ['1', '1', '1', 'RC']],
            '1.1.1-RC1' => ['1.1.1-RC1', ['1', '1', '1', 'RC', '1']],
        ];
    }

    /** @dataProvider validVersionsProvider */
    public function testValidVersionParsing(string $string, array $expected)
    {
        $version = new Version($string);
        $this->assertSame($expected, $version->getParts());
    }

    /** @dataProvider validVersionsProvider */
    public function testToString($expectedString, $parts)
    {
        $version = new Version();
        $version->setParts($parts);

        $this->assertSame($expectedString, $version->__toString());
    }

    public function invalidVersionsProvider(): array
    {
        return [
            ['1.1.1b'],
            ['1.1.1beta'],
            ['1.1.1-dev1'],
            ['1.1'],
            ['1'],
        ];
    }

    /**
     * @dataProvider invalidVersionsProvider
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidVersionParsing(string $string)
    {
        $version = new Version($string);
        $this->assertFalse(true, 'Parsed as ' . implode('.', $version->getParts()));
    }

    public function testIncrementMajor()
    {
        $version = new Version('1.1.1');
        $version->increment('major');

        $this->assertEquals('2.0.0', $version->__toString());
    }

    public function testIncrementMinor()
    {
        $version = new Version('1.1.1');
        $version->increment('minor');

        $this->assertEquals('1.2.0', $version->__toString());
    }

    public function testIncrementPatch()
    {
        $version = new Version('1.1.1');
        $version->increment('patch');

        $this->assertEquals('1.1.2', $version->__toString());
    }

    public function testIncrementFallsBackToParseIfInvalid()
    {
        $version = new Version('1.1.1');
        $version->increment('2.1.1');

        $this->assertEquals('2.1.1', $version->__toString());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testIncrementInvalidTypeThrowsException()
    {
        $version = new Version('1.1.1');
        $version->increment('millimeter');
    }

    public function testIncrementPatchWithSuffix()
    {
        $version = new Version('1.1.1-alpha2');
        $version->increment('patch');

        $this->assertEquals('1.1.2', $version->__toString());
    }
}