<?php

namespace App\Test\TestCase\Controller\Api\V1\V0;

use App\Event\Controller\Api\EditActionListener;
use App\Event\Controller\Api\IndexActionListener;
use App\Event\Controller\Api\ViewActionListener;
use Cake\Event\EventManager;
use Cake\Http\Client;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

/**
 * Users\Controller\UsersController Test Case
 *
 * @property \Cake\Http\Response $_response
 */
class UsersControllerTest extends IntegrationTestCase
{
    /**
     * @var \App\Model\Table\UsersTable $Users
     */
    private $Users;

    public $fixtures = [
        'app.Users',
        'plugin.Groups.Groups',
        'plugin.Groups.GroupsUsers',
    ];

    /**
     * External API Client object
     *
     * @var \Cake\Http\Client for external api calls.
     */
    protected $apiClient = null;

    public function setUp()
    {
        parent::setUp();

        /**
         * @var \App\Model\Table\UsersTable $table
         */
        $table = TableRegistry::getTableLocator()->get('Users');
        $this->Users = $table;

        // set headers without auth token by default.
        $this->setHeaders();

        $this->apiClient = new Client([
            'host' => 'localhost:8000',
            'scheme' => 'http',
        ]);

        EventManager::instance()->on(new EditActionListener());
        EventManager::instance()->on(new IndexActionListener());
        EventManager::instance()->on(new ViewActionListener());
    }

    public function tearDown()
    {
        unset($this->Users);

        parent::tearDown();
    }

    private function setHeaders(): void
    {
        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    private function setAuthHeaders(string $id): void
    {
        $token = JWT::encode(
            ['sub' => $id, 'exp' => time() + 604800],
            Security::getSalt()
        );

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'authorization' => 'Bearer ' . $token,
            ],
        ]);
    }

    public function testToken(): void
    {
        $data = [
            'username' => 'user-6',
            'password' => '12345',
        ];

        $this->post('/api/users/token.json', $data);

        $this->assertResponseOk();
        $this->assertResponseCode(200);
        $this->assertContentType('application/json');
    }

    public function testTokenWithNonActiveUser(): void
    {
        $data = [
            'username' => 'user-1',
            'password' => '12345',
        ];

        $this->post('/api/users/token.json', $data);

        $this->assertResponseError();
        $this->assertResponseCode(401);
        $this->assertContentType('application/json');
    }

    public function testInitializeForbidden(): void
    {
        // Valid data
        $data = [
            'username' => 'foo',
            'email' => 'foo@company.com',
            'password' => 'bar',
            'active' => true,
        ];

        $this->post('/api/users/add.json', $data);

        $this->assertResponseError();
        $this->assertResponseCode(403);
        $this->assertContentType('application/json');
    }

    public function testTokenInvalid(): void
    {
        $this->post('/api/users/token.json', []);

        $this->assertResponseError();
        $this->assertResponseCode(401);
        $this->assertContentType('application/json');
    }

    public function testViewByLookupField(): void
    {
        $this->setAuthHeaders('00000000-0000-0000-0000-000000000002');

        $email = 'user-2@test.com';
        $this->get('/api/users/view/' . $email . '.json');

        $this->assertResponseOk();
        $this->assertResponseCode(200);
        $this->assertContentType('application/json');

        /**
         * @var \Cake\Http\Response $this->_response
         */
        $body = $this->_response->getBody();
        $response = json_decode($body);

        $this->assertEquals($email, $response->data->email);
    }

    public function testEditByLookupField(): void
    {
        $this->setAuthHeaders('00000000-0000-0000-0000-000000000002');

        // lookup field
        $username = 'user-1';
        $id = '00000000-0000-0000-0000-000000000001';

        $data = [
            'first_name' => 'Some really random first name',
        ];

        $entity = $this->Users->get($id);
        $this->assertNotEquals($data['first_name'], $entity->get('first_name'));

        $this->get('/api/users/index.json');
        $this->assertResponseOk();

        $this->setAuthHeaders('00000000-0000-0000-0000-000000000002');
        $this->put('/api/users/edit/' . $username . '.json', $data);

        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $entity = $this->Users->get($id);

        $this->assertEquals($data['first_name'], $entity->get('first_name'));
    }

    public function testIndexWithArrayConditions(): void
    {
        $this->setAuthHeaders('00000000-0000-0000-0000-000000000002');
        $this->get('/api/users/index.json?conditions[username][]=foo&conditions[username][]=user-1');
        $this->assertResponseOk();

        $id = '00000000-0000-0000-0000-000000000001';
        $entity = $this->Users->get($id);

        $this->assertEquals('first1', $entity->get('first_name'));
    }
}
