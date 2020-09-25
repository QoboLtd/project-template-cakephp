<?php

namespace App\Controller\Api\V1\V0;

use App\Service\OrderFileStorage;

class FileStorageController extends AppController
{
    /**
     * Update File Order function shared among API controllers
     *
     * @return \Cake\Http\Response|void|null
     */
    public function order()
    {
        $this->request->allowMethod(['ajax', 'post']);

        $data = (array)$this->request->getData();

        try {
            OrderFileStorage::orderFiles($data);

            $result = [
                'success' => true,
                'data' => [],
                'message' => __('File order has been changed'),
            ];
        } catch (\InvalidArgumentException | \ErrorException $e) {
            $result = [
                'success' => false,
                'data' => [],
                'message' => __($e->getMessage()),
            ];
        }

        $this->set('result', $result);
        $this->set('_serialize', 'result');
    }
}
