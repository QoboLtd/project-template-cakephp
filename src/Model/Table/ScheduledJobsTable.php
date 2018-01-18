<?php
namespace App\Model\Table;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Filesystem\Folder;
use Cake\I18n\Time;
use Cake\ORM\Table;
use Cake\Utility\Inflector;
use CsvMigrations\Event\EventName;
use DateTime;
use RRule\RfcParser;
use RRule\RRule;
use RuntimeException;

class ScheduledJobsTable extends AppTable
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('scheduled_jobs');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('Muffin/Trash.Trash');
    }

    /**
     * {@inheritDoc}
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $user = $this->getCurrentUser();

        if (empty($user['id'])) {
            return;
        }

        $entity->set('modified_by', $user['id']);
        if ($entity->isNew()) {
            $entity->set('created_by', $user['id']);
        }
    }

    /**
     * afterSave hook
     *
     * @param \Cake\Event\Event $event from the parent afterSave
     * @param \Cake\Datasource\EntityInterface $entity from the parent afterSave
     * @param \ArrayObject $options from the parent afterSave
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $options['current_user'] = $this->getCurrentUser();

        $ev = new Event(
            (string)EventName::MODEL_AFTER_SAVE(),
            $this,
            ['entity' => $entity, 'options' => $options]
        );

        EventManager::instance()->dispatch($ev);
    }

    /**
     * Get Activated Job records
     *
     * @return array $result containing record entities.
     */
    public function getActiveJobs()
    {
        $result = [];

        $query = $this->find()
            ->where(['active' => 1])
            ->order(['priority' => 'ASC']);

        $entities = $query->all();

        if (empty($entities)) {
            return $result;
        }

        $result = $entities;

        return $result;
    }

    /**
     * Get Job Instance
     *
     * Retrieve job object that can be run
     *
     * @param string $command from DB entity
     *
     * @return \App\ScheduledJobs\JobInterface $instance of the job.
     */
    public function getInstance($command = null, $type = null)
    {
        $instance = null;

        if (empty($command) || empty($type)) {
            return $instance;
        }

        $parts = explode('::', $command, 2);
        $handlerName = $parts[0];

        $dir = Inflector::camelize(Inflector::pluralize($type));
        $suffix = Inflector::camelize(Inflector::singularize($type));

        $path = dirname(dirname(dirname(__FILE__))) . DS . 'ScheduledJobs' . DS . $dir . DS;
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
     * @paam \Cake\I18n\Time $now system time
     * @param \RRule\RRule $rrule of the recurrence if any
     *
     * @return bool $state whether to run it or not.
     */
    public function timeToRun(Time $now, RRule $rrule)
    {
        $state = false;

        $dt = new DateTime($now->i18nFormat('yyyy-MM-dd HH:mm'), $now->timezone);

        if ($rrule->occursAt($dt)) {
            $state = true;
        }

        return $state;
    }

    /**
     * Get List of Existing Jobs
     *
     * Iterate through all Handlers and ask for jobs list
     *
     * @param array $options if any needed
     *
     * @return array $result of scripts for UI.
     */
    public function getList(array $options = [])
    {
        $result = $handlers = [];

        $namespace = 'App\\ScheduledJobs\\Handlers\\';
        $path = dirname(dirname(dirname(__FILE__))) . DS . 'ScheduledJobs' . DS . 'Handlers';

        $handlers = $this->scanDir($path);

        foreach ($handlers as $handlerName) {
            $class = $namespace . $handlerName;

            try {
                $object = new $class();

                $result = array_merge($result, $object->getList());
            } catch (RuntimeException $e) {
                pr($e->getMessage());
            }
        }

        $result = array_flip($result);

        foreach ($result as $command => $caption) {
            $result[$command] = $command;
        }

        return $result;
    }

    /**
     * List Handlers in the directory
     *
     * @param string $path of the directory
     */
    protected function scanDir($path)
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
    protected function isValidFile($file = null)
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
     * @return \RRule\RRule $rrule to be used
     */
    public function getRRule(EntityInterface $entity)
    {
        $rrule = null;

        if (empty($entity->recurrence)) {
            return $rrule;
        }

        $stdate = $entity->start_date;

        if (empty($stdate)) {
            $config = RfcParser::parseRRule($entity->recurrence);
        } else {
            // @NOTE: using native DateTime objects within RRule.
            $stdate = new DateTime($stdate->i18nFormat('yyyy-MM-dd HH:mm'), $stdate->timezone);
            $config = RfcParser::parseRRule($entity->recurrence, $stdate);
        }

        $rrule = new RRule($config);

        return $rrule;
    }
}
