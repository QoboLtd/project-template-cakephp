<?php
namespace App\ScheduledJobs\Jobs;

use App\ScheduledJobs\Jobs\JobInterface;

class CakeShellJob implements JobInterface
{
    protected $operator = './bin/cake';

    protected $command = '';

    protected $arguments = '';

    /**
     * Default construct
     *
     * @param string $command for the scripts.
     */
    public function __construct($command = '')
    {
        $this->command = $command;
    }

    /**
     * {@inheritDoc}
     */
    public function run($arguments = null)
    {
        $this->arguments = $arguments;
        $parts = $this->parseCommand();

        $command = $this->operator . ' ' . implode(' ', $parts) . ' ' . $this->arguments;

        exec($command, $output, $state);

        $result = [
            'state' => ($state > 0) ? false : true,
            'response' => $output,
        ];

        return $result;
    }

    /**
     * Parsing Command string into script
     *
     * @return array $shell containing required command parts to be used.
     */
    protected function parseCommand()
    {
        $shell = [];
        $parts = explode('::', $this->command, 2);

        // cutting off App prefix as it's not used anywhere.
        if (preg_match('/^(.*)\:(.*)/', $parts[1], $matches)) {
            $shell[] = $matches[2];
        }

        return $shell;
    }
}
