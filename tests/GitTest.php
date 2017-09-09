<?php
/**
 * Created by PhpStorm.
 * User: hugo
 * Date: 17-09-08
 * Time: 21:18
 */

namespace Tests;


use Korri\ComposerVersion\Git;
use PHPUnit\Framework\TestCase;

class GitTest extends TestCase
{
    /**
     * @param $expectedCommand
     * @param bool $return
     * @return \PHPUnit_Framework_MockObject_MockObject|Git
     */
    private function mockGit($expectedCommand, $return = true)
    {
        $git = $this->createPartialMock(Git::class, ['exec']);
        $git->expects($this->once())
            ->method('exec')
            ->with($this->equalTo($expectedCommand))
            ->willReturn($return);

        return $git;
    }

    public function testCommitFile()
    {
        $git = $this->mockGit("commit 'composer.json' -m 'v1.1.1'");

        $this->assertTrue($git->commitFile('composer.json', 'v1.1.1'));
    }

    public function testTag()
    {
        $git = $this->mockGit("tag 'v1.1.1'");

        $this->assertTrue($git->tagVersion('v1.1.1'));
    }

    public function testPush()
    {
        $git = $this->mockGit('push');

        $this->assertTrue($git->push());
    }

    public function testPushTag()
    {
        $git = $this->mockGit("push origin 'v1.1.1'");

        $this->assertTrue($git->push('v1.1.1'));
    }
}