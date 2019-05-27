<?php
namespace App\Test\TestCase\Settings;

use App\Model\Table\SettingsTable;
use App\Settings\DbConfig;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class DbConfigTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Cake\ORM\Table
     */
    public $Settings;

    /**
     * $configure instance of DbConfig()
     * @var \App\Settings\DbConfig
     */
    public $configure;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.settings'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Settings') ? [] : ['className' => SettingsTable::class];
        $this->Settings = TableRegistry::get('Settings', $config);
        $this->configure = new DbConfig();
    }

    /**
     * testGetArray
     * @return void
     */
    public function testGetArray(): void
    {
        $array = $this->configure->read('Settings');
        $this->assertInternalType('array', $array);
    }

    /**
     * testGetEmptyArray
     * @return void
     */
    public function testGetEmptyArray(): void
    {
        $array = $this->configure->read('SettingsWrong');
        $this->assertEquals([], $array);
    }
}