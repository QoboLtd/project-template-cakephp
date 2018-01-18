<?php
namespace App\Test\TestCase\Shell;

use App\Shell\CronShell;
use Cake\TestSuite\TestCase;

/**
 * App\Shell\CronShell Test Case
 */
class CronShellTest extends TestCase
{

    /**
     * ConsoleIo mock
     *
     * @var \Cake\Console\ConsoleIo|\PHPUnit_Framework_MockObject_MockObject
     */
    public $io;

    /**
     * Test subject
     *
     * @var \App\Shell\CronShell
     */
    public $Cron;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();
        $this->Cron = new CronShell($this->io);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Cron);

        parent::tearDown();
    }

    /**
     * Test getOptionParser method
     *
     * @return void
     */
    public function testGetOptionParser()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test main method
     *
     * @return void
     */
    public function testMain()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
