<?php
namespace App\Event\Controller\Api;

use App\Event\EventName;
use Cake\Datasource\QueryInterface;
use Cake\Datasource\RepositoryInterface;
use Cake\Datasource\ResultSetDecorator;
use Cake\Event\Event;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use CsvMigrations\FieldHandlers\CsvField;
use CsvMigrations\FieldHandlers\FieldHandlerFactory;
use CsvMigrations\FieldHandlers\RelatedFieldTrait;
use CsvMigrations\FieldHandlers\Setting;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;

class LookupActionListener extends BaseActionListener
{
    use RelatedFieldTrait;

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            (string)EventName::API_LOOKUP_BEFORE_FIND() => 'beforeLookup',
            (string)EventName::API_LOOKUP_AFTER_FIND() => 'afterLookup'
        ];
    }

    /**
     * Add conditions to Lookup Query.
     *
     * @param \Cake\Event\Event $event Event instance
     * @param \Cake\Datasource\QueryInterface $query ORM Query
     * @return void
     */
    public function beforeLookup(Event $event, QueryInterface $query): void
    {
        /**
         * @var \Cake\Controller\Controller $controller
         */
        $controller = $event->getSubject();
        $request = $controller->getRequest();

        $table = $controller->loadModel();
        $this->_alterQuery($table, $query, $request);
    }

    /**
     * Alters lookup query and adds ORDER BY clause, WHERE clause
     * if a query string is passed and typeahead fields are defined.
     *
     * Also it adds table JOIN if parent modules are defined.
     *
     * Additionally if any of the defined typeahead fields is a related
     * one, then the WHERE clause condition changes from LIKE to IN and
     * includes the related module's UUIDs that matched the query string.
     *
     * NOTE: There are recursive calls between this method and _getRelatedModuleValues().
     *
     * @param \Cake\Datasource\RepositoryInterface $table Table instance
     * @param \Cake\Datasource\QueryInterface $query Query object
     * @param \Cake\Http\ServerRequest $request Request object
     * @return void
     */
    protected function _alterQuery(RepositoryInterface $table, QueryInterface $query, ServerRequest $request): void
    {
        $fields = $this->_getTypeaheadFields($table);
        /** @var \Cake\ORM\Query $query */
        $query = $query;
        $query->order($this->_getOrderByFields($table, $query, $fields));

        $this->_joinParentTables($table, $query);

        if (empty($fields)) {
            return;
        }

        $value = Hash::get($request->getQueryParams(), 'query', false);

        if (! $value) {
            return;
        }

        // add typeahead fields to where clause
        foreach ($fields as $field) {
            $csvField = $this->_getCsvField($field, $table);
            if (!empty($csvField) && 'related' === $csvField->getType()) {
                $values = $this->_getRelatedModuleValues($csvField, $request);
                $query->orWhere([$field . ' IN' => $values]);
            } else {
                // always type-cast fields to string for LIKE clause to work.
                // otherwise for cases where type is integer LIKE value '%123%' will be converted to '0'
                /** @var array $typeMap */
                $typeMap = array_combine($fields, array_pad([], count($fields), 'string'));
                $query->setTypeMap($typeMap);
                $query->orWhere([$field . ' LIKE' => '%' . $value . '%']);
            }
        }
    }

    /**
     * Instantiates and returns a CsvField object of the provided field.
     *
     * @param string $field Field name
     * @param \Cake\Datasource\RepositoryInterface $table Table instance
     * @return \CsvMigrations\FieldHandlers\CsvField|null
     */
    protected function _getCsvField(string $field, RepositoryInterface $table): ?CsvField
    {
        $result = null;
        if (false !== strpos($field, '.')) {
            list(, $field) = explode('.', $field);
        }

        if (empty($field)) {
            return $result;
        }

        $method = 'getFieldsDefinitions';
        if (!method_exists($table, $method) || !is_callable([$table, $method])) {
            return $result;
        }

        $fieldsDefinitions = $table->{$method}();
        if (empty($fieldsDefinitions[$field])) {
            return $result;
        }

        return new CsvField($fieldsDefinitions[$field]);
    }

    /**
     * Returns related module UUIDs matching the query string.
     *
     * NOTE: There are recursive calls between this method and _alterQuery().
     *
     * @param \CsvMigrations\FieldHandlers\CsvField $csvField CsvField instance
     * @param \Cake\Http\ServerRequest $request Request object
     * @return mixed[]
     */
    protected function _getRelatedModuleValues(CsvField $csvField, ServerRequest $request): array
    {
        $table = TableRegistry::get((string)$csvField->getLimit());
        $query = $table->find('list', [
            'keyField' => $table->primaryKey()
        ]);

        // recursive call
        $this->_alterQuery($table, $query, $request);

        $result = $query->toArray();

        $result = !empty($result) ? array_keys($result) : [null];

        return $result;
    }

    /**
     * Modify lookup entities after they have been fetched from the database
     *
     * @param \Cake\Event\Event $event Event instance
     * @param \Cake\Datasource\ResultSetDecorator $entities Entities resultset
     * @return void
     */
    public function afterLookup(Event $event, ResultSetDecorator $entities): void
    {
        /**
         * @var \Cake\Controller\Controller $controller
         */
        $controller = $event->getSubject();

        if ($entities->isEmpty()) {
            return;
        }
        /**
         * @var \Cake\ORM\Table $table
         */
        $table = $controller->loadModel();

        // Properly populate display values for the found entries.
        // This will recurse into related modules and get display
        // values as deep as needed.
        $fhf = new FieldHandlerFactory();
        foreach ($entities as $id => $label) {
            $event->result[$id] = $fhf->renderValue(
                $table,
                $table->getDisplayField(),
                $label,
                ['renderAs' => Setting::RENDER_PLAIN_VALUE_RELATED()]
            );
        }

        $parentModule = $this->_getParentModule($table);
        if ('' === $parentModule) {
            return;
        }

        foreach ($event->result as $id => &$label) {
            $label = $this->_prependParentModule($table->registryAlias(), $parentModule, $id, $label);
        }
    }

    /**
     * Get module's virtual fields.
     *
     * @param \Cake\Datasource\RepositoryInterface $table Table instance
     * @return mixed[]
     */
    protected function _getVirtualFields(RepositoryInterface $table): array
    {
        $config = (new ModuleConfig(ConfigType::MODULE(), $table->getRegistryAlias()))->parse();
        $config = json_encode($config);
        $config = false !== $config ? json_decode($config, true) : [];

        return array_key_exists('virtualFields', $config) ? $config['virtualFields'] : [];
    }

    /**
     * Updates the provided list of mixed real and virtual fields, so that the final list includes only real fields.
     * This is done by taking into consideration the corresponding section in config.json
     *
     * @param \Cake\Datasource\RepositoryInterface $table Table instance
     * @param mixed[] $fields List of mixed real and virtual fields
     * @return mixed[]
     */
    private function extractVirtualFields(RepositoryInterface $table, array $fields): array
    {
        $virtualFields = $this->_getVirtualFields($table);

        $extractedFields = [];
        foreach ($fields as $fieldName) {
            if (array_key_exists($fieldName, $virtualFields)) {
                $extractedFields = array_merge($extractedFields, $virtualFields[$fieldName]);
            } else {
                $extractedFields[] = $fieldName;
            }
        }

        return array_unique($extractedFields);
    }

    /**
     * Get module's type-ahead fields.
     *
     * @param \Cake\Datasource\RepositoryInterface $table Table instance
     * @return mixed[]
     */
    protected function _getTypeaheadFields(RepositoryInterface $table): array
    {
        $config = (new ModuleConfig(ConfigType::MODULE(), $table->getRegistryAlias()))->parse();
        /** @var \Cake\ORM\Table $table */
        $table = $table;

        $fields = ! empty($config->table->typeahead_fields) ?
            $config->table->typeahead_fields :
            [$table->getDisplayField()];

        // Extract the virtual fields to actual db fields before asking for an alias
        $fields = $this->extractVirtualFields($table, $fields);
        foreach ($fields as $k => $v) {
            $fields[$k] = $table->aliasField($v);
        }

        return $fields;
    }

    /**
     * Get order by fields for lookup Query.
     *
     * @param \Cake\Datasource\RepositoryInterface $table Table instance
     * @param \Cake\Datasource\QueryInterface $query ORM Query
     * @param mixed[] $fields Optional fields to be used in order by clause
     * @return mixed[]
     */
    protected function _getOrderByFields(RepositoryInterface $table, QueryInterface $query, array $fields = []): array
    {
        $parentModule = $this->_getParentModule($table);
        if ('' === $parentModule) {
            return $this->extractVirtualFields($table, $fields);
        }

        $parentAssociation = null;
        /**
         * @var \Cake\ORM\Table $table
         */
        $table = $table;
        foreach ($table->associations() as $association) {
            if ($association->className() !== $parentModule) {
                continue;
            }
            $parentAssociation = $association;
            break;
        }

        if (is_null($parentAssociation)) {
            return $this->extractVirtualFields($table, $fields);
        }

        $targetTable = $parentAssociation->target();

        // add parent display field to order-by fields
        array_unshift($fields, $targetTable->aliasField($targetTable->getDisplayField()));

        $fields = $this->_getOrderByFields($targetTable, $query, $fields);

        return $this->extractVirtualFields($table, $fields);
    }

    /**
     * Join parent modules.
     *
     * @param \Cake\Datasource\RepositoryInterface $table Table instance
     * @param \Cake\Datasource\QueryInterface $query ORM Query
     * @return void
     */
    protected function _joinParentTables(RepositoryInterface $table, QueryInterface $query): void
    {
        $parentModule = $this->_getParentModule($table);
        if ('' === $parentModule) {
            return;
        }

        $parentAssociation = null;
        /**
         * @var \Cake\ORM\Table $table
         */
        $table = $table;
        foreach ($table->associations() as $association) {
            if ($association->className() !== $parentModule) {
                continue;
            }
            $parentAssociation = $association;
            break;
        }

        if (is_null($parentAssociation)) {
            return;
        }
        /**
         * @var string $parentForeignKey
         */
        $parentForeignKey = $parentAssociation->getForeignKey();
        /**
         * @var string $parentPrimaryKey
         */
        $parentPrimaryKey = $parentAssociation->getTarget()->getPrimaryKey();

        $targetTable = $parentAssociation->getTarget();
        $primaryKey = $targetTable->aliasField($parentPrimaryKey);
        $foreignKey = $table->aliasField($parentForeignKey);

        /** @var \Cake\ORM\Query $query */
        $query = $query;
        $query->join([
            'table' => $targetTable->table(),
            'alias' => $parentAssociation->name(),
            'type' => 'INNER',
            'conditions' => $foreignKey . ' = ' . $primaryKey . ' OR ' . $foreignKey . ' IS NULL'
        ]);

        $this->_joinParentTables($targetTable, $query);
    }

    /**
     * Returns parent module name for provided Table instance.
     * If parent module is not defined then it returns null.
     *
     * @param \Cake\Datasource\RepositoryInterface $table Table instance
     * @return string
     */
    protected function _getParentModule(RepositoryInterface $table): string
    {
        $config = (new ModuleConfig(ConfigType::MODULE(), $table->getRegistryAlias()))->parse();

        return isset($config->parent->module) ? $config->parent->module : '';
    }

    /**
     * Prepend parent module display field to label.
     *
     * @param string $tableName Table name
     * @param string $parentModule Parent module name
     * @param string $id uuid
     * @param string $label Label
     * @return string
     */
    protected function _prependParentModule(string $tableName, string $parentModule, string $id, string $label): string
    {
        $properties = $this->_getRelatedParentProperties(
            $this->_getRelatedProperties($tableName, $id)
        );

        if (empty($properties['dispFieldVal'])) {
            return $label;
        }

        $prefix = $properties['dispFieldVal'] . ' ' . $this->_separator . ' ';

        if (empty($properties['config']['parent']['module']) || empty($properties['id'])) {
            return $prefix . $label;
        }

        $prefix = $this->_prependParentModule(
            $parentModule,
            $properties['config']['parent']['module'],
            $properties['id'],
            $prefix
        );

        return $prefix . $label;
    }
}
