<?php

/**
 * Copyright (c) Qobo Ltd. (https://www.qobo.biz)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Qobo Ltd. (https://www.qobo.biz)
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

use App\Controller\AppController;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Http\Response;
use Cake\Log\Log;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\Validation\Validation;
use CsvMigrations\Controller\Traits\ImportTrait;
use CsvMigrations\Event\EventName;
use CsvMigrations\Exception\UnsupportedPrimaryKeyException;
use CsvMigrations\Table;
use CsvMigrations\Utility\Field;
use CsvMigrations\Utility\FileUpload;
use Exception;
use PDOException;
use Psr\Http\Message\ResponseInterface;
use Qobo\Utils\Utility\User;
use Webmozart\Assert\Assert;

class BaseModuleController extends AppController
{
    use ImportTrait;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('CsvView');
        // set current user
        if (property_exists($this, 'Auth')) {
            User::setCurrentUser((array)$this->Auth->user());
        }
    }

    /**
     * Called before the controller action. You can use this method to configure and customize components
     * or perform logic that needs to happen before each controller action.
     *
     * @param \Cake\Event\Event $event An Event instance
     * @return \Psr\Http\Message\ResponseInterface|void
     * @link http://book.cakephp.org/3.0/en/controllers.html#request-life-cycle-callbacks
     */
    public function beforeFilter(Event $event)
    {
        $result = parent::beforeFilter($event);
        if ($result instanceof ResponseInterface) {
            return $result;
        }
    }

    /**
     * View method
     *
     * @param string $id Entity id.
     * @return \Cake\Http\Response|void|null
     */
    public function view(string $id)
    {
        $entity = $this->fetchEntity($id);

        $this->set('entity', $entity);
        $this->render('/Module/view');
        $this->set('_serialize', ['entity']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|void|null
     */
    public function add()
    {
        $table = $this->loadModel();

        $entity = $table->newEntity();

        if ($this->request->is('post')) {
            $post_data = $this->request->getParam(
                'data',
                (array)$this->request->getData()
            );

            $response = $this->persistEntity($entity, $post_data);
            if ($response) {
                $this->saveAssociations($entity, $post_data);

                return $response;
            }
        }

        $this->set('entity', $entity);
        $this->render('/Module/add');
        $this->set('_serialize', ['entity']);
    }

    /**
     * Edit method
     *
     * @param string $id Entity id.
     * @return \Cake\Http\Response|void|null
     */
    public function edit(string $id)
    {
        $table = $this->loadModel();

        $entity = $this->fetchEntity($id);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $post_data = (array)$this->request->getData();

            // enable accessibility to associated entity's primary key
            // to avoid associated entity getting flagged as new
            $options = $table instanceof Table ? $table->enablePrimaryKeyAccess() : [];

            $response = $this->persistEntity($entity, $post_data, $options);
            if ($response) {
                $this->saveAssociations($entity, $post_data);

                return $response;
            }
        }

        $this->set('entity', $entity);
        $this->render('/Module/edit');
        $this->set('_serialize', ['entity']);
    }

    /**
     * Save associations
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param mixed[] $post_data The post data
     * @return void
     */
    public function saveAssociations(EntityInterface $entity, array $post_data): void
    {
        $table = $this->loadModel();
        Assert::isInstanceOf($table, Table::class);

        $tableAssociations = $table->associations();
        if (empty($tableAssociations)) {
            return;
        }

        foreach ($tableAssociations as $association) {
            if ('manyToMany' !== $association->type()) {
                continue;
            }

            if (!array_key_exists($association->getName(), $post_data)) {
                continue;
            }

            $associationData = [];
            if (is_array($post_data[$association->getName()]) && array_key_exists('_ids', $post_data[$association->getName()]) && !empty($post_data[$association->getName()]['_ids'])) {
                foreach ($post_data[$association->getName()]['_ids'] as $id) {
                    $associationData[] = TableRegistry::getTableLocator()->get($association->className())->get($id);
                }
            }

            Assert::isInstanceOf($association, BelongsToMany::class);
            $association->replaceLinks($entity, $associationData);
        }
    }

    /**
     * Fetches entity from database.
     *
     * Tries to fetch the record using the primary key, if no record found and the ID
     * value is not a UUID it will try to fetch the record using the lookup fields.
     * If that fails as well then a record not found exception will be thrown.
     *
     * @param string $id Entity id
     * @return \Cake\Datasource\EntityInterface
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When the record is not found
     */
    protected function fetchEntity(string $id): EntityInterface
    {
        $table = $this->loadModel();
        Assert::isInstanceOf($table, Table::class);

        $primaryKey = $table->getPrimaryKey();
        if (! is_string($primaryKey)) {
            throw new UnsupportedPrimaryKeyException();
        }

        try {
            $entity = $table->find()
                ->where([$table->aliasField($primaryKey) => $id])
                ->enableHydration(true)
                ->firstOrFail();
            Assert::isInstanceOf($entity, EntityInterface::class);

            return $entity;
        } catch (Exception $e) {
            // $id is a UUID, re-throwing the exception as we cannot fetch the record by lookup field(s)
            if (Validation::uuid($id)) {
                throw $e;
            }
        }

        /**
         * Try to fetch record by lookup field(s)
         *
         * @var \Cake\Datasource\EntityInterface
         */
        $entity = $table->find()
            ->applyOptions(['lookup' => true, 'value' => $id])
            ->enableHydration(true)
            ->firstOrFail();

        return $entity;
    }

    /**
     * Persist new/modified entity.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity instance
     * @param mixed[] $data Request data
     * @param mixed[] $options Patch options
     * @return \Cake\Http\Response|null
     */
    protected function persistEntity(EntityInterface $entity, array $data, array $options = []): ?Response
    {
        $table = $this->loadModel();
        Assert::isInstanceOf($table, Table::class);

        $options = array_merge($options, ['lookup' => true]);
        $entity = $table->patchEntity($entity, $data, $options);

        $saved = false;
        try {
            $saved = $table->save($entity);
        } catch (PDOException $e) {
            Log::error($e->getMessage());
        }

        if ($entity->getErrors()) {
            Log::warning((string)json_encode($entity->getErrors()));
        }

        if (! $saved) {
            $this->Flash->error((string)__('The record could not be saved, please try again.'));
        }

        if ($saved) {
            $this->Flash->success((string)__('The record has been saved.'));

            $primaryKey = $table->getPrimaryKey();
            if (! is_string($primaryKey)) {
                throw new UnsupportedPrimaryKeyException();
            }

            // handle file uploads if found in the request data
            $fileUpload = new FileUpload($table);
            $fileUpload->link(
                $entity->get($primaryKey),
                (array)$this->request->getData()
            );

            $url = [];
            if ($table instanceof Table) {
                $url = $table->getParentRedirectUrl($table, $entity);
            }
            $url = ! empty($url) ? $url : ['action' => 'view', $entity->get($primaryKey)];

            return $this->redirect($url);
        }

        return null;
    }

    /**
     * Delete method
     *
     * @param string $id Entity id.
     * @return \Cake\Http\Response|void|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete(string $id)
    {
        $this->request->allowMethod(['post', 'delete']);
        $model = $this->loadModel();
        $entity = $model->get($id);

        if ($model->delete($entity)) {
            $this->Flash->success((string)__('The record has been deleted.'));
        } else {
            $this->Flash->error((string)__('The record could not be deleted. Please, try again.'));
        }

        $url = $this->referer();

        if (false !== strpos($url, $id)) {
            $url = ['action' => 'index'];
        }

        return $this->redirect($url);
    }

    /**
     * Unlink method
     *
     * @param string $id Entity id.
     * @param string $assocName Association Name.
     * @param string $assocId Associated Entity id.
     * @return \Cake\Http\Response|void|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function unlink(string $id, string $assocName, string $assocId)
    {
        $this->request->allowMethod(['post']);

        $table = $this->loadModel();
        Assert::isInstanceOf($table, Table::class);

        $entity = $table->get($id);
        $assocEntity = $table->{$assocName}->get($assocId);

        // unlink associated record
        $table->{$assocName}->unlink($entity, [$assocEntity]);

        $this->Flash->success((string)__('The record has been unlinked.'));

        return $this->redirect($this->referer());
    }

    /**
     * Link Method
     *
     * Embedded linking form for many-to-many records,
     * link the associations without calling direct edit() action
     * on the origin entity - it prevents overwritting the associations
     *
     * @param string $id Entity id.
     * @param string $associationName Association Name.
     * @return \Cake\Http\Response|void|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function link(string $id, string $associationName)
    {
        $this->request->allowMethod(['post']);

        $table = $this->loadModel();
        Assert::isInstanceOf($table, Table::class);

        $association = $table->{$associationName};
        $ids = (array)$this->request->getData($associationName . '._ids');

        if (empty($ids)) {
            $this->Flash->error((string)__('No records provided for linking.'));

            return $this->redirect($this->referer());
        }

        $primaryKey = $association->getPrimaryKey();
        if (!is_string($primaryKey)) {
            throw new UnsupportedPrimaryKeyException();
        }

        $query = $association->find('all')
            ->where([$association->getPrimaryKey() . ' IN' => $ids]);

        if ($query->isEmpty()) {
            $this->Flash->error((string)__('No records found for linking.'));

            return $this->redirect($this->referer());
        }

        if (! $association->link($table->get($id), $query->toArray())) {
            $this->Flash->error((string)__('Failed to link records.'));

            return $this->redirect($this->referer());
        }

        $this->Flash->success(sprintf('(%s)', count($ids)) . ' ' . __('records have been linked.'));

        return $this->redirect($this->referer());
    }

    /**
     * Batch operations action.
     *
     * @param string $operation Batch operation.
     * @return \Cake\Http\Response|void|null Redirects to referer.
     */
    public function batch(string $operation)
    {
        $this->request->allowMethod(['post']);

        $table = $this->loadModel();
        Assert::isInstanceOf($table, Table::class);

        $redirectUrl = $this->getBatchRedirectUrl();

        $batchIds = (array)$this->request->getData('batch.ids');
        if (empty($batchIds)) {
            $this->Flash->error((string)__('No records selected.'));

            return $this->redirect($redirectUrl);
        }

        $batchIdsCount = count($batchIds);

        // broadcast batch ids event
        $event = new Event((string)EventName::BATCH_IDS(), $this, [
            $batchIds,
            $operation,
            $this->Auth->user(),
        ]);
        $this->getEventManager()->dispatch($event);

        $batchIds = is_array($event->result) ? $event->result : $batchIds;

        if (empty($batchIds)) {
            $operation = strtolower(Inflector::humanize($operation));
            $this->Flash->error((string)__('Insufficient permissions to ' . $operation . ' the selected records.'));

            return $this->redirect($redirectUrl);
        }

        if ('delete' === $operation) {
            $primaryKey = $table->getPrimaryKey();
            if (! is_string($primaryKey)) {
                throw new UnsupportedPrimaryKeyException();
            }

            $conditions = [$primaryKey . ' IN' => $batchIds];
            $deleteMethod = $table->hasBehavior('Trash') ? 'trashAll' : 'deleteAll';
            // execute batch delete
            if ($table->{$deleteMethod}($conditions)) {
                $this->Flash->success(
                    (string)__('{0} of {1} selected records have been deleted.', count($batchIds), $batchIdsCount)
                );
            } else {
                $this->Flash->error((string)__('Selected records could not be deleted. Please, try again.'));
            }

            return $this->redirect($redirectUrl);
        }

        if ('edit' === $operation && (bool)$this->request->getData('batch.execute')) {
            $fields = (array)$this->request->getData($this->name);
            if (empty($fields)) {
                $this->Flash->error((string)__('Selected records could not be updated. No changes provided.'));

                return $this->redirect($redirectUrl);
            }

            $primaryKey = $table->getPrimaryKey();
            if (! is_string($primaryKey)) {
                throw new UnsupportedPrimaryKeyException();
            }

            $conditions = [$primaryKey . ' IN' => $batchIds];
            // execute batch edit
            if ($table->updateAll($fields, $conditions)) {
                $this->Flash->success(
                    (string)__('{0} of {1} selected records have been updated.', count($batchIds), $batchIdsCount)
                );
            } else {
                $this->Flash->error((string)__('Selected records could not be updated. Please, try again.'));
            }

            return $this->redirect($redirectUrl);
        }

        $this->set('entity', $table->newEntity());
        $this->set('fields', Field::getCsvView($table, $operation, true, true));
        $this->render('/Module/batch');
    }

    /**
     * Fetch batch redirect url.
     *
     * @return string
     */
    protected function getBatchRedirectUrl(): string
    {
        // default url
        $result = ['plugin' => $this->plugin, 'controller' => $this->name, 'action' => 'index'];

        $currentUrl = $this->request->getEnv('HTTP_ORIGIN') . $this->request->getRequestTarget();
        // if referer does not match current url, redirect to referer (delete action)
        if (false === strpos($this->referer(), $currentUrl)) {
            $result = $this->referer();
        }

        // use batch redirect url if provided (edit action)
        if ($this->request->getData('batch.redirect_url')) {
            $result = $this->request->getData('batch.redirect_url');
        }

        return Router::url($result);
    }
}
