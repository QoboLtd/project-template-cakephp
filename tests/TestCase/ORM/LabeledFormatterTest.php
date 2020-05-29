<?php

namespace App\Test\TestCase\ORM;

use App\ORM\LabeledFormatter;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class LabeledFormatterTest extends TestCase
{
    public $fixtures = [
        'app.LogAudit',
        'app.Things',
        'app.Users',
    ];

    private $table;

    public function setUp(): void
    {
        parent::setUp();

        $this->table = TableRegistry::getTableLocator()->get('Things');
    }

    public function tearDown(): void
    {
        unset($this->table);

        parent::tearDown();
    }

    public function testFormatResults(): void
    {
        $expected = [
            'appointment' => '2019-10-29 15:47:16',
            'area_amount' => 25.74,
            'area_unit' => 'm²',
            'assigned_to' => 'user second',
            'bio' => 'A blob type',
            'country' => '<span class="flag-icon flag-icon-cy flag-icon-default"></span>&nbsp;&nbsp;Cyprus',
            'created' => '2018-01-18 15:47:16',
            'created_by' => 'first1 last1',
            'currency' => '<span title="United Kingdom Pound">£&nbsp;(GBP)</span>',
            'date_of_birth' => '1990-01-17',
            'description' => 'Long description goes here',
            'email' => '1@thing.com',
            'file' => null,
            'gender' => 'Male',
            'id' => '00000000-0000-0000-0000-000000000001',
            'language' => 'Ancient Greek',
            'level' => 7,
            'modified' => '2018-01-18 15:47:16',
            'modified_by' => 'first1 last1',
            'name' => 'Thing #1',
            'non_searchable' => '',
            'phone' => '+35725123456',
            'photos' => null,
            'primary_thing' => 'Thing #2',
            'rate' => 25.13,
            'salary_amount' => 1000.0,
            'salary_currency' => '<span title="Euro">€&nbsp;(EUR)</span>',
            'test_list' => 'first - second children',
            'testmetric_amount' => 33.18,
            'testmetric_unit' => 'ft²',
            'testmoney_amount' => 155.22,
            'testmoney_currency' => '<span title="United States Dollar">&#36;&nbsp;(USD)</span>',
            'title' => 'Dr',
            'trashed' => null,
            'vip' => true,
            'website' => 'https://google.com',
            'work_start' => '08:32',
        ];

        $query = $this->table
            ->find()
            ->where(['Things.id' => '00000000-0000-0000-0000-000000000001'])
            ->formatResults(new LabeledFormatter());

        $result = $query->first()->toArray();

        foreach ($expected as $key => $value) {
            $this->assertSame($value, $result[$key]);
        }
    }

    public function testFormatResultsWithContain(): void
    {
        $expected = [
            'appointment' => '2019-10-29 15:47:16',
            'area_amount' => 25.74,
            'area_unit' => 'm²',
            'assigned_to' => 'user second',
            'assigned_to_user' => [
                'activation_date' => '2015-06-24 17:33:54',
                'active' => true,
                'additional_data' => null,
                'birthdate' => null,
                'company' => null,
                'country' => null,
                'created' => '2015-06-24 17:33:54',
                'department' => null,
                'email' => 'user-2@test.com',
                'extras' => null,
                'fax' => null,
                'first_name' => 'user',
                'gender' => null,
                'id' => '00000000-0000-0000-0000-000000000002',
                'image' => null,
                'image_src' => '/uploads/avatars/00000000-0000-0000-0000-000000000002.png',
                'initials' => null,
                'is_admin' => true,
                'is_superuser' => true,
                'is_supervisor' => false,
                'last_name' => 'second',
                'modified' => '2015-06-24 17:33:54',
                'name' => 'user second',
                'phone_extension' => null,
                'phone_home' => null,
                'phone_mobile' => null,
                'phone_office' => null,
                'position' => null,
                'reports_to' => null,
                'role' => 'admin',
                'secret' => 'xxx',
                'secret_verified' => false,
                'team' => null,
                'tos_date' => '2015-06-24 17:33:54',
                'trashed' => null,
                'username' => 'user-2',
            ],
            'bio' => 'A blob type',
            'country' => '<span class="flag-icon flag-icon-cy flag-icon-default"></span>&nbsp;&nbsp;Cyprus',
            'created' => '2018-01-18 15:47:16',
            'created_by' => 'first1 last1',
            'currency' => '<span title="United Kingdom Pound">£&nbsp;(GBP)</span>',
            'date_of_birth' => '1990-01-17',
            'description' => 'Long description goes here',
            'email' => '1@thing.com',
            'file' => null,
            'gender' => 'Male',
            'id' => '00000000-0000-0000-0000-000000000001',
            'language' => 'Ancient Greek',
            'level' => 7,
            'modified' => '2018-01-18 15:47:16',
            'modified_by' => 'first1 last1',
            'name' => 'Thing #1',
            'non_searchable' => '',
            'phone' => '+35725123456',
            'photos' => null,
            'primary_thing' => 'Thing #2',
            'rate' => 25.13,
            'salary_amount' => 1000.0,
            'salary_currency' => '<span title="Euro">€&nbsp;(EUR)</span>',
            'test_list' => 'first - second children',
            'testmetric_amount' => 33.18,
            'testmetric_unit' => 'ft²',
            'testmoney_amount' => 155.22,
            'testmoney_currency' => '<span title="United States Dollar">&#36;&nbsp;(USD)</span>',
            'title' => 'Dr',
            'trashed' => null,
            'vip' => true,
            'website' => 'https://google.com',
            'work_start' => '08:32',
        ];

        $query = $this->table
            ->find()
            ->where(['Things.id' => '00000000-0000-0000-0000-000000000001'])
            ->contain('AssignedToUsers')
            ->formatResults(new LabeledFormatter());

        $result = $query->first()->toArray();

        foreach ($expected as $key => $value) {
            if ('assigned_to_user' !== $key) {
                $this->assertSame($value, $result[$key]);
            }
        }

        foreach ($expected['assigned_to_user'] as $key => $value) {
            $this->assertSame($value, $result['assigned_to_user'][$key]);
        }
    }

    public function testFormatResultsWithMatching(): void
    {
        $expected = [
            '_matchingData' => [
                'AssignedToUsers' => [
                    'activation_date' => '2015-06-24 17:33:54',
                    'active' => true,
                    'additional_data' => null,
                    'birthdate' => null,
                    'company' => null,
                    'country' => null,
                    'created' => '2015-06-24 17:33:54',
                    'department' => null,
                    'email' => 'user-2@test.com',
                    'extras' => null,
                    'fax' => null,
                    'first_name' => 'user',
                    'gender' => null,
                    'id' => '00000000-0000-0000-0000-000000000002',
                    'image' => null,
                    'image_src' => '/uploads/avatars/00000000-0000-0000-0000-000000000002.png',
                    'initials' => null,
                    'is_admin' => true,
                    'is_superuser' => true,
                    'is_supervisor' => false,
                    'last_name' => 'second',
                    'modified' => '2015-06-24 17:33:54',
                    'name' => 'user second',
                    'phone_extension' => null,
                    'phone_home' => null,
                    'phone_mobile' => null,
                    'phone_office' => null,
                    'position' => null,
                    'reports_to' => null,
                    'role' => 'admin',
                    'secret' => 'xxx',
                    'secret_verified' => false,
                    'team' => null,
                    'tos_date' => '2015-06-24 17:33:54',
                    'trashed' => null,
                    'username' => 'user-2',
                ],
            ],
            'appointment' => '2019-10-29 15:47:16',
            'area_amount' => 25.74,
            'area_unit' => 'm²',
            'assigned_to' => 'user second',
            'bio' => 'A blob type',
            'country' => '<span class="flag-icon flag-icon-cy flag-icon-default"></span>&nbsp;&nbsp;Cyprus',
            'created' => '2018-01-18 15:47:16',
            'created_by' => 'first1 last1',
            'currency' => '<span title="United Kingdom Pound">£&nbsp;(GBP)</span>',
            'date_of_birth' => '1990-01-17',
            'description' => 'Long description goes here',
            'email' => '1@thing.com',
            'file' => null,
            'gender' => 'Male',
            'id' => '00000000-0000-0000-0000-000000000001',
            'language' => 'Ancient Greek',
            'level' => 7,
            'modified' => '2018-01-18 15:47:16',
            'modified_by' => 'first1 last1',
            'name' => 'Thing #1',
            'non_searchable' => '',
            'phone' => '+35725123456',
            'photos' => null,
            'primary_thing' => 'Thing #2',
            'rate' => 25.13,
            'salary_amount' => 1000.0,
            'salary_currency' => '<span title="Euro">€&nbsp;(EUR)</span>',
            'test_list' => 'first - second children',
            'testmetric_amount' => 33.18,
            'testmetric_unit' => 'ft²',
            'testmoney_amount' => 155.22,
            'testmoney_currency' => '<span title="United States Dollar">&#36;&nbsp;(USD)</span>',
            'title' => 'Dr',
            'trashed' => null,
            'vip' => true,
            'website' => 'https://google.com',
            'work_start' => '08:32',
        ];

        $query = $this->table
            ->find()
            ->where(['Things.id' => '00000000-0000-0000-0000-000000000001'])
            ->matching('AssignedToUsers')
            ->formatResults(new LabeledFormatter());

        $result = $query->first()->toArray();

        foreach ($expected as $key => $value) {
            if ('_matchingData' !== $key) {
                $this->assertSame($value, $result[$key]);
            }
        }

        foreach ($expected['_matchingData']['AssignedToUsers'] as $key => $value) {
            $this->assertSame($value, $result['_matchingData']['AssignedToUsers'][$key]);
        }
    }

    public function testFormatResultsWithPermissions(): void
    {
        $query = $this->table
            ->find()
            ->where(['Things.id' => '00000000-0000-0000-0000-000000000001'])
            ->formatResults(new \App\ORM\PermissionsFormatter())
            ->formatResults(new LabeledFormatter());

        $keys = array_keys($query->first()->toArray());
        $this->assertTrue(in_array('_permissions', $keys, true));
    }

    public function testFormatResultsWithEmptyRelatedField(): void
    {
        // remove related field value
        $thing = $this->table->get('00000000-0000-0000-0000-000000000001');
        $thing->set('primary_thing', 'a-non-uuid-string');
        $this->table->saveOrFail($thing);

        $query = $this->table
            ->find()
            ->where(['Things.id' => '00000000-0000-0000-0000-000000000001'])
            ->formatResults(new LabeledFormatter());

        $this->assertSame('a-non-uuid-string', $query->first()->get('primary_thing'));
    }

    public function testFormatResultsWithNestedRelationAsDisplayField(): void
    {
        // adjust association's target table display-field to a foreign-key
        $this->table->getAssociation('Thingsprimary_thing')
            ->getTarget()
            ->setDisplayField('assigned_to');

        $query = $this->table
            ->find()
            ->where(['Things.id' => '00000000-0000-0000-0000-000000000001'])
            ->formatResults(new LabeledFormatter());

        $this->assertSame('user second', $query->first()->get('primary_thing'));
    }
}
