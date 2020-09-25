<?php

namespace App\Test\TestCase\Controller;

use App\Feature\Factory;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;
use Cake\Utility\Inflector;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;
use Webmozart\Assert\Assert;

class BaseModuleControllerTest extends IntegrationTestCase
{
    public $fixtures = [
        'plugin.CsvMigrations.Dblists',
        'plugin.CsvMigrations.DblistItems',
        'plugin.Menu.Menus',
        'plugin.Menu.MenuItems',
        'app.FileStorage',
        'app.LogAudit',
        'app.SavedSearches',
        'app.Things',
        'app.Users',
    ];

    public function setUp()
    {
        parent::setUp();

        $this->disableErrorHandlerMiddleware();

        $this->session([
            'Auth' => [
                'User' => TableRegistry::getTableLocator()->get('Users')->get('00000000-0000-0000-0000-000000000002')->toArray(),
            ],
        ]);
    }

    /**
     * @dataProvider modulesProvider
     */
    public function testIndex(string $module): void
    {
        $this->get('/' . Inflector::dasherize($module));

        $this->assertResponseOk();
        $this->assertContentType('text/html');
        $this->assertResponseContains('<table-ajax');
    }

    /**
     * @dataProvider modulesProvider
     */
    public function testView(string $module): void
    {
        $table = TableRegistry::getTableLocator()->get($module);
        $entity = $table->find()->firstOrFail();
        Assert::isInstanceOf($entity, \Cake\Datasource\EntityInterface::class);

        $primaryKey = $table->getPrimaryKey();
        Assert::string($primaryKey);

        $this->get('/' . Inflector::dasherize($module) . '/view/' . $entity->get($primaryKey));

        $this->assertResponseCode(200);
        $this->assertContentType('text/html');
        $this->assertResponseContains(sprintf('<h4><a href="/%s">', Inflector::dasherize($module)));
    }

    /**
     * @dataProvider modulesProvider
     */
    public function testAddGetRequest(string $module): void
    {
        $this->get('/' . Inflector::dasherize($module) . '/add/');

        $this->assertResponseCode(200);
        $this->assertContentType('text/html');
        $this->assertResponseContains(sprintf('<h4><a href="/%s">', Inflector::dasherize($module)));
    }

    /**
     * @dataProvider modulesProvider
     */
    public function testEditGetRequest(string $module): void
    {
        $table = TableRegistry::getTableLocator()->get($module);
        $entity = $table->find()->firstOrFail();
        Assert::isInstanceOf($entity, \Cake\Datasource\EntityInterface::class);

        $primaryKey = $table->getPrimaryKey();
        Assert::string($primaryKey);

        $this->get('/' . Inflector::dasherize($module) . '/edit/' . $entity->get($primaryKey));

        $this->assertResponseCode(200);
        $this->assertContentType('text/html');
        $this->assertResponseContains(sprintf('<h4><a href="/%s">', Inflector::dasherize($module)));
    }

    /**
     * @dataProvider modulesProvider
     */
    public function testDelete(string $module): void
    {
        $this->enableCsrfToken();
        $this->enableSecurityToken();

        $table = TableRegistry::getTableLocator()->get($module);
        $entity = $table->find()->firstOrFail();
        Assert::isInstanceOf($entity, \Cake\Datasource\EntityInterface::class);

        $primaryKey = $table->getPrimaryKey();
        Assert::string($primaryKey);

        $this->delete('/' . Inflector::dasherize($module) . '/delete/' . $entity->get($primaryKey));

        $this->assertRedirect();
        $this->assertContentType('text/html');

        $query = $table->find()->where([$primaryKey => $entity->get($primaryKey)]);
        $this->assertTrue($query->isEmpty());
    }

    /**
     * Modules provider.
     *
     * @return mixed[]
     */
    public function modulesProvider(): array
    {
        // return [['Accounts']];
        // store default path
        $defaultPath = Configure::read('CsvMigrations.modules.path');

        Configure::write('CsvMigrations.modules.path', CONFIG . 'Modules' . DS);

        $modules = [];
        foreach ((new Folder(App::path('Controller')[0]))->find('^\w+Controller\.php$') as $file) {
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
        if (in_array($name, ['DynamicTemplateMessages', 'ScheduledJobLogs'], true)) {
            return false;
        }

        $config = (new ModuleConfig(ConfigType::MIGRATION(), $name, '', ['cacheSkip' => true]))->parseToArray();

        return [] !== $config;
    }

    private function isActive(string $module): bool
    {
        $feature = Factory::get('Module' . DS . $module);

        return $feature->isActive();
    }
}
