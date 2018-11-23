<?php
namespace App\Controller\Api\V1\V0;

use Cake\Event\Event;
use Cake\Utility\Hash;

class LanguageTranslationsController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public function index()
    {
        $this->Crud->on('beforePaginate', function (Event $event) {
            if (! property_exists($event->getSubject(), 'query')) {
                return;
            }

            $query = $event->getSubject()->query;

            $params = $this->request->getQueryParams();

            if (Hash::get($params, 'object_model') && Hash::get($params, 'object_foreign_key')) {
                /**
                 * @var \App\Model\Table\LanguageTranslationsTable $table
                 */
                $table = $this->loadModel();
                $conditions = [
                    'object_model' => Hash::get($params, 'object_model'),
                    'object_foreign_key' => Hash::get($params, 'object_foreign_key'),
                ];

                if (Hash::get($params, 'object_field')) {
                    $conditions['object_field'] = Hash::get($params, 'object_field');
                }

                if (Hash::get($params, 'language')) {
                    $conditions['language_id'] = $table->getLanguageId(Hash::get($params, 'language'));
                }

                $query->applyOptions(['conditions' => $conditions]);
                $query->applyOptions(['contain' => ['Languages']]);
                $query->applyOptions(['fields' => [
                    $table->aliasField('translation'),
                    $table->aliasField('object_model'),
                    $table->aliasField('object_foreign_key'),
                    $table->aliasField('object_field'),
                    'Languages.code'
                ]]);
            } else {
                // In case of missing params to return empty dataset instead of all records
                $query->applyOptions(['conditions' => ['id' => null]]);
            }
        });

        return $this->Crud->execute();
    }
}
