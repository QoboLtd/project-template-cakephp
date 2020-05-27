<?php

namespace App\Model\Behavior;

use ArrayObject;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Association;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use CsvMigrations\Exception\UnsupportedPrimaryKeyException;
use Qobo\Utils\Module\ModuleRegistry;
use Webmozart\Assert\Assert;

/**
 * Lookup behavior
 */
class LookupBehavior extends Behavior
{
    protected $lookupFields = [];

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        if (!empty($this->getConfig('lookupFields'))) {
            Assert::isArray($this->getConfig('lookupFields'));
            $this->lookupFields = $this->getConfig('lookupFields');
        }
    }

    /**
     * Apply lookup fields to Query's where clause.
     *
     * @param \Cake\Event\Event $event Event object
     * @param \Cake\ORM\Query $query Query object
     * @param \ArrayObject $options Query options
     * @param bool $primary Primary Standalone Query flag
     * @return void
     */
    public function beforeFind(Event $event, Query $query, ArrayObject $options, bool $primary): void
    {
        $table = $event->getSubject();
        Assert::isInstanceOf($table, Table::class);

        if (! $primary) {
            return;
        }

        if (! isset($options['lookup']) || ! $options['lookup']) {
            return;
        }

        if (! isset($options['value'])) {
            return;
        }

        // fail-safe binding of primary key to query's where clause, if lookup
        // fields are not defined, to avoid random record retrieval.
        if (empty($this->lookupFields)) {
            $primaryKey = $table->getPrimaryKey();
            if (! is_string($primaryKey)) {
                throw new UnsupportedPrimaryKeyException();
            }

            $query->where([
                $table->aliasField($primaryKey) => $options['value'],
            ]);

            return;
        }

        $conditions = [];
        if ($query->clause('where') !== null) {
            $conditions[] = $query->clause('where');
        }

        foreach ($this->lookupFields as $field) {
            $value = $this->castValueByFieldType($options['value'], (string)$table->getSchema()->getColumnType($field));
            // cast value back to string and do strict comparison,
            // skip lookup field if cast value does not match original
            if ((string)$value !== $options['value']) {
                continue;
            }

            $conditions[] = [$table->aliasField($field) => $options['value']];
        }

        $query->where(
            [
                'OR' => $conditions,
            ],
            [],
            true
        );
    }

    /**
     * Returns value type-cast to the provided field type.
     *
     * @param mixed $value Original value
     * @param string $fieldType Field type
     * @return mixed
     */
    private function castValueByFieldType($value, string $fieldType)
    {
        switch ($fieldType) {
            case TableSchema::TYPE_BOOLEAN:
                $value = (bool)$value;
                break;

            case TableSchema::TYPE_TINYINTEGER:
            case TableSchema::TYPE_SMALLINTEGER:
            case TableSchema::TYPE_INTEGER:
            case TableSchema::TYPE_BIGINTEGER:
                $value = (int)$value;
                break;

            case TableSchema::TYPE_FLOAT:
            case TableSchema::TYPE_DECIMAL:
                $value = (float)$value;
                break;
        }

        return $value;
    }

    /**
     * Checks request data association fields (foreign keys) values and query's the database to find
     * the associated record. If the record is not found, it query's again to find the record by
     * lookup fields. If found it replaces the associated field's value with the records id.
     *
     * This is useful for cases where the display field value is used on the associated field. For example
     * a new post is created and in the 'owner' field the username of the user is used instead of its uuid.
     *
     * BEFORE:
     * [
     *    'title' => 'Lorem Ipsum',
     *    'content' => '.....',
     *    'owner' => 'admin',
     * ]
     *
     * AFTER:
     * [
     *    'title' => 'Lorem Ipsum',
     *    'content' => '.....',
     *    'owner' => '77dd9203-3f21-4571-8843-0264ae1cfa48',
     * ]
     *
     * @param \Cake\Event\Event $event Event object
     * @param \ArrayObject $data Request data
     * @param \ArrayObject $options Query options
     * @return void
     */
    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options): void
    {
        if (! isset($options['lookup']) || ! (bool)$options['lookup']) {
            return;
        }

        $table = $event->getSubject();
        Assert::isInstanceOf($table, Table::class);

        foreach ($table->associations() as $association) {
            if (! $this->validate($association, $data)) {
                continue;
            }

            $this->setRelatedIdByLookupField($association, $data);
        }
    }

    /**
     * Validate's if lookup logic can be applied using the specified association.
     *
     * @param \Cake\ORM\Association $association Table association
     * @param \ArrayObject $data Request data
     * @return bool
     */
    private function validate(Association $association, ArrayObject $data): bool
    {
        if (! $this->isValidAssociation($association)) {
            return false;
        }

        // skip if foreign key is not set in the request data
        if (empty($data[$association->getForeignKey()])) {
            return false;
        }

        // skip if foreign key is a valid ID
        if ($this->isValidID($association, $data[$association->getForeignKey()])) {
            return false;
        }

        return true;
    }

    /**
     * Validates if association can be used for lookup functionality.
     *
     * @param \Cake\ORM\Association $association Table association
     * @return bool
     */
    private function isValidAssociation(Association $association): bool
    {
        if (Association::MANY_TO_ONE !== $association->type()) {
            return false;
        }

        if (empty($association->className())) {
            return false;
        }

        return true;
    }

    /**
     * Checks if foreign key value is a valid ID.
     *
     * @param \Cake\ORM\Association $association Table association
     * @param mixed $value Foreign key value
     * @return bool
     */
    private function isValidID(Association $association, $value): bool
    {
        $table = $association->getTarget();
        Assert::isInstanceOf($table, Table::class);

        $primaryKey = $table->getPrimaryKey();
        Assert::string($primaryKey);

        $query = $table->find('all')
            ->where([$primaryKey => $value])
            ->limit(1);

        return ! $query->isEmpty();
    }

    /**
     * Sets related record value by lookup fields.
     *
     * @param \Cake\ORM\Association $association Table association
     * @param \ArrayObject $data Request data
     * @return void
     */
    private function setRelatedIdByLookupField(Association $association, ArrayObject $data): void
    {
        $lookupFields = $this->getLookupFields($association->className());
        if (empty($lookupFields)) {
            return;
        }

        $relatedEntity = $this->getRelatedEntity($association, $data, $lookupFields);
        if (is_null($relatedEntity)) {
            return;
        }

        $primaryKey = $association->getTarget()->getPrimaryKey();
        if (!is_string($primaryKey)) {
            throw new UnsupportedPrimaryKeyException();
        }

        $data[$association->getForeignKey()] = $relatedEntity->get($primaryKey);
    }

    /**
     * Module lookup fields getter.
     *
     * @param string $moduleName Module name
     * @return mixed[]
     */
    private function getLookupFields(string $moduleName): array
    {
        return Hash::get(
            ModuleRegistry::getModule($moduleName)->getConfig(),
            'table.lookup_fields',
            []
        );
    }

    /**
     * Retrieves associated entity.
     *
     * @param \Cake\ORM\Association $association Table association
     * @param \ArrayObject $data Request data
     * @param mixed[] $fields Lookup fields
     * @return \Cake\Datasource\EntityInterface|null
     */
    private function getRelatedEntity(Association $association, ArrayObject $data, array $fields): ?EntityInterface
    {
        $query = $association->getTarget()
            ->find('all')
            ->select($association->getTarget()->getPrimaryKey())
            ->enableHydration(true)
            ->limit(1);

        $conditions = [];
        if ($query->clause('where') !== null) {
            $conditions[] = $query->clause('where');
        }

        foreach ($fields as $field) {
            $conditions[] = [$field => $data[$association->getForeignKey()]];
        }

        $query->where(
            [
                'OR' => $conditions,
            ],
            [],
            true
        );

        $result = $query->first();
        Assert::nullOrIsInstanceOf($result, EntityInterface::class);

        return $result;
    }

    /**
     * Apply lookup fields to Query's where clause.
     *
     * @param \Cake\ORM\Query $query Query object
     * @param mixed[] $options Query options
     * @return \Cake\ORM\Query|null
     */
    public function findLookup(Query $query, array $options): ?Query
    {
        $table = $this->_table;
        Assert::isInstanceOf($table, Table::class);
        if (! isset($options['value'])) {
            return $query;
        }

        // fail-safe binding of primary key to query's where clause, if lookup
        // fields are not defined, to avoid random record retrieval.
        if (empty($this->lookupFields)) {
            $primaryKey = $table->getPrimaryKey();
            if (! is_string($primaryKey)) {
                throw new UnsupportedPrimaryKeyException();
            }

            $query->where([
                $table->aliasField($primaryKey) => $options['value'],
            ]);

            return $query;
        }

        $conditions = [];
        if ($query->clause('where') !== null) {
            $conditions[] = $query->clause('where');
        }

        foreach ($this->lookupFields as $field) {
            $value = $this->castValueByFieldType($options['value'], (string)$table->getSchema()->getColumnType($field));
            // cast value back to string and do strict comparison,
            // skip lookup field if cast value does not match original
            if ((string)$value !== $options['value']) {
                continue;
            }

            $conditions[] = [$table->aliasField($field) => $options['value']];
        }

        $query->where(
            [
                'OR' => $conditions,
            ],
            [],
            true
        );

        return $query;
    }
}
