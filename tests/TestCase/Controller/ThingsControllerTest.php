<?php

namespace App\Test\TestCase\Controller;

use App\Feature\Factory;
use App\Model\Entity\Thing;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * App\Test\App\Controller\ThingsController Test Case
 */
class ThingsControllerTest extends IntegrationTestCase
{
    public $fixtures = [
        'app.Things',
        'app.Users',
        'app.LogAudit',
        'app.FileStorage',
        'plugin.Menu.Menus',
        'plugin.Menu.MenuItems',
        'plugin.RolesCapabilities.Roles',
        'plugin.Translations.Languages',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->enableRetainFlashMessages();

        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $userId = '00000000-0000-0000-0000-000000000001';
        $this->session([
            'Auth' => [
                'User' => TableRegistry::getTableLocator()->get('Users')->get($userId)->toArray(),
            ],
        ]);
    }

    public function testViewUnauthenticatedFails(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $this->session(['Auth' => '']);

        $id = '00000000-0000-0000-0000-000000000001';

        // No session data set.
        $this->get('/things/view/' . $id);
        $this->assertRedirectContains('/login?redirect=%2Fthings%2Fview%2F' . $id);
    }

    public function testView(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $this->get('/things/view/00000000-0000-0000-0000-000000000001');

        $this->assertResponseOk();

        $this->assertResponseContains('<a href="#translations_translate_id_modal" data-toggle="modal" data-record="00000000-0000-0000-0000-000000000001" data-model="Things" data-field="description" data-value="Long description goes here">');
        $this->assertResponseContains('<textarea name="orig_for_translate"');
        $this->assertResponseContains(' id="orig_for_translate"');
        $this->assertResponseContains('<input type="hidden" name="model"');
        $this->assertResponseContains('<input type="hidden" name="field"');
        $this->assertResponseContains('<input type="hidden" name="foreign_key"');

        $this->assertResponseContains('id="form_translation_ru"');
        $this->assertResponseContains('<input type="hidden" name="id" id="translation_id_ru"');
        $this->assertResponseContains('<input type="hidden" name="language_id" value="00000000-0000-0000-0000-000000000001"');
        $this->assertResponseContains('<input type="hidden" name="code" value="ru"');
        $this->assertResponseContains('<div id="result_ru"');
        $this->assertResponseContains('<button id="btn_translate_ru" name="btn_translation" data-lang="ru"');

        $this->assertResponseContains('id="form_translation_de"');
        $this->assertResponseContains('<input type="hidden" name="id" id="translation_id_de"');
        $this->assertResponseContains('<input type="hidden" name="language_id" value="00000000-0000-0000-0000-000000000002"');
        $this->assertResponseContains('<input type="hidden" name="code" value="de"');
        $this->assertResponseContains('<div id="result_de"');
        $this->assertResponseContains('<button id="btn_translate_ru" name="btn_translation" data-lang="de"');

        $this->assertResponseContains('id="form_translation_cn"');
        $this->assertResponseContains('<input type="hidden" name="id" id="translation_id_cn"');
        $this->assertResponseContains('<input type="hidden" name="language_id" value="00000000-0000-0000-0000-000000000003"');
        $this->assertResponseContains('<input type="hidden" name="code" value="cn"');
        $this->assertResponseContains('<div id="result_cn"');
        $this->assertResponseContains('<button id="btn_translate_ru" name="btn_translation" data-lang="cn"');
    }

    public function testAddUnauthenticatedFails(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $this->session(['Auth' => '']);

        // No session data set.
        $this->get('/things/add');

        $this->assertRedirectContains('/login');
    }

    public function testAdd(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $this->get('/things/add');
        $this->assertResponseOk();
        // form element and attributes
        $this->assertResponseContains('<form');
        $this->assertResponseContains('action="/things/add"');
        // submit button
        $this->assertResponseContains('type="submit"');
        // input element(s) and attributes
        $this->assertResponseContains('name');
        $this->assertResponseContains('name="Things[name]"');
    }

    public function testAddPostData(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $data = [
            'type' => 'a',
            'name' => 'test',
            'country' => 'CY',
            'currency' => 'EUR',
            'gender' => 'm',
            'email' => 'name@domain.com',
        ];

        $this->post('/things/add', $data);
        $this->assertResponseSuccess();

        // fetch new record
        $query = TableRegistry::getTableLocator()->get('Things')->find()->where(['name' => $data['name']]);

        $this->assertEquals(1, $query->count());
    }

    public function testEditUnauthenticatedFails(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $this->session(['Auth' => '']);

        // No session data set.
        $this->get('/things/edit');

        $this->assertRedirectContains('/login');
    }

    public function testEdit(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $this->get('/things/edit/00000000-0000-0000-0000-000000000001');
        $this->assertResponseOk();
        // form element and attributes
        $this->assertResponseContains('<form');
        $this->assertResponseContains('action="/things/edit/00000000-0000-0000-0000-000000000001"');
        // $this->assertResponseContains('data-panels-url="/api/things/panels"');
        // submit button
        $this->assertResponseContains('type="submit"');
        // input element(s) and attributes
        $this->assertResponseContains('name');
        $this->assertResponseContains('name="Things[name]"');
    }

    public function testEditPostData(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $id = '00000000-0000-0000-0000-000000000001';

        $data = [
            'type' => 'a',
            'name' => 'test',
        ];

        $this->post('/things/edit/' . $id, $data);
        $this->assertResponseSuccess();

        // fetch modified record
        $entity = TableRegistry::getTableLocator()->get('things')->get($id);

        $this->assertEquals($data['name'], $entity->get('name'));
    }

    public function testEditPutData(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $id = '00000000-0000-0000-0000-000000000001';

        $data = [
            'type' => 'a',
            'name' => 'test',
        ];

        $this->put('/things/edit/' . $id, $data);
        $this->assertResponseSuccess();

        // fetch modified record
        $entity = TableRegistry::getTableLocator()->get('things')->get($id);

        $this->assertEquals($data['name'], $entity->get('name'));
    }

    public function testDeleteUnauthenticatedFails(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $this->session(['Auth' => '']);

        // No session data set.
        $this->delete('/things/delete/00000000-0000-0000-0000-000000000001');

        $this->assertRedirect(['controller' => 'Users', 'action' => 'login']);
    }

    public function testDeleteGetRequest(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $this->get('/things/delete/00000000-0000-0000-0000-000000000001');
        $this->assertResponseError();
    }

    public function testDeleteData(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $id = '00000000-0000-0000-0000-000000000001';

        $this->delete('/things/delete/' . $id);
        $this->assertResponseSuccess();

        // try to fetch deleted record
        $query = TableRegistry::getTableLocator()->get('things')->find()->where(['id' => $id]);
        $this->assertEquals(0, $query->count());
    }

    public function testDeletePostData(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $id = '00000000-0000-0000-0000-000000000001';

        $this->post('/things/delete/' . $id);
        $this->assertResponseSuccess();

        // try to fetch deleted record
        $query = TableRegistry::getTableLocator()->get('things')->find()->where(['id' => $id]);
        $this->assertEquals(0, $query->count());
    }

    public function testBatchGetRequest(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $this->get('/things/batch/edit');
        $this->assertResponseError();
    }

    public function testBatchDelete(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $table = TableRegistry::getTableLocator()->get('things');
        $initialCount = $table->find('all')->count();

        $data = [
            'batch' => [
                'ids' => [
                    '00000000-0000-0000-0000-000000000001',
                    '00000000-0000-0000-0000-000000000002',
                ],
            ],
        ];

        $this->post('/things/batch/delete', $data);
        $this->assertResponseSuccess();
        $this->assertSession('2 of 2 selected records have been deleted.', 'Flash.flash.0.message');

        $this->assertSame($initialCount - 2, $table->find('all')->count());
    }

    public function testBatchDeleteNoIds(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $this->post('/things/batch/delete');
        $this->assertRedirect('/');
        $this->assertSession('No records selected.', 'Flash.flash.0.message');
    }

    public function testBatchEdit(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $data = [
            'batch' => [
                'ids' => [
                    '00000000-0000-0000-0000-000000000001',
                    '00000000-0000-0000-0000-000000000002',
                ],
            ],
        ];
        $this->post('/things/batch/edit', $data);
        $this->assertResponseSuccess();

        $entity = $this->viewVariable('entity');

        $this->assertInstanceOf(Thing::class, $entity);
        $this->assertTrue($entity->isNew());
    }

    public function testBatchEditNoIds(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $this->post('/things/batch/edit');
        $this->assertRedirect('/');
        $this->assertSession('No records selected.', 'Flash.flash.0.message');
    }

    public function testBatchEditExecuteNoIds(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $data = [
            'batch' => [
                'execute' => true,
            ],
        ];

        $this->post('/things/batch/edit', $data);
        $this->assertRedirect('/');
        $this->assertSession('No records selected.', 'Flash.flash.0.message');
    }

    public function testBatchEditExecuteNoData(): void
    {
        $feature = Factory::get('Module' . DS . 'Things');

        if (! $feature->isActive()) {
            $this->markTestSkipped('Skipping, Things module is disabled');
        }

        $data = [
            'batch' => [
                'execute' => true,
                'ids' => [
                    '00000000-0000-0000-0000-000000000001',
                    '00000000-0000-0000-0000-000000000002',
                ],
            ],
        ];

        $this->post('/things/batch/edit', $data);
        $this->assertResponseSuccess();
        $this->assertSession('Selected records could not be updated. No changes provided.', 'Flash.flash.0.message');
    }
}
