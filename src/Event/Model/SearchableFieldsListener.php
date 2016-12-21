<?php
namespace App\Event\Model;

use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\Table;
use Cake\Utility\Inflector;
use CsvMigrations\FieldHandlers\CsvField;
use CsvMigrations\FieldHandlers\DbField;
use CsvMigrations\FieldHandlers\FieldHandlerFactory;

class SearchableFieldsListener implements EventListenerInterface
{
    /**
     * Searchable parameter name.
     */
    const PARAM_NON_SEARCHABLE = 'non-searchable';

    /**
     * Field handler factory instance.
     *
     * @var \CsvMigrations\FieldHandlers\FieldHandlerFactory
     */
    protected $_fhf;

    /**
     * Skipped field types.
     *
     * @var array
     */
    protected $_skipTypes = ['uuid'];

    protected $_combinedFields = [
        'money',
        'metric'
    ];

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            'Search.Model.Search.searchabeFields' => 'getSearchableFields'
        ];
    }

    /**
     * Method that retrieves target table searchable fields.
     *
     * @param \Cake\Event\Event $event Event instance
     * @param \Cake\ORM\Table $table Table instance
     * @return void
     */
    public function getSearchableFields(Event $event, Table $table)
    {
        $method = 'getFieldsDefinitions';
        // skip if method cannot be called
        if (!method_exists($table, $method) || !is_callable([$table, $method])) {
            return;
        }

        $this->_fhf = new FieldHandlerFactory();

        $result = [];
        foreach ($table->{$method}($table->alias()) as $field) {
            // skip non-searchable fields and fields with type defined in _skipTypes array
            if ($field[static::PARAM_NON_SEARCHABLE] || in_array($field['type'], $this->_skipTypes)) {
                continue;
            }
            $csvField = new CsvField($field);

            // skip non-searchable fields
            if ($csvField->getNonSearchable()) {
                continue;
            }

            // get field type
            $type = $csvField->getType();
            if (!$type) {
                continue;
            }

            if (!in_array($type, $this->_combinedFields)) {
                $properties = $this->_getFieldProperties($table, $csvField);
            } else {
                $properties = $this->_getCombinedFieldProperties($table, $csvField);
            }

            if (empty($properties)) {
                continue;
            }

            $result = array_merge($result, $properties);
        }

        $event->result = $result;
    }

    /**
     * Get searchable field properties.
     *
     * @param  \Cake\ORM\Table $table Table instance
     * @param  \CsvMigrations\FieldHandlers\CsvField $csvField CsvField instance
     * @return array
     */
    protected function _getFieldProperties(Table $table, CsvField $csvField)
    {
        // get field type
        $type = $csvField->getType();
        if (!$type) {
            return [];
        }

        // get field name
        $name = $csvField->getName();
        if (!$name) {
            return [];
        }

        // get search input
        $input = $this->_fhf->renderSearchInput($table, $name);
        if (!$input) {
            return [];
        }

        // get search operators
        $operators = $this->_fhf->getSearchOperators($table, $name);
        if (!$operators) {
            return [];
        }

        // get field label
        $label = $this->_fhf->getSearchLabel($table, $name);
        if (empty($label)) {
            $label = Inflector::humanize($name);
        }

        $result[$name] = [
            'type' => $type,
            'label' => $label,
            'operators' => $operators,
            'input' => $input
        ];

        return $result;
    }

    /**
     * Get searchable field properties.
     *
     * @param  \Cake\ORM\Table $table Table instance
     * @param  \CsvMigrations\FieldHandlers\CsvField $csvField CsvField instance
     * @return array
     */
    protected function _getCombinedFieldProperties(Table $table, CsvField $csvField)
    {
        $result = [];
        // get field type
        $type = $csvField->getType();
        if (!$type) {
            return $result;
        }

        // get csv field name
        $name = $csvField->getName();
        if (!$name) {
            return $result;
        }
        // get combined field search inputs
        $inputs = $this->_fhf->renderSearchInput($table, $name);
        if (!$inputs) {
            return $result;
        }

        // get combined field search operators
        $operators = $this->_fhf->getSearchOperators($table, $name);
        if (!$operators) {
            return $result;
        }

        // get combined field labels
        $labels = $this->_fhf->getSearchLabel($table, $name);

        $dbFields = $this->_fhf->fieldToDb($csvField);

        // get field names from DbField objects
        foreach ($dbFields as $dbField) {
            // get db field name
            $name = $dbField->getName();
            if (!$name || empty($operators[$name]) || empty($inputs[$name])) {
                continue;
            }

            $result[$name] = [
                'type' => $type,
                'label' => !empty($labels) ? $labels[$name] : Inflector::humanize($name),
                'operators' => $operators[$name],
                'input' => $inputs[$name]
            ];
        }

        return $result;
    }

    /**
     * Method that retrieves list field searchable properties.
     *
     * @param \Cake\ORM\Table $table Table instance
     * @param \CsvMigrations\FieldHandlers\CsvField $csvField CsvField instance
     * @param \CsvMigrations\FieldHandlers\DbField $dbField DbField instance
     * @return array
     */
    protected function _getListProperties(Table $table, CsvField $csvField, DbField $dbField)
    {
        return [
            'type' => $csvField->getType(),
            'fieldOptions' => $table->_getSelectOptions($csvField->getLimit())
        ];
    }
}
