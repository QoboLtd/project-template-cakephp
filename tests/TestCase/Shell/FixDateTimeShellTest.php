<?php

namespace App\Test\TestCase\Shell;

use App\Shell\FixDateTimeShell;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Webmozart\Assert\Assert;

class FixDateTimeShellTest extends TestCase
{
    public $fixtures = [
        'app.things',
        'app.log_audit',
        'app.DateTimeFix'
    ];

    private $table;
    private $primaryKey;

    public function setUp(): void
    {
        parent::setUp();

        $this->table = TableRegistry::get('Things');
        $this->primaryKey = $this->table->getPrimaryKey();
        Assert::string($this->primaryKey);
    }

    public function tearDown(): void
    {
        unset($this->table);

        parent::tearDown();
    }

    public function testUpdateFields(): void
    {
        $entity = $this->table->deleteAll([]);
        $data = [
            'name' => 'Sample Date change',
            'email' => 'sampledatenew@thing.com',
            'description' => 'Long description goes here',
            'created' => '2018-01-18 15:47:16',
            'modified' => '2018-01-18 15:47:16',
            'sample_date' => '2018-01-18 15:47:16',
            'created_by' => '00000000-0000-0000-0000-000000000001',
            'modified_by' => '00000000-0000-0000-0000-000000000001',
            'trashed' => null
        ];
        $entity = $this->table->newEntity($data);
        $this->table->save($entity);
        $entity = $this->table->get($entity->get($this->primaryKey));

        $shell = new FixDateTimeShell();
        $shell->setModule('Things');
        $shell->setTimezoneFrom('Asia/Nicosia');
        $shell->setTimezoneTo('UTC');
        $shell->setLimit('1');
        $shell->updateFields('Things');

        $entity = $this->table->get($entity->get($this->primaryKey));

        $this->assertSame('2018-01-18 13:47:16', $entity->get('sample_date')->format('Y-m-d H:i:s'));

        //Verify that if we run the shell for second time the data will not be changed twice
        $shell = new FixDateTimeShell();
        $shell->setModule('Things');
        $shell->setTimezoneFrom('Asia/Nicosia');
        $shell->setTimezoneTo('UTC');
        $shell->setLimit('1');
        $shell->updateFields('Things');

        $entity = $this->table->get($entity->get($this->primaryKey));

        $this->assertSame('2018-01-18 13:47:16', $entity->get('sample_date')->format('Y-m-d H:i:s'));
    }
}
