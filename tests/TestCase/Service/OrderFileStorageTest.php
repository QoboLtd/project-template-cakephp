<?php
namespace App\Test\TestCase\Service;

use App\Service\OrderFileStorage;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

class OrderFileStorageTest extends TestCase
{
    public $fixtures = [
        'app.Things',
        'app.Users',
        'app.FileStorage',
    ];

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testOrderFiles(): void
    {
        $table = TableRegistry::getTableLocator()->get('FileStorage');

        $files = $table->find()->all();

        $filesToOrder = [
            [
                'key' => 'file-storage-1',
            ],
            [
                'key' => 'file-storage-2',
            ],
            [
                'key' => 'file-storage-3',
            ],
            [
                'key' => 'file-storage-4',
            ],
        ];

        $expected = [
            'file-storage-1' => 0,
            'file-storage-2' => 1,
            'file-storage-3' => 2,
            'file-storage-4' => 3,
        ];

        OrderFileStorage::orderFiles($filesToOrder);

        $files = $table->find()->all();
        $sortedFiles = [];
        foreach ($files as $file) {
            $sortedFiles[$file->id] = $file->order;
        }

        $this->assertSame($expected, $sortedFiles);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedException \ErrorException
     */
    public function testOrderFilesFailed(): void
    {
        $table = TableRegistry::getTableLocator()->get('FileStorage');

        $table->find()->all();

        $filesToOrder = [];

        OrderFileStorage::orderFiles($filesToOrder);

        $table->find()->all();
    }
}
