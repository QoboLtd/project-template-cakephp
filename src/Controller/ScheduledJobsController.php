<?php
namespace App\Controller;

use CsvMigrations\Controller\AppController as BaseController;

/**
 * ScheduledJobs Controller
 *
 */
class ScheduledJobsController extends BaseController
{
    /**
     * {@inheritDoc}
     */
    public function index()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function add()
    {
        $model = $this->{$this->name};
        $entity = $model->newEntity();

        $commands = $model->getList();

        if ($this->request->is(['post', 'put'])) {
            $entity = $model->patchEntity($entity, $this->request->getData());

            if ($model->save($entity)) {
                $this->Flash->success(__('Scheduled Job has been saved.'));

                return $this->redirect(['action' => 'index']);
            }

            $this->Flash->error(__('Scheduled Job could not be saved. Please, try again'));
        }

        $this->set(compact('entity', 'commands'));
        $this->set('_serialize', ['entity', 'commands']);
    }

    /**
     * {@inheritDoc}
     */
    public function edit($id = null)
    {
        $model = $this->{$this->name};
        $entity = $model->get($id, [
            'contain' => [],
        ]);

        $commands = $model->getList();

        if ($this->request->is(['patch', 'post', 'put'])) {
            if ($this->request->data('btn_operation') == 'cancel') {
                return $this->redirect(['action' => 'view', $id]);
            }

            $entity = $model->patchEntity($entity, $this->request->getData());

            $saved = $model->save($entity);

            if ($saved) {
                $this->Flash->success(__('The record has been saved.'));
                $redirectUrl = $model->getParentRedirectUrl($model, $entity);

                if (empty($redirectUrl)) {
                    return $this->redirect(['action' => 'view', $entity->{$model->primaryKey()}]);
                } else {
                    return $this->redirect($redirectUrl);
                }
            } else {
                $this->Flash->error(__('This record could not be saved.'));
            }
        }

        $this->set(compact('entity', 'commands'));
        $this->set('_serialize', ['entity', 'commands']);
    }
}
