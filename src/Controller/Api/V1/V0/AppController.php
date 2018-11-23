<?php
namespace App\Controller\Api\V1\V0;

use App\Event\EventName;
use App\Feature\Factory as FeatureFactory;
use App\Swagger\Annotation;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Crud\Controller\ControllerTrait;
use CsvMigrations\Controller\Traits\PanelsTrait;
use CsvMigrations\Utility\FileUpload;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;
use Qobo\Utils\Utility\User;
use RolesCapabilities\CapabilityTrait;

/**
 * @property \Cake\Http\ServerRequest $request
 * @property \Crud\Controller\Component\CrudComponent $Crud
 */
class AppController extends Controller
{
    use CapabilityTrait;
    use ControllerTrait;
    use PanelsTrait;

    public $components = [
        'RequestHandler',
        'Crud.Crud' => [
            'actions' => [
                'Crud.Index',
                'Crud.View',
                'Crud.Add',
                'Crud.Edit',
                'Crud.Delete',
                'Crud.Lookup',
                'related' => ['className' => '\App\Crud\Action\RelatedAction']
            ],
            'listeners' => [
                'Crud.Api',
                'Crud.ApiPagination',
                'Crud.ApiQueryLog'
            ]
        ]
    ];

    public $paginate = [
        'page' => 1,
        'limit' => 10,
        'maxLimit' => 100,
    ];

    /**
     * Authentication config
     *
     * @var array
     */
    protected $authConfig = [
        // non-persistent storage, for stateless authentication
        'storage' => 'Memory',
        'authenticate' => [
            // used for validating user credentials before the token is generated
            'Form' => [
                'finder' => 'auth'
            ],
            // used for token validation
            'ADmad/JwtAuth.Jwt' => [
                'parameter' => 'token',
                'userModel' => 'Users',
                'finder' => 'auth',
                'fields' => [
                    'username' => 'id'
                ],
                'queryDatasource' => true
            ]
        ],
        'unauthorizedRedirect' => false,
        'checkAuthIn' => 'Controller.initialize'
    ];

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->_authentication();

        // prevent access on disabled module
        $feature = FeatureFactory::get('Module' . DS . $this->name);
        if (!$feature->isActive()) {
            throw new NotFoundException();
        }

        if (Configure::read('API.auth')) {
            $this->enableAuthorization();
        }
    }

    /**
     * Enable API authorization checks.
     *
     * @throws \Cake\Http\Exception\ForbiddenException when user has no access
     * @return void
     */
    protected function enableAuthorization(): void
    {
        $user = empty($this->Auth->user()) ? [] : $this->Auth->user();
        $hasAccess = $this->_checkAccess($this->request->getAttribute('params'), $user);

        if (!$hasAccess) {
            throw new ForbiddenException();
        }
    }

    /**
     * Method that sets up API Authentication.
     *
     * @link http://www.bravo-kernel.com/2015/04/how-to-add-jwt-authentication-to-a-cakephp-3-rest-api/
     * @return void
     */
    protected function _authentication(): void
    {
        $this->loadComponent('Auth', $this->authConfig);

        $authObject = $this->Auth->getAuthenticate('ADmad/JwtAuth.Jwt');

        // set auth user from token
        $user = null === $authObject ? [] : $authObject->getUser($this->request);
        $this->Auth->setUser($user);

        // set current user for access to all MVC layers
        User::setCurrentUser((array)$this->Auth->user());

        // If API authentication is disabled, allow access to all actions. This is useful when using some
        // other kind of access control check.
        // @todo currently, even if API authentication is disabled, we are always generating an API token
        // within the Application for internal system use. That way we populate the Auth->user() information
        // which allows other access control systems to work as expected. This logic can be removed if API
        // authentication is always forced.
        if (!Configure::read('API.auth')) {
            $this->Auth->allow();
        }
    }

    /**
     * View CRUD action events handling logic.
     *
     * @return \Cake\Http\Response|void|null
     */
    public function view()
    {
        $this->Crud->on('beforeFind', function (Event $event) {
            if (! property_exists($event->getSubject(), 'query')) {
                return;
            }

            $event->getSubject()->query->applyOptions([
                'lookup' => true,
                'value' => $this->request->getParam('pass.0')
            ]);

            $ev = new Event((string)EventName::API_VIEW_BEFORE_FIND(), $this, [
                'query' => $event->getSubject()->query
            ]);
            $this->getEventManager()->dispatch($ev);
        });

        $this->Crud->on('afterFind', function (Event $event) {
            if (! property_exists($event->getSubject(), 'entity')) {
                return;
            }

            $ev = new Event((string)EventName::API_VIEW_AFTER_FIND(), $this, [
                'entity' => $event->getSubject()->entity
            ]);
            $this->getEventManager()->dispatch($ev);
        });

        return $this->Crud->execute();
    }

    /**
     * Related CRUD action events handling logic.
     *
     * @param string $id Record id
     * @param string $associationName Association name
     * @return \Cake\Http\Response|void|null
     */
    public function related(string $id, string $associationName)
    {
        $this->Crud->on('beforePaginate', function (Event $event) {
            if (! property_exists($event->getSubject(), 'query')) {
                return;
            }

            $ev = new Event((string)EventName::API_RELATED_BEFORE_PAGINATE(), $this, [
                'query' => $event->getSubject()->query
            ]);
            $this->getEventManager()->dispatch($ev);
        });

        $this->Crud->on('afterPaginate', function (Event $event) {
            if (! property_exists($event->getSubject(), 'entities')) {
                return;
            }

            $ev = new Event((string)EventName::API_RELATED_AFTER_PAGINATE(), $this, [
                'entities' => $event->getSubject()->entities
            ]);
            $this->getEventManager()->dispatch($ev);
        });

        $this->Crud->on('beforeRender', function (Event $event) {
            if (! property_exists($event->getSubject(), 'entities')) {
                return;
            }

            $ev = new Event((string)EventName::API_RELATED_BEFORE_RENDER(), $this, [
                'entities' => $event->getSubject()->entities
            ]);
            $this->getEventManager()->dispatch($ev);
        });

        return $this->Crud->execute();
    }

    /**
     * Index CRUD action events handling logic.
     *
     * @return \Cake\Http\Response|void|null
     */
    public function index()
    {
        $this->Crud->on('beforePaginate', function (Event $event) {
            if (! property_exists($event->getSubject(), 'query')) {
                return;
            }

            $ev = new Event((string)EventName::API_INDEX_BEFORE_PAGINATE(), $this, [
                'query' => $event->getSubject()->query
            ]);
            $this->getEventManager()->dispatch($ev);
        });

        $this->Crud->on('afterPaginate', function (Event $event) {
            if (! property_exists($event->getSubject(), 'entities')) {
                return;
            }

            $ev = new Event((string)EventName::API_INDEX_AFTER_PAGINATE(), $this, [
                'entities' => $event->getSubject()->entities
            ]);
            $this->getEventManager()->dispatch($ev);
        });

        $this->Crud->on('beforeRender', function (Event $event) {
            if (! property_exists($event->getSubject(), 'entities')) {
                return;
            }

            $ev = new Event((string)EventName::API_INDEX_BEFORE_RENDER(), $this, [
                'entities' => $event->getSubject()->entities
            ]);
            $this->getEventManager()->dispatch($ev);
        });

        return $this->Crud->execute();
    }

    /**
     * Add CRUD action events handling logic.
     *
     * @return \Cake\Http\Response|void|null
     */
    public function add()
    {
        /** @var \Crud\Action\AddAction */
        $action = $this->Crud->action();
        $action->saveOptions(['lookup' => true]);

        $this->Crud->on('beforeSave', function (Event $event) {
            if (! property_exists($event->getSubject(), 'entity')) {
                return;
            }

            $ev = new Event((string)EventName::API_ADD_BEFORE_SAVE(), $this, [
                'entity' => $event->getSubject()->entity
            ]);
            $this->getEventManager()->dispatch($ev);
        });

        $this->Crud->on('afterSave', function (Event $event) {
            if (! property_exists($event->getSubject(), 'entity')) {
                return;
            }

            /** @var \Cake\Datasource\RepositoryInterface&\Cake\ORM\Table */
            $table = $this->loadModel();

            // handle file uploads if found in the request data
            $fileUpload = new FileUpload($table);
            $fileUpload->link(
                $event->getSubject()->entity->get($table->getPrimaryKey()),
                (array)$this->request->getData()
            );

            $ev = new Event((string)EventName::API_ADD_AFTER_SAVE(), $this, [
                'entity' => $event->getSubject()->entity
            ]);
            $this->getEventManager()->dispatch($ev);
        });

        return $this->Crud->execute();
    }

    /**
     * Edit CRUD action events handling logic.
     *
     * @return \Cake\Http\Response|void|null
     */
    public function edit()
    {
        /** @var \Crud\Action\EditAction */
        $action = $this->Crud->action();
        $action->saveOptions(['lookup' => true]);

        $this->Crud->on('beforeFind', function (Event $event) {
            if (! property_exists($event->getSubject(), 'query')) {
                return;
            }

            $event->getSubject()->query->applyOptions([
                'lookup' => true,
                'value' => $this->request->getParam('pass.0')
            ]);

            $ev = new Event((string)EventName::API_EDIT_BEFORE_FIND(), $this, [
                'query' => $event->getSubject()->query
            ]);
            $this->getEventManager()->dispatch($ev);
        });

        $this->Crud->on('afterFind', function (Event $event) {
            if (! property_exists($event->getSubject(), 'entity')) {
                return;
            }

            $ev = new Event((string)EventName::API_EDIT_AFTER_FIND(), $this, [
                'entity' => $event->getSubject()->entity
            ]);
            $this->getEventManager()->dispatch($ev);
        });

        $this->Crud->on('beforeSave', function (Event $event) {
            if (! property_exists($event->getSubject(), 'entity')) {
                return;
            }

            $ev = new Event((string)EventName::API_EDIT_BEFORE_SAVE(), $this, [
                'entity' => $event->getSubject()->entity
            ]);
            $this->getEventManager()->dispatch($ev);
        });

        $this->Crud->on('afterSave', function (Event $event) {
            if (! property_exists($event->getSubject(), 'entity')) {
                return;
            }

            /** @var \Cake\Datasource\RepositoryInterface&\Cake\ORM\Table */
            $table = $this->loadModel();

            // handle file uploads if found in the request data
            $fileUpload = new FileUpload($table);
            $fileUpload->link(
                $event->getSubject()->entity->get($table->getPrimaryKey()),
                (array)$this->request->getData()
            );
        });

        return $this->Crud->execute();
    }

    /**
     * Delete CRUD action events handling logic.
     *
     * @return \Cake\Http\Response|void|null
     */
    public function delete()
    {
        return $this->Crud->execute();
    }

    /**
     * upload function shared among API controllers
     *
     * @return void
     */
    public function upload(): void
    {
        $this->request->allowMethod(['post']);

        $fileUpload = new FileUpload($this->loadModel());

        $result = [
            'success' => true,
            'data' => []
        ];
        foreach ((array)$this->request->getData($this->name) as $field => $files) {
            if (! is_array($files)) {
                continue;
            }

            $result['data'] = $fileUpload->saveAll($field, $files);
        }

        $this->set('result', $result);
        $this->set('_serialize', 'result');
    }

    /**
     * Lookup CRUD action events handling logic.
     *
     * @return \Cake\Http\Response|void|null
     */
    public function lookup()
    {
        $this->Crud->on('beforeLookup', function (Event $event) {
            if (! property_exists($event->getSubject(), 'query')) {
                return;
            }

            $ev = new Event((string)EventName::API_LOOKUP_BEFORE_FIND(), $this, [
                'query' => $event->getSubject()->query
            ]);
            $this->getEventManager()->dispatch($ev);
        });

        $this->Crud->on('afterLookup', function (Event $event) {
            if (! property_exists($event->getSubject(), 'entities')) {
                return;
            }

            $ev = new Event((string)EventName::API_LOOKUP_AFTER_FIND(), $this, [
                'entities' => $event->getSubject()->entities
            ]);
            $this->getEventManager()->dispatch($ev);
            $event->getSubject()->entities = $ev->result;
        });

        return $this->Crud->execute();
    }

    /**
     * Panels to show.
     *
     * @return array|void
     */
    public function panels()
    {
        $this->request->allowMethod(['ajax', 'post']);
        $result = [
            'success' => false,
            'data' => [],
        ];
        $data = $this->request->getData();
        if (empty($data) || ! is_array($data)) {
            return $result;
        }

        if (is_array($data[$this->name])) {
            $innerKey = key($data[$this->name]);
            // embedded form - [module][dynamicField][inputName] : Regular form format - [module][inputName]
            $data = is_array($data[$this->name][$innerKey]) ? $data[$this->name][$innerKey] : $data[$this->name];
        }

        $panels = $this->getPanels(
            (new ModuleConfig(ConfigType::MODULE(), $this->name))->parseToArray(),
            $data
        );
        if (! empty($panels)) {
            $result['success'] = true;
            $result['data'] = $panels;
        }

        $this->set('result', $result);
        $this->set('_serialize', 'result');
    }

    /**
     * Before filter handler.
     *
     * @param  \Cake\Event\Event $event The event.
     * @return mixed
     * @link   http://book.cakephp.org/3.0/en/controllers/request-response.html#setting-cross-origin-request-headers-cors
     */
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $this->response->cors($this->request)
            ->allowOrigin(['*'])
            ->allowMethods(['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'])
            ->allowHeaders(['X-CSRF-Token', 'Origin', 'X-Requested-With', 'Content-Type', 'Accept'])
            ->maxAge($this->_getSessionTimeout())
            ->build();

        // if request method is OPTIONS just return the response with appropriate headers.
        if ('OPTIONS' === $this->request->getMethod()) {
            return $this->response;
        }
    }

    /**
     * Get session timeout in seconds
     *
     * @return int Session lifetime in seconds
     */
    protected function _getSessionTimeout(): int
    {
        // Read from Session.timeout configuration
        $result = Configure::read('Session.timeout');
        if ($result) {
            $result = $result * 60; // Convert minutes to seconds
        }

        // Read from PHP configuration
        if (!$result) {
            $result = ini_get('session.gc_maxlifetime');
        }

        // Fallback on default
        if (!$result) {
            $result = 1800; // 30 minutes
        }

        return $result;
    }

    /**
     * Generates Swagger annotations
     *
     * Instantiates CsvAnnotation with required parameters
     * and returns its generated swagger annotation content.
     *
     * @param string $className Model class name
     * @param string $path File path
     * @param bool $withInfo Info annotation flag
     * @return string
     */
    public static function generateSwaggerAnnotations(string $className, string $path, bool $withInfo): string
    {
        $csvAnnotation = new Annotation($className, $path, $withInfo);

        return $csvAnnotation->getContent();
    }
}
