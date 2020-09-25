<?php

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         1.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Test\TestCase\Controller;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * PagesControllerTest class
 */
class SettingsControllerTest extends IntegrationTestCase
{
    /**
     * Settings Table
     *
     * @var \App\Model\Table\SettingsTable $settings
     */
    public $settings;

    public $fixtures = [
        'app.Settings',
        'app.Users',
        'plugin.Groups.GroupsUsers',
        'plugin.Groups.Groups',
        'plugin.RolesCapabilities.Capabilities',
        'plugin.RolesCapabilities.GroupsRoles',
        'plugin.RolesCapabilities.Permissions',
        'plugin.RolesCapabilities.Roles',
        'plugin.Search.Dashboards',
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->enableCsrfToken();
        $this->enableSecurityToken();

        TableRegistry::clear();
        /**
         * @var \App\Model\Table\SettingsTable $table
         */
        $table = TableRegistry::getTableLocator()->get('Settings');
        $this->settings = $table;

        $userId = '00000000-0000-0000-0000-000000000001';
        $this->session([
            'Auth' => [
                'User' => TableRegistry::get('Users')->get($userId)->toArray(),
            ],
        ]);
    }

    /**
     * testUserMenuOrder method
     *
     * @return void
     */
    public function testUserMenuOrder(): void
    {
        $dashboardsOrder = [
            ['id' => '00000000-0000-0000-0000-000000000002', 'order' => 0],
            ['id' => '00000000-0000-0000-0000-000000000001', 'order' => 1],
        ];

        $expected = [
            'id' => 6,
            'key' => 'Menu.dashboard_menu_order_value',
            'value' => json_encode($dashboardsOrder),
            'scope' => 'user',
            'context' => '00000000-0000-0000-0000-000000000001',
        ];

        $data = [
            'Settings' => ['Menu.dashboard_menu_order_value' => json_encode($dashboardsOrder)],
        ];

        $this->post('/settings/my/dashboard', $data);
        $settings = $this->settings->get(6)->toArray();

        $this->assertEquals($expected, $settings);
    }

    /**
     * testAppMenuOrder method
     *
     * @return void
     */
    public function testAppMenuOrder(): void
    {
        $dashboardsOrder = [
            ['id' => '00000000-0000-0000-0000-000000000002', 'order' => 0],
            ['id' => '00000000-0000-0000-0000-000000000001', 'order' => 1],
        ];

        $expected = [
            'id' => 7,
            'key' => 'Menu.dashboard_menu_order_value',
            'value' => json_encode($dashboardsOrder),
            'scope' => 'app',
            'context' => 'app',
        ];

        $data = [
            'Settings' => ['Menu.dashboard_menu_order_value' => json_encode($dashboardsOrder)],
        ];

        $this->post('/settings/app/dashboard', $data);
        $settings = $this->settings->get(7)->toArray();

        $this->assertEquals($expected, $settings);
    }
}
