<?php

namespace App\Test\TestCase\Controller\Component;

use App\Feature\Factory;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * LogActionsComponentTest class
 */
class LogActionsComponentTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Things',
        'app.LogAudit',
        'app.Users',
        'plugin.Menu.Menus',
        'plugin.Menu.MenuItems',
        'plugin.RolesCapabilities.Roles',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->enableRetainFlashMessages();

        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $userId = '00000000-0000-0000-0000-000000000001';
        $this->session([
            'Auth' => [
                'User' => TableRegistry::getTableLocator()->get('Users')->get($userId)->toArray(),
            ],
        ]);
    }

    public function testLogView(): void
    {
        $configure = [
            'LogActions' => [
                'enableLogActions' => true,
                'controllers' => [\App\Controller\ThingsController::class],
                'actions' => ['view'],
            ],
        ];
        Configure::write($configure);

        $query = TableRegistry::getTableLocator()->get('LogAudit')->find()->where(['source' => 'things', 'type' => 'read']);
        $count_record = (int)$query->count();

        $this->get('/things/view/00000000-0000-0000-0000-000000000001');
        $query = TableRegistry::getTableLocator()->get('LogAudit')->find()->where(['source' => 'things', 'type' => 'read']);

        $this->assertEquals($count_record + 1, $query->count());
    }

    public function testLogPostRequest(): void
    {
        $configure = [
            'LogActions' => [
                'enableLogActions' => true,
                'controllers' => [\App\Controller\ThingsController::class],
                'actions' => ['view'],
            ],
        ];
        Configure::write($configure);

        $query = TableRegistry::getTableLocator()->get('LogAudit')->find()->where(['source' => 'things', 'type' => 'read']);
        $count_record = (int)$query->count();

        $data = [
            'type' => 'a',
            'name' => 'test',
        ];

        $this->post('/things/add', $data);

        $query = TableRegistry::getTableLocator()->get('LogAudit')->find()->where(['source' => 'things', 'type' => 'read']);

        $this->assertEquals($count_record, $query->count());
    }

    public function testLogEmptyController(): void
    {
        $configure = [
            'LogActions' => [
                'enableLogActions' => true,
                'controllers' => [],
                'actions' => ['view'],
            ],
        ];
        Configure::write($configure);

        $query = TableRegistry::getTableLocator()->get('LogAudit')->find()->where(['source' => 'things', 'type' => 'read']);
        $count_record = (int)$query->count();

        $this->get('/things/view/00000000-0000-0000-0000-000000000001');
        $query = TableRegistry::getTableLocator()->get('LogAudit')->find()->where(['source' => 'things', 'type' => 'read']);

        $this->assertEquals($count_record, $query->count());
    }

    public function testLogAvoidAction(): void
    {
        $configure = [
            'LogActions' => [
                'enableLogActions' => true,
                'controllers' => [\App\Controller\ThingsController::class],
                'actions' => [],
            ],
        ];
        Configure::write($configure);

        $query = TableRegistry::getTableLocator()->get('LogAudit')->find()->where(['source' => 'things', 'type' => 'read']);
        $count_record = (int)$query->count();

        $this->get('/things/view/00000000-0000-0000-0000-000000000001');
        $query = TableRegistry::getTableLocator()->get('LogAudit')->find()->where(['source' => 'things', 'type' => 'read']);

        $this->assertEquals($count_record, $query->count());
    }
}
