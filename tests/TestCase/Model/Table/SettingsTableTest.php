<?php

namespace App\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * App\Model\Table\SettingsTable Test Case
 */
class SettingsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\SettingsTable $settings
     */
    public $settings;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.Settings',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        TableRegistry::getTableLocator()->clear();
        /**
         * @var \App\Model\Table\SettingsTable $table
         */
        $table = TableRegistry::getTableLocator()->get('Settings');

        $this->settings = $table;
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->settings);

        parent::tearDown();
    }

    /**
     * testCreateEntityNoKey Test Create a new entity with invalid key
     * @return void
     */
    public function testCreateEntityNoKey(): void
    {
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->settings->createEntity('invalid.key', '1234', 'integer', 'app', 'app');
    }

    /**
     * testCreateEntityExistRecord Test the creation a duplicate entity
     * @return void
     */
    public function testCreateEntityExistRecord(): void
    {
        /**
         * @var \Cake\ORM\Entity $existEntity
         */
        $existEntity = $this->settings->createEntity('FileStorage.defaultImageSize', '1234', 'integer', 'app', 'app');
        $this->assertEquals(false, $existEntity->isNew());
    }

    /**
     * testCreateEntityNew Test the creation of a new valid entity
     * @return void
     */
    public function testCreateEntityNew(): void
    {
        $params = [
            'key' => 'FileStorage.defaultImageSize',
            'value' => '1234',
            'scope' => 'user',
            'context' => 'bb697cd7-c869-491d-8696-805b1af8c08f',
            'type' => 'integer',
        ];

        $entity = $this->settings->newEntity($params);
        $myEntity = $this->settings->createEntity('FileStorage.defaultImageSize', '1234', 'integer', 'user', 'bb697cd7-c869-491d-8696-805b1af8c08f');

        $this->assertEquals($entity, $myEntity);

        // test list type
        $params = [
            'key' => 'FileStorage.defaultImageSize',
            'value' => 'large',
            'scope' => 'user',
            'context' => 'bb697cd7-c869-491d-8696-805b1af8c08f',
            'type' => 'string',
        ];

        $entity = $this->settings->newEntity($params);
        $myEntity = $this->settings->createEntity('FileStorage.defaultImageSize', 'large', 'list', 'user', 'bb697cd7-c869-491d-8696-805b1af8c08f');

        $this->assertEquals($entity, $myEntity);
    }

    /**
     * testCreateEntityPatch Test patch entity
     * @return void
     */
    public function testCreateEntityPatch(): void
    {
        $params = [
            'key' => 'ScheduledLog.stats.age',
            'value' => 'my NEW value',
            'scope' => 'user',
            'context' => 'bb697cd7-c869-491d-8696-805b1af8c08f',
            'type' => 'string',
        ];
        /**
         * @var \Cake\Datasource\EntityInterface $oldEntity
         */
        $oldEntity = $this->settings->find('all')->where(['key' => 'ScheduledLog.stats.age'])->first();
        $this->settings->patchEntity($oldEntity, $params);
        $oldEntityValues = [
            'key' => 'ScheduledLog.stats.age',
            'value' => $oldEntity['value'],
            'scope' => $oldEntity['scope'],
            'context' => $oldEntity['context'],
            'type' => $oldEntity['type'],
        ];

        $this->assertEquals($params, $oldEntityValues);
    }

    /**
     * testFilterSettings Test FilterSettings method
     * @return void
     */
    public function testFilterSettings(): void
    {
        $userRoles = ['settings'];

        $configSettings = [
         'N Tab' => [
           'Another Column' => [
             'This section 1' => [
               'name1' => [
                 'alias' => 'FileStorage.defaultImageSize',
                 'type' => 'string',
                 'scope' => ['Everyone', 'settings', 'anotherRole'],
               ],
               'name2' => [
                 'alias' => 'Avatar.defaultImage',
                 'type' => 'string',
                 'scope' => ['Everyone', 'anotherRole'],
               ],
             ],
           ],
         ],
        ];

        $configSettingsFilter = [
         'N Tab' => [
           'Another Column' => [
             'This section 1' => [
               'name1' => [
                 'alias' => 'FileStorage.defaultImageSize',
                 'type' => 'string',
                 'scope' => ['Everyone', 'settings', 'anotherRole'],
               ],
             ],
           ],
         ],
        ];

        $filterData = $this->settings->filterSettings($configSettings, $userRoles);
        $this->assertEquals($configSettingsFilter, $filterData);
    }

    /**
     * testFilterDataException Test wrong configuration
     * @return void
     */
    public function testFilterDataException(): void
    {
        $userRoles = ['settings'];
        // Wrong configuration
        $configSettings = [
         'N Tab' => [
           'This section 1' => [
             'name1' => [
               'alias' => 'FileStorage.defaultImageSize',
               'type' => 'string',
               'scope' => ['Everyone', 'settings', 'anotherRole'],
             ],
           ],
         ],
        ];

        $this->expectException('\RuntimeException');
        $this->settings->filterSettings($configSettings, $userRoles);
    }

    /**
     * testUpdateValidationNoErrors Test valid configuration
     * @return void
     */
    public function testUpdateValidationNoErrors(): void
    {
        $key = 'FileStorage.defaultImageSize';
        $entity = $this->settings->find('all')->where(['key' => $key])->firstOrFail();
        if (is_array($entity)) {
            return;
        }
        $params = [
            'key' => $key,
            'value' => '300',
            'type' => 'integer', // dynamic field to pass type to the validator
        ];
        $newEntity = $this->settings->patchEntity($entity, $params);
        $this->assertEmpty($newEntity->getErrors());
    }

    /**
     * testUpdateValidationWithErrors Test wrong validation
     * @return void
     */
    public function testUpdateValidationWithErrors(): void
    {
        $key = 'FileStorage.defaultImageSize';
        $entity = TableRegistry::getTableLocator()->get('Settings')->find('all')->where(['key' => $key])->first();
        $params = [
            'key' => $key,
            'value' => 'wrong value',
            'type' => 'integer', // dynamic field to pass type to the validator
        ];
        if (is_array($entity) || is_null($entity)) {
            return;
        }
        $newEntity = $this->settings->patchEntity($entity, $params);
        $this->assertEquals('The provided value is invalid', $newEntity->getErrors()['value']['custom']);
    }

    /**
     * testGetAlias Test getAliasDiff method
     * @return void
     */
    public function testGetAlias(): void
    {
        $configSettings = [
         'N Tab' => [
           'Another Column' => [
             'This section 1' => [
               'name2' => [
                 'alias' => 'FileStorage.defaultImageSize',
                 'type' => 'string',
               ],
             ],
           ],
         ],
        ];

        $alias = (array)Hash::extract($configSettings, '{s}.{s}.{s}.{s}.alias');
        $this->assertEmpty($this->settings->getAliasDiff($alias));
    }

    /**
     * testGetNewRecord getAliasDiff method
     * @return void
     */
    public function testGetNewRecord(): void
    {
        $configSettings = [
         'N Tab' => [
           'Another Column' => [
             'This section 1' => [
               'name2' => [
                 'alias' => 'FileStorage.defaultImageSize',
                 'type' => 'string',
               ],
             ],
            ],
          ],
          'N Tab1' => [
           'Another Column' => [
             'This section 1' => [
               'name3' => [
                 'alias' => 'Avatar.defaultImage',
                 'type' => 'string',
               ],
             ],
           ],
          ],
        ];

        $alias = (array)Hash::extract($configSettings, '{s}.{s}.{s}.{s}.alias');
        $this->assertEquals(['Avatar.defaultImage'], $this->settings->getAliasDiff($alias));
    }

    /**
     * testGetException Test exception
     * @return void
     */
    public function testGetException(): void
    {
        $configSettings = [
         'N Tab 1' => [
           'Another Column' => [
             'This section 1' => [
               'name2' => [
                 'alias' => 'FileStorage.defaultImageSize',
                 'type' => 'string',
               ],
             ],
           ],
         ],
          'N Tab' => [
           'Another Column' => [
             'This section 1' => [
               'name3' => [
                 'alias' => 'Pizza.with.pinapple',
                 'type' => 'string',
               ],
             ],
           ],
          ],
        ];

        $alias = (array)Hash::extract($configSettings, '{s}.{s}.{s}.{s}.alias');
        $this->expectException('RuntimeException');
        $this->settings->getAliasDiff($alias);
    }

    /**
     * testContextValidationValidUser Test valid user
     * @return void
     */
    public function testContextValidationValidUser(): void
    {
        $key = 'FileStorage.defaultImageSize';
        $entity = TableRegistry::getTableLocator()->get('Settings')->find('all')->where(['key' => $key])->first();
        $params = [
            'key' => 'FileStorage.defaultImageSize',
            'value' => '1234',
            'scope' => 'user',
            'context' => 'bb697cd7-c869-491d-8696-805b1af8c08f',
            'type' => 'integer',
        ];
        if (is_array($entity) || is_null($entity)) {
            return;
        }
        $newEntity = $this->settings->patchEntity($entity, $params);
        $this->assertEquals([], $newEntity->getErrors());
    }

    /**
     * testContextValidationErrorUser Test unvalid user
     * @return void
     */
    public function testContextValidationErrorUser(): void
    {
        $key = 'FileStorage.defaultImageSize';
        $entity = TableRegistry::getTableLocator()->get('Settings')->find('all')->where(['key' => $key])->first();
        $params = [
            'key' => 'FileStorage.defaultImageSize',
            'value' => '1234',
            'scope' => 'user',
            'context' => 'not a UUID !',
            'type' => 'integer',
        ];
        if (is_array($entity) || is_null($entity)) {
            return;
        }
        $newEntity = $this->settings->patchEntity($entity, $params);
        $this->assertEquals('The provided value is invalid', $newEntity->getErrors()['context']['custom']);
    }

    /**
     * testContextValidationValidApp
     * @return void
     */
    public function testContextValidationValidApp(): void
    {
        $key = 'FileStorage.defaultImageSize';
        $entity = TableRegistry::getTableLocator()->get('Settings')->find('all')->where(['key' => $key])->first();
        $params = [
            'key' => 'FileStorage.defaultImageSize',
            'value' => '1234',
            'scope' => 'app',
            'context' => 'app',
            'type' => 'integer',
        ];
        if (is_array($entity) || is_null($entity)) {
            return;
        }
        $newEntity = $this->settings->patchEntity($entity, $params);
        $this->assertEquals([], $newEntity->getErrors());
    }

    /**
     * testContextValidationErrorApp
     * @return void
     */
    public function testContextValidationErrorApp(): void
    {
        $key = 'FileStorage.defaultImageSize';
        $entity = TableRegistry::getTableLocator()->get('Settings')->find('all')->where(['key' => $key])->first();
        $params = [
            'key' => 'FileStorage.defaultImageSize',
            'value' => '1234',
            'scope' => 'app',
            'context' => 'not app string',
            'type' => 'integer',
        ];
        if (is_array($entity) || is_null($entity)) {
            return;
        }
        $newEntity = $this->settings->patchEntity($entity, $params);
        $this->assertEquals('The provided value is invalid', $newEntity->getErrors()['context']['custom']);
    }

    public function testFindDataApp(): void
    {
        $options = ['value' => 'huge'];

        $list = $this->settings->findDataApp($this->settings->query(), $options);

        $this->assertSame(['FileStorage.defaultImageSize' => $options['value']], $list);
    }
}
