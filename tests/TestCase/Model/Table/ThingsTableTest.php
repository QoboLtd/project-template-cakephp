<?php

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ThingsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Validation\Validator;
use Webmozart\Assert\Assert;

class ThingsTableTest extends TestCase
{
    private $table;

    public $fixtures = [
        'app.things',
        'app.log_audit',
    ];

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

    public function testInitialize(): void
    {
        $this->assertInstanceOf(ThingsTable::class, $this->table);
        $this->assertSame('things', $this->table->getTable());
        $this->assertSame('id', $this->table->getPrimaryKey());
        $this->assertSame(true, $this->table->hasBehavior('Timestamp'));
    }

    public function testValidationDefault(): void
    {
        $validator = $this->table->validationDefault(new Validator());

        $entity = $this->table->newEntity(['email' => '1@thing.com', 'gender' => 'm', 'country' => 'CY', 'currency' => 'EUR', 'phone' => '33331']);
        $this->assertSame([], $entity->getErrors());

        $this->table->save($entity);
        $this->assertNotNull($entity->get('id'));
    }
}
