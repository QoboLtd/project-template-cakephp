<?php

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\ScheduledJobsTable;
use Cake\Datasource\EntityInterface;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\ScheduledJobsTable Test Case
 */
class ScheduledJobsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\ScheduledJobsTable
     */
    public $ScheduledJobsTable;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.log_audit',
        'app.scheduled_jobs'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('ScheduledJobs') ? [] : ['className' => ScheduledJobsTable::class];
        /**
         * @var \App\Model\Table\ScheduledJobsTable $table
         */
        $table = TableRegistry::get('ScheduledJobs', $config);
        $this->ScheduledJobsTable = $table;
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->ScheduledJobsTable);

        parent::tearDown();
    }

    public function testGetJobs(): void
    {
        $result = $this->ScheduledJobsTable->getJobs(3);
        $this->assertNotEmpty($result);
    }

    public function testGetInstance(): void
    {
        $result = $this->ScheduledJobsTable->getInstance(null, null);
        $this->assertNull($result);

        $result = $this->ScheduledJobsTable->getInstance('CakeShell::App:clean_modules_data', 'Handler');
        $this->assertInstanceOf('\App\ScheduledJobs\Handlers\CakeShellHandler', $result);
    }

    public function testGetList(): void
    {
        $result = $this->ScheduledJobsTable->getList();

        $this->assertTrue(is_array($result));
        $this->assertNotEmpty($result);
    }

    /**
     * @dataProvider providerTestIsValidFile
     */
    public function testIsValidFile(string $file, bool $expected): void
    {
        $result = $this->ScheduledJobsTable->isValidFile($file);

        $this->assertEquals($result, $expected);
    }

    /**
     * Provide files data set
     *
     * @return mixed[]
     */
    public function providerTestIsValidFile(): array
    {
        return [
            ['foobar.php', true],
            ['foo.bar', false],
        ];
    }

    public function testTimeToInvoke(): void
    {
        $time = new Time('2018-01-18 09:00:00', 'UTC');

        // in 1st scenario, the rule will be executed at the beginning of each hour.
        // Due to that dtstart won't fall in condition.
        $dtstart = new \DateTime('2018-01-18 08:10:00', new \DateTimeZone('UTC'));
        $dtstartString = $dtstart->format('Y-m-d H:i:s');
        $rrule = new \RRule\RRule(['FREQ' => 'HOURLY', 'DTSTART' => $dtstartString]);

        $result = $this->ScheduledJobsTable->timeToInvoke($time, $rrule);
        $this->assertFalse($result);

        // in 2nd scenario, the rrule will be executed every minute, as
        // previous time/date vars match FREQ condition.
        $rrule2 = new \RRule\RRule(['FREQ' => 'MINUTELY', 'DTSTART' => $dtstartString]);
        $result = $this->ScheduledJobsTable->timeToInvoke($time, $rrule2);
        $this->assertTrue($result);

        unset($rrule2);
        unset($rrule);
    }

    /**
     * @dataProvider providerGetRRule
     */
    public function testGetRRule(string $id, string $expected): void
    {
        $entity = $this->ScheduledJobsTable->get($id);

        $result = $this->ScheduledJobsTable->getRRule($entity);

        if (empty($expected)) {
            $this->assertEquals($result, $expected);
        } else {
            $this->assertInstanceOf($expected, $result);
        }
    }

    public function testGetRRuleWithoutRecurrence(): void
    {
        $entity = $this->ScheduledJobsTable->newEntity();

        $result = $this->ScheduledJobsTable->getRRule($entity);
        $this->assertSame(null, $result);
    }

    /**
     * Return RRule data sets
     *
     * @return mixed[]
     */
    public function providerGetRRule(): array
    {
        return [
            ['00000000-0000-0000-0000-000000000001', '\RRule\RRule'],
            ['00000000-0000-0000-0000-000000000002', '\RRule\RRule'],
        ];
    }

    public function testBeforeSave(): void
    {
        $startDate = new Time('+1 year');
        $expected = $startDate->format('YmdHi');

        $entity = $this->ScheduledJobsTable->newEntity(['start_date' => $startDate]);
        $result = $this->ScheduledJobsTable->save($entity);

        $this->assertInstanceOf(EntityInterface::class, $result);
        $this->assertSame('00', $entity->get('start_date')->format('s'));
        $this->assertSame($expected, $entity->get('start_date')->format('YmdHi'));
    }

    public function testGetStartDate(): void
    {
        $now = new Time();

        $startDate = $this->ScheduledJobsTable->getStartDate($now);

        $this->assertInstanceOf(Time::class, $startDate);
        $this->assertSame('00', $startDate->format('s'));
    }

    public function testGetStartDateWithString(): void
    {
        $startDate = $this->ScheduledJobsTable->getStartDate('+1 year');

        $this->assertInstanceOf(Time::class, $startDate);
        $this->assertSame('00', $startDate->format('s'));
    }
}
