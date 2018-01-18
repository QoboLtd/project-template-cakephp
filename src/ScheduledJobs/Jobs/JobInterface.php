<?php
namespace App\ScheduledJobs\Jobs;

interface JobInterface
{
    /**
     * Run job command
     *
     * Accepts arguments saved in the DB instance
     * to execute the script/api call, etc.
     *
     * @param mixed $arguments to be passed in the execution stage.
     *
     * @return array $result containing response state and output.
     */
    public function run($arguments = null);
}
