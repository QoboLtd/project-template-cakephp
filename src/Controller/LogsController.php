<?php

namespace App\Controller;

use App\Controller\Traits\SearchTrait;
use Cake\Core\Configure;
use Cake\I18n\Time;
use DatabaseLog\Controller\Admin\LogsController as BaseLogsController;

class LogsController extends BaseLogsController
{
    use SearchTrait;

    public $modelClass = 'Logs';

    /**
     * Setup pagination
     *
     * @var array
     */
    public $paginate = [
        'order' => ['DatabaseLogs.id' => 'DESC'],
        'fields' => [
            'DatabaseLogs.created',
            'DatabaseLogs.type',
            'DatabaseLogs.message',
            'DatabaseLogs.id'
        ]
    ];

    /**
     * Initialization hook method.
     *
     * Implement this method to avoid having to overwrite
     * the constructor and call parent.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadModel('DatabaseLog.DatabaseLogs');

        $this->paginate['limit'] = 10;
        $this->paginate['fields'] = null;
    }

    /**
     * Delete log records older than specified time (maxLength).
     *
     * This is identical to `./bin/cake database_logs gc` functionality.
     *
     * @return \Cake\Http\Response|void|null
     */
    public function gc()
    {
        $this->request->allowMethod('post');

        $age = Configure::read('DatabaseLog.maxLength');
        if (!$age) {
            $this->Flash->error("Max age is not configured.");

            return $this->redirect(['action' => 'index']);
        }

        $date = new Time($age);
        $count = $this->DatabaseLogs->deleteAll(['created <' => $date]);

        $this->Flash->success((string)__d('Qobo/ProjectTemplateCakephp', 'Removed {0} log records older than {1}.', number_format($count), ltrim($age, '-')));

        return $this->redirect(['action' => 'index']);
    }
}
