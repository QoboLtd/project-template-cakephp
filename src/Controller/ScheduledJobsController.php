<?php

namespace App\Controller;

use App\Controller\BaseModuleController;

/**
 * ScheduledJobs Controller
 *
 */
class ScheduledJobsController extends BaseModuleController
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
     * Add Scheduled Job instance
     *
     * Saving executing Scheduled Job with RRule params
     *
     * @return \Cake\Http\Response|void|null
     */
    public function add()
    {
        /**
         * @var \App\Model\Table\ScheduledJobsTable $table
         */
        $table = $this->loadModel();
        $entity = $table->newEntity();

        $commands = $table->getList();

        if ($this->request->is(['post', 'put'])) {
            $entity = $table->patchEntity($entity, (array)$this->request->getData());

            if ($table->save($entity)) {
                $this->Flash->success((string)__d('Qobo/ProjectTemplateCakephp', 'Scheduled Job has been saved.'));

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error((string)__d('Qobo/ProjectTemplateCakephp', 'Scheduled Job could not be saved. Please, try again'));
        }

        $this->set(compact('entity', 'commands'));
        $this->set('_serialize', ['entity', 'commands']);
    }

    /**
     * Edit Scheduled Jobs record
     *
     * @param string $entityId of the scheduled job
     *
     * @return \Cake\Http\Response|void|null
     */
    public function edit(string $entityId)
    {
        /**
         * @var \App\Model\Table\ScheduledJobsTable $table
         */
        $table = $this->loadModel();
        $entity = $table->get($entityId, [
            'contain' => [],
        ]);

        $redirectUrl = ['action' => 'view', $entityId];
        $commands = $table->getList();

        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($this->request->getData('btn_operation') == 'cancel') {
                return $this->redirect($redirectUrl);
            }

            $entity = $table->patchEntity($entity, (array)$this->request->getData());
            $saved = $table->save($entity);

            if ($saved) {
                $this->Flash->success((string)__d('Qobo/ProjectTemplateCakephp', 'The record has been saved.'));
            } else {
                $this->Flash->error((string)__d('Qobo/ProjectTemplateCakephp', 'This record could not be saved.'));
            }

            return $this->redirect($redirectUrl);
        }

        $this->set(compact('entity', 'commands'));
        $this->set('_serialize', ['entity', 'commands']);
    }
}
