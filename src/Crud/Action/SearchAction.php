<?php

namespace App\Crud\Action;

use App\Search\Manager as SearchManager;
use App\Utility\Field;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Crud\Action\BaseAction;
use Crud\Traits\FindMethodTrait;
use Crud\Traits\ViewVarTrait;
use CsvMigrations\Exception\UnsupportedPrimaryKeyException;
use CsvMigrations\Table as CsvMigrationsTable;
use InvalidArgumentException;
use Qobo\Utils\Module\ModuleRegistry;
use Webmozart\Assert\Assert;

/**
 * Handles 'Search' Crud actions
 */
class SearchAction extends BaseAction
{
    use FindMethodTrait;
    use ViewVarTrait;

    /**
     * Default settings for 'related' actions
     *
     * @var array
     */
    protected $_defaultConfig = [
        'enabled' => true,
        'scope' => 'table',
        'findMethod' => 'search',
        'view' => null,
        'viewVar' => null,
        'serialize' => [],
        'api' => [
            'methods' => ['post'],
            'success' => [
                'code' => 200,
            ],
            'error' => [
                'code' => 400,
            ],
        ],
    ];

    /**
     * HTTP POST handler
     *
     * @return void
     */
    protected function _post(): void
    {
        list($finder, ) = $this->_extractFinder();
        $options = SearchManager::getOptionsFromRequest(
            (array)$this->_request()->getData(),
            $this->_request()->getQueryParams()
        );

        if (SearchManager::includePrimaryKey($options)) {
            $options['fields'] = array_merge((array)$this->_table()->getPrimaryKey(), Hash::get($options, 'fields', []));
        }

        // Check order by clause for related Modules
        $orderBy = $options['order'] ?? [];
        foreach ($orderBy as $field => $direction) {
            $newOrder = [];
            $fieldMeta = $this->getField($field);
            $state = $fieldMeta->state();
            if ($state['type'] !== 'related') {
                continue;
            }

            $relatedModule = Inflector::camelize($state['source']);
            $relatedField = $state['display_field'];
            $associationName = CsvMigrationsTable::generateAssociationName($relatedModule, $state['name']);
            $config = ModuleRegistry::getModule($relatedModule)->getConfig();

            // Virtual fields consisting of concatenation of database fields need to be unpacked into multiple order by clauses.
            if (isset($config['virtualFields'][$relatedField])) {
                foreach ($config['virtualFields'][$relatedField] as $virtualRefField) {
                    $orderClauseName = $associationName . '.' . $virtualRefField;
                    $newOrder[$orderClauseName] = $direction;
                }
            } else {
                $orderClauseName = $associationName . '.' . $relatedField;
                $newOrder[$orderClauseName] = $direction;
            }

            // In order to make the join happen, we need to select atleast one field from the associated table
            try {
                $relatedPrimaryKeyField = $this->_table()
                    ->getAssociation($associationName)
                    ->getTarget()
                    ->getPrimaryKey();
                Assert::string($relatedPrimaryKeyField, (string)__('Primary key must be a string.'));
                $relatedPrimaryKey = $associationName . '.' . $relatedPrimaryKeyField;
                if (!in_array($relatedPrimaryKey, $options['fields'])) {
                    $options['fields'][] = $relatedPrimaryKey;
                }

                // Remove the old ordering
                unset($options['order'][$field]);
                $options['order'] = array_merge($options['order'], $newOrder);
            } catch (InvalidArgumentException $e) {
                throw new UnsupportedPrimaryKeyException($e->getMessage(), $e->getCode(), $e);
            }
        }

        $query = $this->_table()->find($finder, $options);

        $subject = $this->_subject(['success' => true, 'query' => $query]);

        if (! property_exists($subject, 'query')) {
            throw new \InvalidArgumentException('"query" property is required');
        }

        $this->_trigger('beforePaginate', $subject);

        $subject->query->formatResults(new \App\ORM\PrettyFormatter())
            ->formatResults(new \App\ORM\PermissionsFormatter())
            ->formatResults(new \App\ORM\FlatFormatter());

        $resultSet = $this->_controller()->paginate($subject->query, [
            'limit' => $this->_request()->getData('limit', 10),
            'page' => $this->_request()->getData('page', 1),
        ]);

        $subject->set(['entities' => $resultSet->toArray()]);

        $this->_trigger('afterPaginate', $subject);
        $this->_trigger('beforeRender', $subject);
    }

    /**
     * Returns a field instance.
     *
     * @param string $modelField Aliased module field.
     * @return \App\Utility\Field
     * @throws \InvalidArgumentException When the module field is not aliased.
     */
    protected function getField(string $modelField): Field
    {
        if (strpos($modelField, '.') === false) {
            throw new InvalidArgumentException((string)__('The field name provided is not aliased.'));
        }
        list($module, $field) = explode('.', $modelField, 2);

        return new Field($module, $field);
    }
}
