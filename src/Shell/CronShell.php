<?php
namespace App\Shell;

use Cake\Console\Shell;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use DateTime;

/**
 * Cron shell command.
 */
class CronShell extends Shell
{

    public $tasks = ['Lock'];

    /**
     * Manage the available sub-commands along with their arguments and help
     *
     * @see http://book.cakephp.org/3.0/en/console-and-shells.html#configuring-options-and-generating-help
     *
     * @return \Cake\Console\ConsoleOptionParser
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        return $parser;
    }

    /**
     * main() method.
     *
     * @return bool|int|null Success or error code.
     */
    public function main()
    {
        $this->info('Running cron...');
        $this->ScheduledJobs = TableRegistry::get('ScheduledJobs');

        $lock = $this->Lock->lock(__FILE__, __CLASS__);

        $jobs = $this->ScheduledJobs->getActiveJobs();

        if (empty($jobs)) {
            $lock->unlock();

            return $this->out('No jobs found. Exiting...');
        }

        $now = Time::now();

        foreach ($jobs as $entity) {
            $instance = $this->ScheduledJobs->getInstance($entity->job, 'Job');

            if (!$instance) {
                continue;
            }

            $rrule = $this->ScheduledJobs->getRRule($entity);

            if ($this->ScheduledJobs->timeToRun($now, $rrule)) {
                $state = $instance->run($entity->options);

                // @TODO: saving state response of shell execution.
            }
        }

        $lock->unlock();
        $this->out('Lock released. Exiting...');
    }
}
