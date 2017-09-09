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
use Korri\ComposerVersion\Git;
use Korri\ComposerVersion\Version;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
{
    private function initCommand($options = [], $arguments = [], $mockFile = null, $mockGit = null)
    {
        $mockFile = $mockFile ?? $this->initComposerFileMock();
        $mockGit = $mockGit ?? $this->initGitMock();

        $command = new Command($mockFile, $mockGit);

        $command->setArguments($arguments);
        $command->setOptions($options);

        return $command;
    }

    /**
     * @param string|null $expectedFileParsed
     * @return \PHPUnit_Framework_MockObject_MockObject|ComposerFile
     */
    private function initComposerFileMock(string $expectedFileParsed = null)
    {
        $version = new Version('1.1.1');

        $mockComposerFile = $this->createMock(ComposerFile::class);
        if ($expectedFileParsed) {
            $mockComposerFile->expects($this->once())
                ->method('parseFile')
                ->with($this->equalTo($expectedFileParsed));
            $mockComposerFile->expects($this->once())
                ->method('getVersion')
                ->willReturn($version);
            $mockComposerFile->expects($this->once())
                ->method('writeFile')
                ->willReturn(true);
        }

        return $mockComposerFile;
    }

    /**
     * @param array $expectedCommands
     * @return \PHPUnit_Framework_MockObject_MockObject|Git
     */
    private function initGitMock(array $expectedCommands = [])
    {
        $mockGit = $this->createPartialMock(Git::class, ['exec']);

        foreach ($expectedCommands as $k => $command) {
            $mockGit->expects($this->at($k))
                ->method('exec')
                ->with($command)
                ->willReturn(true);
        }

        return $mockGit;
    }

    /** @expectedException \InvalidArgumentException */
    public function testBasicCommandShowsHelpAndFails()
    {
        $command = $this->initCommand();

        $command->execute();
    }

    /** @expectedException \InvalidArgumentException */
    public function testToManyArgumentsShowsUsageAndFails()
    {
        $command = $this->initCommand([], ['test.json', '--option-after-argument']);

        $command->execute();
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
        $mockGit = $this->initGitMock([
            "commit 'composer.json' -m 'v1.2.0'",
            "tag 'v1.2.0'"
        ]);

        $command = $this->initCommand([], ['minor'], $mockComposerFile, $mockGit);

        $this->assertTrue($command->execute());
    }

    public function testSpecificVersion()
    {
        $mockComposerFile = $this->initComposerFileMock('composer.json');
        $mockGit = $this->initGitMock([
            "commit 'composer.json' -m 'v0.1.1'",
            "tag 'v0.1.1'"
        ]);

        $command = $this->initCommand([], ['0.1.1'], $mockComposerFile, $mockGit);

        $this->assertTrue($command->execute());
    }

    public function testFileOption()
    {
        $mockComposerFile = $this->initComposerFileMock('test.json');
        $mockGit = $this->initGitMock([
            "commit 'test.json' -m 'v1.1.2'",
            "tag 'v1.1.2'"
        ]);

        $command = $this->initCommand(['file' => 'test.json'], ['patch'], $mockComposerFile, $mockGit);

        $this->assertTrue($command->execute());
    }

    public function testPushOption()
    {
        $mockComposerFile = $this->initComposerFileMock('test.json');
        $mockGit = $this->initGitMock([
            "commit 'test.json' -m 'v2.0.0'",
            "tag 'v2.0.0'",
            "push",
            "push origin 'v2.0.0'"
        ]);

        $command = $this->initCommand(['file' => 'test.json', 'push' => false], ['major'], $mockComposerFile, $mockGit);

        $this->assertTrue($command->execute());
    }
}
