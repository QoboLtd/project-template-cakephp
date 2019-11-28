<?php

namespace App\Controller;

use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\I18n\Time;
use Cake\Log\Log;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validation;

/**
 * ScheduledJobs Controller
 *
 */
class ScheduledJobLogsController extends BaseModuleController
{
    /**
     * Index method
     *
     * Returns a a list of scheduled jobs
     *
     * @return \Cake\Http\Response|void|null
     */
    public function index()
    {
    }

    /**
     * View method
     *
     * @param string $id Entity id.
     * @return \Cake\Http\Response|void|null
     * @throws \Cake\Http\Exception\NotFoundException When record not found.
     */
    public function view(string $id)
    {
        /**
         * @var \Cake\ORM\Table $table
         */
        $table = $this->loadModel();
        /**
         * @var string
         */
        $primaryKey = $table->getPrimaryKey();
        $entity = $table->find()
            ->where([$primaryKey => $id])
            ->first();

        if (empty($entity) && ! Validation::uuid($id)) {
            $entity = $table->find()
                ->applyOptions(['lookup' => true, 'value' => $id])
                ->firstOrFail();
        }

        if (empty($entity)) {
            throw new RecordNotFoundException(sprintf(
                'Record not found in table "%s"',
                $table->getTable()
            ));
        }

        $this->set('entity', $entity);
        $this->set('_serialize', ['entity']);
    }

    /**
     * Delete log records older than specified time (maxLength).
     *
     * This is identical to `./bin/cake database_logs gc` functionality.
     *
     * @return \Cake\Http\Response|null|void
     */
    public function gc()
    {
        $this->request->allowMethod('post');

        $age = Configure::read('ScheduledLog.stats.age');
        if (!$age) {
            $this->Flash->error((string)__('Max age is not configured.'));

            return $this->redirect(['controller' => 'ScheduledJobs', 'action' => 'index']);
        }

        $date = new Time($age);
        $query = TableRegistry::get('ScheduledJobLogs');
        // Count how many has been deleted
        $count = $query->deleteAll(['created <' => $date]);
        // Write in the Log
        Log::write('info', "Clean up scheduled job logs older then $age.");

        $this->Flash->success((string)__('Removed {0} log records older than {1}.', number_format($count), ltrim($age, '-')));

        return $this->redirect(['controller' => 'ScheduledJobs', 'action' => 'index']);
    }
}
