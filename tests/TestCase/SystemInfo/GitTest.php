<?php

namespace App\Test\TestCase\SystemInfo;

use App\SystemInfo\Git;
use PHPUnit\Framework\TestCase;

class GitTest extends TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testGetCommandException(): void
    {
        Git::getCommand('this command is not defined');
    }

    public function testGetCommand(): void
    {
        $commands = [
            'localChanges',
            'currentHash',
        ];

        foreach ($commands as $command) {
            $result = Git::getCommand($command);
            $this->assertTrue(is_string($result), "getCommand() returned a non-string result for '$command' command");
            $this->assertFalse(empty($result), "getCommand() returned an empty result for '$command' command");
        }
    }

    public function testGetLocalChanges(): void
    {
        $result = Git::getLocalChanges();
        $this->assertTrue(is_array($result), "getLocalChanges() returned a non-array result");
    }

    public function testGetCurrentHash(): void
    {
        $result = Git::getCurrentHash();
        $this->assertTrue(is_string($result), "getCurrentHash() returned a non-string result");
        $this->assertFalse(empty($result), "getCurrentHash() returned an empty result");
    }
}
