<?php

namespace App\Controller;

use App\Controller\AppController;
use App\Model\Table\SettingsTable;
use Cake\Core\Configure;
use Cake\Http\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Settings Controller
 *
 * @property \App\Model\Table\SettingsTable $Settings
 *
 * @method \App\Model\Entity\Setting[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 * @method \Cake\ORM\Table filterSettings(array $dataSettings, array $userScope)
 */
class SettingsController extends AppController
{

    /**
     * Implemented scope are : user, app
     * @var string
     */
    private $scope;

    /**
     * Value of the scope. In case of :
     * user => uuid
     * app  => SettingsTable::SCOPE_APP
     * @var string
     */
    private $context = '';

    /**
     * It will read the current user setting from Configure::read()
     * or load from the settings table, in case of SettingsTable::SCOPE_APP or other user settings
     * @var array
     */
    private $dataSettings;

    /**
     * Data from the DB with scope SettingsTable::SCOPE_APP
     * @var array
     */
    private $dataApp;

    /**
     * TableRegistry::get('Settings');
     * @var \App\Model\Table\SettingsTable
     */
    private $query;

    /**
     * Instead Configure::read(), it will load form the DB the settings of each scope/context
     * if the user doesn't have a record for a particular key, it will use the app value.
     * @var array
     */
    private $configureValue;

    /**
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
        $this->dataSettings = Configure::read('Settings');
        /**
         * @var \App\Model\Table\SettingsTable $table
         */
        $table = TableRegistry::get('Settings');
        $this->query = $table;
        $this->dataApp = (array)$table->find('dataApp', ['scope' => SettingsTable::SCOPE_APP, 'context' => SettingsTable::CONTEXT_APP]);
    }

    /**
     * Give access to edit any user settings.
     * @param string $context uuid of user
     * @return \Cake\Http\Response|void|null
     */
    public function user(string $context)
    {
        $this->scope = SettingsTable::SCOPE_USER;
        $this->context = $context;
        $dataUser = (array)$this->query->find('dataApp', ['scope' => SettingsTable::SCOPE_USER, 'context' => $this->context]);

        $this->configureValue = Hash::merge($this->dataApp, $dataUser);
        $this->dataSettings = Hash::merge($this->dataSettings, Hash::expand($this->dataApp), Hash::expand($dataUser));
        $this->viewBuilder()->template('index');

        $userName = TableRegistry::get('Users')->find('list')->where(['id' => $context])->toArray();
        $this->set('afterTitle', $userName[$context]);

        return $this->settings();
    }

    /**
     * Give access to edit app settings
     * @return \Cake\Http\Response|void|null
     */
    public function app()
    {
        $this->scope = SettingsTable::SCOPE_APP;
        $this->context = SettingsTable::CONTEXT_APP;
        $this->configureValue = $this->dataApp;
        $this->viewBuilder()->template('index');
        $this->set('afterTitle', 'App');

        if ($this->isLocalhost()) {
            $this->set('linkToGenerator', true);
        }

        return $this->settings();
    }

    /**
     * Give access to edit personal settings
     * @return \Cake\Http\Response|void|null
     */
    public function my()
    {
        $this->scope = SettingsTable::SCOPE_USER;
        $this->context = $this->Auth->user('id');
        $dataUser = $this->query->find('dataApp', ['scope' => SettingsTable::SCOPE_USER, 'context' => $this->context]);

        $this->configureValue = Hash::merge($this->dataApp, $dataUser);
        $this->viewBuilder()->template('index');

        $this->set('afterTitle', $this->Auth->user('username'));

        return $this->settings();
    }

    /**
     * Redirect to my()
     * @return \Cake\Http\Response|void|null
     */
    public function index()
    {
        $this->redirect(['action' => 'my']);
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void|null
     */
    private function settings()
    {
        $dataFiltered = $this->query->filterSettings($this->dataSettings, [$this->scope]);
        $settings = $this->paginate($this->Settings);
        $this->set(compact('settings'));
        $this->set('data', $dataFiltered);
        $this->set('configure', $this->configureValue);

        if ($this->request->is('put')) {
            $dataPut = Hash::flatten($this->request->data('Settings'));
            $type = Hash::combine($dataFiltered, '{s}.{s}.{s}.{s}.alias', '{s}.{s}.{s}.{s}.type');
            $scope = Hash::combine($dataFiltered, '{s}.{s}.{s}.{s}.alias', '{s}.{s}.{s}.{s}.scope');
            $links = Hash::filter(Hash::combine($dataFiltered, '{s}.{s}.{s}.{s}.alias', '{s}.{s}.{s}.{s}.links'));

            $set = [];
            foreach ($dataPut as $key => $value) {
                $type[$key] !== 'list' ?: $type[$key] = 'string';

                $entity = $this->query->createEntity($key, $value, $type[$key], $this->scope, $this->context);
                if (!empty($entity)) {
                    $set[] = $entity;
                }

                if (empty($links[$key])) {
                    continue;
                }

                foreach ($links[$key] as $link => $keyLink) {
                    $entity = $this->query->createEntity($keyLink, $value, $type[$key], $this->scope, $this->context);
                    if (!empty($entity)) {
                        $set[] = $entity;
                    }
                }
            }

            if (empty($set)) {
                $this->Flash->success((string)__('Nothing to update'));

                return $this->redirect($this->request->referer());
            }

            /**
             * @var \Cake\ORM\ResultSet&iterable<\Cake\Datasource\EntityInterface> $entities
             */
            $entities = $set;
            if ($this->query->saveMany($entities)) {
                $this->Flash->success((string)__('Settings successfully updated'));

                return $this->redirect($this->request->referer());
            } else {
                $this->Flash->error((string)__('Failed to update settings, please try again.'));
            }
        }
    }

    /**
     * ONLY for developers
     * Pass data to generator page
     * Avaiable only for developers in localhost
     * @return \Cake\Http\Response|void
     * @throws \Cake\Http\Exception\UnauthorizedException check if is localhost
     */
    public function generator()
    {
        if (!$this->isLocalhost()) {
            throw new UnauthorizedException('Run in localhost to access');
        }

        // For render the main structure
        $dataSettings = Configure::read('Settings');
        $this->set('data', empty($dataSettings) ? null : $dataSettings);
        // For seach the new fields to insert
        $data = Configure::read();
        // Remove settings.php
        unset($data['Settings']);
        $data = Hash::flatten($data);
        $this->set('alldata', $data);

        // list of scope
        $this->set('scope', [SettingsTable::SCOPE_USER, SettingsTable::SCOPE_APP]);

        if ($this->request->is('post')) {
            $this->autoRender = false;
            var_export($this->request->data());
        }
    }

    /**
     * Check if the webserver is on localhost
     * @return bool true if is localhost
     */
    private function isLocalhost(): bool
    {
        $localhost = '/^https?:\/\/(localhost|127(?:\.[0-9]+){0,2}\.[0-9]+|^(?:0*\:)*?:?0*1)(:\d{2,}|)(\/?)$/';

        $url = \Cake\Routing\Router::fullbaseUrl();

        if (!preg_match_all($localhost, $url)) {
            return false;
        }

        return true;
    }
}
