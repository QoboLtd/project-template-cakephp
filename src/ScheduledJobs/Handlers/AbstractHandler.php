<?php
namespace App\ScheduledJobs\Handlers;

abstract class AbstractHandler
{
    /**
     * Get Jobs list
     *
     * Depending on the type/location of job sources
     * we execute the listing of them differently.
     *
     * @param array $options with extra configs
     *
     * @return array $result containing list of jobs
     */
    abstract public function getList(array $options = []);
}
