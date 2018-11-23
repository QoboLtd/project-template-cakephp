<?php
namespace App\Test\TestCase\Shell;

use App\Shell\CronShell;
use Cake\Console\Shell;
use Cake\TestSuite\ConsoleIntegrationTestCase;

/**
 * App\Shell\CronShell Test Case
 */
class CronShellTest extends ConsoleIntegrationTestCase
{

    public $fixtures = [
        'app.users',
        'app.scheduled_jobs',
        'app.scheduled_job_logs',
    ];

    /**
     * Test subject
     *
     * @var \App\Shell\CronShell
     */
    public $CronShell;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        /** @var \Cake\Console\ConsoleIo */
        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        $this->CronShell = new CronShell($io);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->CronShell);

        parent::tearDown();
    }

    /**
     * Test main method
     *
     * @return void
     */
    public function testMain(): void
    {
        $this->exec('cron');
        $this->assertExitCode(Shell::CODE_SUCCESS);
    }

    /**
     * @dataProvider fileAndClassNamesProvider
     * @param string $file File name
     * @param mixed $class Class name
     * @param string $normalized Normalized name
     * @return void
     */
    public function testLock(string $file, $class, string $normalized) : void
    {
        $this->exec(sprintf('cron lock %s %s', $file, $class));

        $expected = sprintf('%s%s_%s.lock.lock', sys_get_temp_dir() . DS, $normalized, md5($file));
        $this->assertTrue(file_exists($expected));
    }

    /**
     * @return mixed[]
     */
    public function fileAndClassNamesProvider() : array
    {
        return [
            ['foo', 'bar', 'bar'],
            ['foo1', 'bar1', 'bar1'],
            ['foo_1234', 'bar__1', 'bar__1'],
            ['foo\1234', 'b*a@r!5', 'b_a_r_5'],
            ['foos', null, ''],
            ['foos', '', ''],
            ['foos', '1', '1']
        ];
    }
}
