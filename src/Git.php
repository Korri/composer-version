<?php
/**
 * Created by PhpStorm.
 * User: hugo
 * Date: 17-09-08
 * Time: 21:15
 */

namespace Korri\ComposerVersion;


class Git
{
    /** @var string */
    protected $binary;

    public function __construct(string $binary = 'git')
    {
        $this->binary = $binary;
    }

    public function exec($command): bool
    {

        \exec($this->binary . ' ' . $command, $_, $return);

        return $return === 0;
    }

    public function commitFile(string $file, string $message): bool
    {
        return $this->exec('commit ' . escapeshellarg($file) . ' -m ' . escapeshellarg($message));
    }

    public function tagVersion(string $version): bool
    {
        return $this->exec('tag ' . escapeshellarg($version));
    }

    public function push(string $tag = null): bool
    {
        if ($tag !== null) {
            return $this->exec('push origin ' . escapeshellarg($tag));
        }
        return $this->exec('push');
    }
}