<?php

namespace App\Test\TestCase;

use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\TestSuite\TestCase;

class AppTest extends TestCase
{
    /**
     * @dataProvider pluginProvider
     * @param string $plugin Plugin name
     * @param mixed $config Plugin config
     */
    public function testLoadedPlugins(string $plugin, $config): void
    {
        if (empty($config)) {
            $this->assertTrue((bool)Plugin::isLoaded($plugin), 'Plugin ' . $plugin . ' is not loaded');

            return;
        }

        $enabled = false;
        switch (gettype($config)) {
            case 'string':
                $enabled = Configure::read($config);
                break;

            case 'array':
                foreach ($config as $conf) {
                    if (!Configure::read($conf)) {
                        $enabled = false;
                        break;
                    }

                    $enabled = true;
                }
                break;
        }

        $message = 'Plugin ' . $plugin . ' is not loaded but [' . implode(' or ', (array)$config) . '] is true';
        $this->assertEquals($enabled, Plugin::isLoaded($plugin), $message);
    }

    /**
     * @return mixed[] Plugins and settings
     */
    public function pluginProvider(): array
    {
        return [
            ['ADmad/JwtAuth', null],
            ['AdminLTE', null],
            ['Alt3/Swagger', 'Swagger.crawl'],
            ['AuditStash', null],
            ['Burzum/FileStorage', null],
            ['CakeDC/Users', null],
            ['Crud', null],
            ['CsvMigrations', null],
            ['DatabaseLog', null],
            ['Groups', null],
            ['Menu', null],
            ['Migrations', null],
            ['Qobo/Utils', null],
            ['RolesCapabilities', null],
            ['Search', null],
            ['Translations', null],
        ];
    }
}
