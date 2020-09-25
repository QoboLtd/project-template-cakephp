<?php

namespace App\Test\TestCase\Model\Table;

use App\Model\Entity\ScheduledJobLog;
use App\Model\Table\ScheduledJobLogsTable;
use Cake\I18n\Time;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Text;
use Cake\Validation\Validator;

class ScheduledJobLogsTableTest extends TestCase
{
    public $fixtures = [
        'app.ScheduledJobs',
        'app.ScheduledJobLogs',
    ];

    private $table;

    public function setUp(): void
    {
        parent::setUp();

        $this->table = TableRegistry::getTableLocator()->get('ScheduledJobLogs');
    }

    public function tearDown(): void
    {
        unset($this->table);

        parent::tearDown();
    }

    public function testInitialize(): void
    {
        $this->assertInstanceOf(ScheduledJobLogsTable::class, $this->table);
        $this->assertSame('scheduled_job_logs', $this->table->getTable());
        $this->assertSame('id', $this->table->getPrimaryKey());
        $this->assertSame('context', $this->table->getDisplayField());
        $this->assertSame(true, $this->table->hasBehavior('Timestamp'));

        $association = $this->table->getAssociation('ScheduledJobs');
        $this->assertInstanceOf(BelongsTo::class, $association);
        $this->assertSame('scheduled_job_id', $association->getForeignKey());
        $this->assertSame('INNER', $association->getJoinType());
    }

    public function testValidationDefault(): void
    {
        $validator = $this->table->validationDefault(new Validator());

        $this->assertSame(true, $validator->hasField('id'));
        $this->assertSame(true, $validator->hasField('context'));
        $this->assertSame(true, $validator->hasField('status'));
        $this->assertSame(true, $validator->hasField('datetime'));

        $this->assertSame('create', $validator->field('id')->isEmptyAllowed());
        $this->assertSame(true, $validator->field('id')->offsetExists('uuid'));
        $this->assertSame(false, $validator->field('id')->isPresenceRequired());

        $this->assertSame(true, $validator->field('context')->isEmptyAllowed());
        $this->assertSame(false, $validator->field('context')->isPresenceRequired());
        $this->assertSame(true, $validator->field('context')->offsetExists('scalar'));
        $this->assertSame(true, $validator->field('context')->offsetExists('maxLength'));
        $this->assertSame([255], $validator->field('context')->offsetGet('maxLength')->get('pass'));

        $this->assertSame(true, $validator->field('status')->isEmptyAllowed());
        $this->assertSame(false, $validator->field('status')->isPresenceRequired());
        $this->assertSame(true, $validator->field('status')->offsetExists('scalar'));
        $this->assertSame(true, $validator->field('status')->offsetExists('maxLength'));
        $this->assertSame([255], $validator->field('status')->offsetGet('maxLength')->get('pass'));

        $this->assertSame(false, $validator->field('datetime')->isEmptyAllowed());
        $this->assertSame('create', $validator->field('datetime')->isPresenceRequired());
        $this->assertSame(true, $validator->field('datetime')->offsetExists('dateTime'));

        $entity = $this->table->newEntity(['datetime' => new \DateTime()]);
        $this->assertSame([], $entity->getErrors());

        $this->table->save($entity);
        $this->assertNotNull($entity->get('id'));
    }

    public function testBuildRules(): void
    {
        $table = TableRegistry::getTableLocator()->get('ScheduledJobs');
        $scheduledJob = $table->newEntity(['start_date' => new \Cake\I18n\Time()]);
        $table->save($scheduledJob);

        $entity = $this->table->newEntity(['scheduled_job_id' => $scheduledJob->get('id')], ['validate' => false]);
        $this->table->save($entity);
        $this->assertNotNull($entity->get('id'));
    }

    public function testBuildRulesWithInvalidScheduledJobId(): void
    {
        $entity = $this->table->newEntity(['scheduled_job_id' => Text::uuid()], ['validate' => false]);
        $this->assertSame(false, $this->table->save($entity));
    }

    public function testLogJob(): void
    {
        $table = TableRegistry::getTableLocator()->get('ScheduledJobs');
        $time = new Time();
        $state = ['state' => 'foobar'];

        $entity = $table->newEntity(['start_date' => $time]);
        $table->save($entity);

        $result = $this->table->logJob($entity, $state, $time);

        $this->assertInstanceOf(ScheduledJobLog::class, $result);
        $this->assertSame($entity->get('id'), $result->get('scheduled_job_id'));
        $this->assertSame(null, $result->get('context'));
        $this->assertSame('foobar', $result->get('status'));
        $this->assertSame($time->i18nFormat('yyyy-mm-dd HH:mm:00'), $result->get('datetime'));
        $this->assertSame(json_encode($state), $result->get('extra'));
    }

    public function testLogJobWithMissingState(): void
    {
        $table = TableRegistry::getTableLocator()->get('ScheduledJobs');
        $entity = $table->newEntity(['start_date' => new Time()]);
        $table->save($entity);

        $result = $this->table->logJob($entity, ['state' => 'foobar'], new Time());

        $this->assertInstanceOf(ScheduledJobLog::class, $result);
        $this->assertSame($entity->get('id'), $result->get('scheduled_job_id'));
    }
}
