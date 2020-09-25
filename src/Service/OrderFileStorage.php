<?php

namespace App\Service;

use Cake\ORM\TableRegistry;
use InvalidArgumentException;

final class OrderFileStorage
{
    /**
     * Order files
     *
     * @param  mixed[] $files Files to order
     * @throws \RuntimeException RuntimeException.
     * @return void
     */
    public static function orderFiles(array $files): void
    {
        if (empty($files)) {
            throw new InvalidArgumentException('There was an error updating file order');
        }

        $table = TableRegistry::getTableLocator()->get('FileStorage');
        foreach ($files as $order => $fileDetails) {
            if (!isset($fileDetails['key']) || empty($fileDetails['key'])) {
                continue;
            }

            $file = $table->get($fileDetails['key']);
            $file->set('order', $order);
            $table->saveOrFail($file);
        }
    }
}
