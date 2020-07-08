<?php

namespace App\Test\TestCase\Controller\Api;

use App\Feature\Factory;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;
use Qobo\Utils\TestSuite\JsonIntegrationTestCase;

/**
 * @property \Cake\Http\Response $_response
 */
class ControllerApiTest extends JsonIntegrationTestCase
{
    public $fixtures = [
        'plugin.CsvMigrations.Dblists',
        'plugin.CsvMigrations.DblistItems',
        'app.Things',
        'app.LogAudit',
        'app.Users',
        'app.FileStorage',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->setRequestHeaders([], '00000000-0000-0000-0000-000000000002');
    }

    public function testApiFilesPlacedCorrectly(): void
    {
        $path = App::path('Controller/Api')[0];
        $dir = new Folder($path);
        $found = 0;

        // checking for scanned files
        foreach ($dir->find('^\w+Controller\.php$') as $file) {
            $found++;
        }

        $this->assertEquals(0, $found, "Check API directory. Not all controllers were moved to corresponding API subdirs");
    }

    /**
     * @dataProvider modulesProvider
     */
    public function testIndex(string $module): void
    {
        $this->get('/api/' . Inflector::dasherize($module));
        $this->assertJsonResponseOk();
    }

    /**
     * @dataProvider modulesProvider
     */
    public function testView(string $module): void
    {
        $table = TableRegistry::getTableLocator()->get($module);
        $entity = $table->newEntity();
        $table->save($entity);

        /**
         * @var string
         */
        $primaryKey = $table->getPrimaryKey();

        $this->get('/api/' . Inflector::dasherize($module) . '/view/' . $entity->get($primaryKey));
        $this->assertJsonResponseOk();

        $response = $this->getParsedResponse();
        $this->assertEquals($entity->get($primaryKey), $response->data->{$primaryKey});
    }

    /**
     * @dataProvider modulesProvider
     */
    public function testAdd(string $module): void
    {
        $table = TableRegistry::getTableLocator()->get($module);

        $this->post('/api/' . Inflector::dasherize($module) . '/add/');
        $this->assertTrue(in_array($this->_response->getStatusCode(), [201, 422]));
        $this->assertContentType('application/json');

        if (201 === $this->_response->getStatusCode()) {
            $response = $this->getParsedResponse();
            $this->assertEquals(36, strlen($response->data->{$table->getPrimaryKey()}));
        }
    }

    /**
     * @dataProvider modulesProvider
     */
    public function testEdit(string $module): void
    {
        $table = TableRegistry::getTableLocator()->get($module);
        $entity = $table->newEntity();
        $table->save($entity);

        /**
         * @var string
         */
        $primaryKey = $table->getPrimaryKey();

        $this->put('/api/' . Inflector::dasherize($module) . '/edit/' . $entity->get($primaryKey));
        $this->assertJsonResponseOk();

        $response = $this->getParsedResponse();
        $this->assertInternalType('array', $response->data);
        $this->assertEmpty($response->data);
    }

    /**
     * @dataProvider modulesProvider
     */
    public function testDelete(string $module): void
    {
        $table = TableRegistry::getTableLocator()->get($module);
        $entity = $table->newEntity();
        $table->save($entity);

        /**
         * @var string
         */
        $primaryKey = $table->getPrimaryKey();

        $this->delete('/api/' . Inflector::dasherize($module) . '/delete/' . $entity->get($primaryKey));
        $this->assertJsonResponseOk();

        $response = $this->getParsedResponse();
        $this->assertInternalType('array', $response->data);

        $query = $table->find()->where([$primaryKey => $entity->get($primaryKey)]);
        $this->assertTrue($query->isEmpty());
    }

    public function testSchema(): void
    {
        $this->get('/api/things/schema');
        $this->assertJsonResponseOk();

        $response = json_decode((string)$this->_response->getBody(), true);

        // Check label
        $this->assertEquals('label name', $response['data']['fields'][1]['label']);

        // Check two fields for money and metric
        $expected = [
            [
                'name' => 'testmetric_amount',
                'type' => 'metric',
                'db_type' => 'decimal',
            ],
            [
                'name' => 'testmetric_unit',
                'type' => 'metric',
                'db_type' => 'string',
            ],
            [
                'name' => 'testmoney_amount',
                'type' => 'money',
                'db_type' => 'decimal',
            ],
            [
                'name' => 'testmoney_currency',
                'type' => 'money',
                'db_type' => 'string',
            ],
            [
                'name' => 'test_list',
                'type' => 'sublist',
                'db_type' => 'string',
                'options' => [
                    [
                        'label' => 'first',
                        'children' => [
                            [
                                'label' => 'first children',
                                'value' => 'first.first_children',
                            ],
                            [
                                'label' => 'second children',
                                'value' => 'first.second_children',
                            ],
                        ],
                        'value' => 'first',
                    ],
                    [
                        'label' => 'second',
                        'value' => 'second',
                    ],
                ],
            ],
        ];

        $this->assertSame([], array_diff(Hash::flatten($expected), Hash::flatten($response['data']['fields'])));
    }

    /**
     * Modules provider.
     *
     * @return mixed[]
     */
    public function modulesProvider(): array
    {
        // store default path
        $defaultPath = Configure::read('CsvMigrations.modules.path');

        Configure::write('CsvMigrations.modules.path', CONFIG . 'Modules' . DS);

        $modules = [];
        foreach ((new Folder(App::path('Controller/Api/V1/V0')[0]))->find('^\w+Controller\.php$') as $file) {
            array_push($modules, basename($file, 'Controller.php'));
        }

        $modules = array_filter($modules, [$this, 'isModule']);
        $modules = array_filter($modules, [$this, 'isActive']);

        // restore default path
        Configure::write('CsvMigrations.modules.path', $defaultPath);

        return array_map(function ($module) {
            return [$module];
        }, $modules);
    }

    private function isModule(string $name): bool
    {
        $config = (new ModuleConfig(ConfigType::MIGRATION(), $name, '', ['cacheSkip' => true]))->parse();
        $config = json_encode($config);
        $config = false !== $config ? json_decode($config, true) : [];

        return ! empty($config);
    }

    private function isActive(string $module): bool
    {
        $feature = Factory::get('Module' . DS . $module);

        return $feature->isActive();
    }
}
