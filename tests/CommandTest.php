<?php
/**
 * Created by PhpStorm.
 * User: hugo
 * Date: 17-09-08
 * Time: 19:18
 */

namespace Tests;

use Korri\ComposerVersion\Command;
use Korri\ComposerVersion\ComposerFile;
use Korri\ComposerVersion\Version;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
{
    private function initCommand($options = [], $arguments = [], $mockFile = null)
    {
        $command = new Command($mockFile);

        $command->setArguments($arguments);
        $command->setOptions($options);

        return $command;
    }

    private function initComposerFileMock($expectedFileParsed)
    {
        $version = new Version();

        $mockComposerFile = $this->createMock(ComposerFile::class);
        $mockComposerFile->expects($this->once())
            ->method('parseFile')
            ->with($this->equalTo($expectedFileParsed));
        $mockComposerFile->expects($this->once())
            ->method('getVersion')
            ->willReturn($version);
        $mockComposerFile->expects($this->once())
            ->method('writeFile')
            ->willReturn(true);

        return $mockComposerFile;
    }

    public function testBasicCommandShowsHelpAndReturnsFalse()
    {
        $this->expectOutputRegex('/^Usage:/');

        $command = $this->initCommand();

        $this->assertFalse($command->execute());
    }

    public function testHelpOptionShowsHelpAndReturnsTrue()
    {
        $this->expectOutputRegex('/^Usage:/');

        $command = $this->initCommand(['help' => false]);

        $this->assertTrue($command->execute());
    }

    public function testBasicVersionIncrement()
    {
        $mockComposerFile = $this->initComposerFileMock('composer.json');

        $command = $this->initCommand([], ['minor'], $mockComposerFile);

        $this->assertTrue($command->execute());
    }

    public function testFileOption()
    {
        $mockComposerFile = $this->initComposerFileMock('test.json');

        $command = $this->initCommand(['file' => 'test.json'], ['minor'], $mockComposerFile);

        $this->assertTrue($command->execute());
    }
}
