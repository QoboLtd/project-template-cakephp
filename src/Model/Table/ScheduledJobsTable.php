<?php

namespace App\Model\Table;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Event\Event;
use Cake\Filesystem\Folder;
use Cake\I18n\Time;
use Cake\Utility\Inflector;
use DateTime;
use RRule\RfcParser;
use RRule\RRule;

class ScheduledJobsTable extends AppTable
{
    public const JOB_ACTIVE = 1;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('scheduled_jobs');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');
    }

    /**
     * Before Save callback
     *
     * Triggered before saving Entity
     * to save modified/created by user id
     *
     * @param \Cake\Event\Event $event broadcasted
     * @param \Cake\Datasource\EntityInterface $entity to be saved
     * @param \ArrayObject $options passed extras
     *
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options): void
    {
        $entity->set('start_date', $this->getStartDate($entity->get('start_date')));
    }

    /**
     * Find Scheduled Jobs base on its `active` state
     *
     * @param int $state of the instance
     *
     * @return \Cake\Datasource\ResultSetInterface $result entities
     */
    public function getJobs(int $state = self::JOB_ACTIVE): ResultSetInterface
    {
        $state = (bool)$state;

        $query = $this->find()
            ->where(['active' => $state])
            ->order(['priority' => 'ASC']);

        $result = $query->all();

        return $result;
    }

    /**
     * Get Job Instance
     *
     * Retrieve Handler/Job Object for the job.
     *
     * @param string|null $command from DB entity
     * @param string|null $type Job type
     *
     * @return mixed $instance of the job.
     */
    public function getInstance(?string $command, ?string $type)
    {
        $instance = null;

        if (empty($command) || empty($type)) {
            return $instance;
        }

        $parts = explode('::', $command, 2);
        $handlerName = $parts[0];

        $dir = Inflector::camelize(Inflector::pluralize($type));
        $suffix = Inflector::camelize(Inflector::singularize($type));

        $path = APP . 'ScheduledJobs' . DS . $dir . DS;
        $path = $path . $handlerName . $suffix . '.php';

        if (file_exists($path)) {
            $class = 'App\\ScheduledJobs\\' . $dir . '\\' . $handlerName . $suffix;
            $instance = new $class($command);
        }

        return $instance;
    }

    /**
     * Is Time To Run the command
     *
     * @param \Cake\I18n\Time $now system time
     * @param \RRule\RRule $rrule rule of the recurrence if any
     *
     * @return bool $state whether to run it or not.
     */
    public function timeToInvoke(Time $now, RRule $rrule): bool
    {
        $state = false;

        /**
         * @var string
         */
        $tick = $now->i18nFormat('yyyy-MM-dd HH:mm');

        $dtNow = new DateTime($tick, $now->getTimezone());

        if ($rrule->occursAt($dtNow)) {
            $state = true;
        }

        return $state;
    }

    /**
     * Get List of Existing Jobs
     *
     * Iterate through all Handlers and ask for jobs list
     *
     * @param mixed[] $options if any needed
     *
     * @return mixed[] $result of scripts for UI.
     */
    public function getList(array $options = []): array
    {
        $result = $handlers = [];

        $namespace = 'App\\ScheduledJobs\\Handlers\\';
        $path = APP . 'ScheduledJobs' . DS . 'Handlers';

        $handlers = $this->scanDir($path);

        foreach ($handlers as $handlerName) {
            $class = $namespace . $handlerName;

            if (! class_exists($class)) {
                continue;
            }

            $object = new $class();
            $result = array_merge($result, $object->getList());
        }

        $commands = array_keys(array_flip($result));
        $result = [];

        foreach ($commands as $command) {
            $result[$command] = $command;
        }

        return $result;
    }

    /**
     * List Handlers in the directory
     *
     * @param string $path of the directory
     *
     * @return string[]
     */
    protected function scanDir(string $path): array
    {
        $result = [];
        $dir = new Folder($path);
        $contents = $dir->read(true, true);

        if (empty($contents[1])) {
            return $result;
        }

        foreach ($contents[1] as $file) {
            if (!$this->isValidFile($file)) {
                continue;
            }

            $result[] = substr($file, 0, -4);
        }

        return $result;
    }

    /**
     * Is given file valid for being listed
     *
     * @param string $file string
     *
     * @return bool $valid result check.
     */
    public function isValidFile(string $file): bool
    {
        $valid = true;

        if (substr($file, -4) !== '.php') {
            $valid = false;
        }

        if (preg_match('/^Abstract/', $file)) {
            $valid = false;
        }

        return $valid;
    }

    /**
     * Get RRule object based on entity
     *
     * @param \Cake\Datasource\EntityInterface $entity of the job
     *
     * @return \RRule\RRule|null $rrule to be used
     */
    public function getRRule(EntityInterface $entity): ?RRule
    {
        $rrule = null;
        $recurrence = $entity->get('recurrence');

        if (empty($recurrence)) {
            return $rrule;
        }

        $stdate = $entity->get('start_date');

        if (empty($stdate)) {
            $config = RfcParser::parseRRule($recurrence);
        } else {
            // @NOTE: using native DateTime objects within RRule.
            $stdate = new DateTime($stdate->i18nFormat('yyyy-MM-dd HH:mm'), $stdate->timezone);
            $config = RfcParser::parseRRule($recurrence, $stdate);
        }

        $rrule = new RRule($config);

        return $rrule;
    }

    /**
     * Get Start Date right
     *
     * Avoid using seconds in case it might mismatch timeToInvoke() method
     *
     * @param \Cake\I18n\Time|string $time of the entity
     *
     * @return \Cake\I18n\Time with zero-value seconds.
     */
    public function getStartDate($time): Time
    {
        if ($time instanceof Time) {
            return $time->second(0);
        }

        if (is_string($time)) {
            return Time::parse($time)->second(0);
        }
    }
}
