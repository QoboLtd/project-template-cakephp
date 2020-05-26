<?php

namespace App\Crud\Action;

use ArrayIterator;
use Cake\Core\App;
use Crud\Action\BaseAction;
use CsvMigrations\FieldHandlers\CsvField;
use Qobo\Utils\Module\ModuleRegistry;

/**
 * Handles 'Schema' Crud actions
 *
 */
class SchemaAction extends BaseAction
{
    /**
     * Default settings
     *
     * @var array
     */
    protected $_defaultConfig = [
        'enabled' => true,
        'api' => [
            'success' => [
                'code' => 200,
            ],
            'error' => [
                'code' => 400,
            ],
        ],
    ];

    /**
     * Only GET allowed on HTTP verbs
     *
     * @return void
     */
    protected function _get(): void
    {
        $data_fields = [];
        $data_association = $this->getAssociations($this->_table()->associations()->getIterator());

        $data_fields = $this->getFields($data_association);
        $subject = $this->_subject(['success' => true]);

        $this->_controller()->set('data', ['fields' => $data_fields, 'associations' => $data_association]);
        $this->_trigger('beforeRender', $subject);
    }

    /**
     * Models fields
     *
     * @param  mixed[] $associations Table associations
     * @return mixed[] custum data
     */
    protected function getFields(array $associations): array
    {
        $module = ModuleRegistry::getModule($this->_controller()->getName());
        $migration = $module->getMigration();
        $fields = $module->getFields();
        $db_fields_type = $this->_table()->getSchema()->typeMap();

        $data_fields = [];
        foreach ($migration as $field) {
            $csvField = new CsvField($field);
            $data = [
                'name' => $csvField->getName(),
                'type' => $csvField->getType(),
                'required' => $csvField->getRequired(),
            ];
            // Check if exist a label, or required, non_searchable, unique are set as true.
            !empty($fields[$csvField->getName()]['label']) ? $data['label'] = $fields[$csvField->getName()]['label'] : '';
            $csvField->getNonSearchable() ? $data['non_searchable'] = true : '';
            $csvField->getUnique() ? $data['unique'] = true : '';

            switch ($csvField->getType()) {
                case "metric":
                    $amount = $data;
                    $amount['name'] = $amount['name'] . '_amount';
                    $amount['db_type'] = 'decimal';
                    $data_fields[] = $amount;
                    $data['name'] = $data['name'] . '_unit';
                    $data['db_type'] = 'string';
                    break;
                case "money":
                    $amount = $data;
                    $amount['name'] = $amount['name'] . '_amount';
                    $amount['db_type'] = 'decimal';
                    $data_fields[] = $amount;
                    $data['name'] = $data['name'] . '_currency';
                    $data['db_type'] = 'string';
                    break;
                case "list":
                case "sublist":
                    list($moduleName, $listName) = false !== strpos((string)$csvField->getLimit(), '.') ?
                        explode('.', (string)$csvField->getLimit(), 2) :
                        [$this->_controller()->getName(), (string)$csvField->getLimit()];
                    $list = ModuleRegistry::getModule($moduleName)->getList($listName);
                    $data['db_type'] = $db_fields_type[$csvField->getName()];
                    $data['options'] = $this->getOptionList($list['items']);
                    break;
                case "related":
                    $data['db_type'] = $db_fields_type[$csvField->getName()];
                    $data['association'] = $this->findAssociation($associations, $csvField->getName());
                    break;
                default:
                    $data['db_type'] = $db_fields_type[$csvField->getName()];
            }
            $data_fields[] = $data;
        }

        return $data_fields;
    }

    /**
     * Link the association name to the related field
     *
     * @param  mixed[]  $associations Custum array with associations data
     * @param  string $foreign_key Filed name
     * @return string|null Association name
     */
    private function findAssociation(array $associations, string $foreign_key): ?string
    {
        foreach ($associations as $key => $value) {
            if ($value['foreign_key'] === $foreign_key) {
                return $value['name'];
            }
        }

        return null;
    }

    /**
     * Table associations
     *
     * @param  ArrayIterator $associations table associations
     * @return mixed[] custum data array
     */
    private function getAssociations(ArrayIterator $associations): array
    {
        $data_association = [];

        foreach ($associations as $association) {
            $data_association[] = [
                'name' => $association->getName(),
                'model' => App::shortName(get_class($association->getTarget()), 'Model/Table', 'Table'),
                'type' => $association->type(),
                'primary_key' => $association->getBindingKey(),
                'foreign_key' => $association->getForeignKey(),
            ];
        }

        return $data_association;
    }

    /**
     * Option list
     * e.i. :
     * "options": [{
     *         "label": "Label 1",
     *         "value": "value_1"
     *         "children": [{
     *                 "label": "Label chindren 1",
     *                 "value": "value_chindren_1"
     *             }
     *         ],
     *     },
     *     {
     *         "label": "Label 2",
     *         "value": "value_2"
     *     }
     * ]
     *
     * @param  mixed[] $data input data
     * @return mixed[] array with no keys.
     */
    private function getOptionList(array $data): array
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (!empty($value['children'])) {
                $value['children'] = $this->getOptionList($value['children']);
            }

            if (!empty($value['inactive']) && $value['inactive']) {
                continue;
            }

            unset($value['inactive']);
            $value['value'] = $key;
            $result[] = $value;
        }

        return $result;
    }
}
