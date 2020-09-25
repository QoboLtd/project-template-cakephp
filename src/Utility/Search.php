<?php

namespace App\Utility;

use App\Search\Manager;
use Cake\Cache\Cache;
use Cake\Core\App;
use Cake\ORM\Association;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Qobo\Utils\Module\Exception\MissingModuleException;
use Qobo\Utils\Module\ModuleRegistry;
use Search\Aggregate\AggregateInterface;
use Search\Model\Entity\SavedSearch;
use Webmozart\Assert\Assert;

final class Search
{
    /**
     * Search filters.
     *
     * @var array
     */
    private static $filters = [];

    private const SUPPORTED_ASSOCIATIONS = [
        Association::MANY_TO_ONE,
        Association::MANY_TO_MANY,
        Association::ONE_TO_MANY,
    ];

    /**
     * Charts list.
     *
     * @var array
     */
    private const CHARTS = [
        ['type' => 'funnelChart', 'icon' => 'filter', 'class' => '\Search\Widgets\Reports\DonutChartReportWidget'],
        ['type' => 'pie', 'icon' => 'pie-chart', 'class' => '\Search\Widgets\Reports\PieChartReportWidget'],
        ['type' => 'bar', 'icon' => 'bar-chart', 'class' => '\Search\Widgets\Reports\BarChartReportWidget'],
    ];

    /**
     * Table fields getter
     *
     * @param string $tableName Table name
     * @param bool $onlySearchable Include only searchable fields
     * @return mixed[]
     */
    public static function getFields(string $tableName, bool $onlySearchable = false): array
    {
        $result = [];
        $cacheKey = 'search_filters_' . md5($tableName);

        // Check local cache
        if (!empty(static::$filters[$tableName])) {
            $result = static::$filters[$tableName];
        }

        // Check file cache
        if (empty($result)) {
            $cached = Cache::read($cacheKey);
            if (false !== $cached) {
                $result = $cached;
            }
        }

        // Populate fields data
        if (empty($result)) {
            $table = TableRegistry::getTableLocator()->get($tableName);
            $labels = self::getAssociationLabels($table);
            $result = self::getTableSchema($table);
            foreach ($result as $index => $options) {
                unset($result[$index]['input']);
                unset($result[$index]['operators']);

                list($group, ) = pluginSplit($options['field']);
                $group = array_key_exists($group, $labels) ? $labels[$group] : $group;

                $result[$index]['group'] = $group;
            }

            $result = array_values($result);
        }

        // useful when user with limited access initiates the request and result is empty
        Assert::isArray($result);
        if (!empty($result)) {
            Cache::write($cacheKey, $result);
        }

        static::$filters[$tableName] = $result;

        if ($onlySearchable) {
            return array_values(Hash::remove($result, '{n}[searchable=false]'));
        }

        return $result;
    }

    /**
     * Search filters getter.
     *
     * @param string $tableName Table name
     * @return mixed[]
     */
    public static function getFilters(string $tableName): array
    {
        deprecationWarning(sprintf(
            '%s::%s method is deprecated. Please use %s::%s instead.',
            Search::class,
            __FUNCTION__,
            Search::class,
            'getFields'
        ));

        return self::getFields($tableName, true);
    }

    /**
     * Method that retrieves target table search display fields.
     *
     * @param string $tableName Table name
     * @return string[]
     */
    public static function getDisplayFields(string $tableName): array
    {
        $result = self::getDisplayFieldsFromSystemSearch($tableName);

        if ([] === $result) {
            $result = self::getDisplayFieldsFromView($tableName);
        }

        if ([] === $result) {
            $result = self::getDisplayFieldsFromDatabaseColumns($tableName);
        }

        $table = TableRegistry::getTableLocator()->get($tableName);

        $result = array_map(function ($item) use ($table) {
            return $table->aliasField($item);
        }, $result);

        $result = array_filter($result, function ($item) use ($tableName) {
            return false !== array_search($item, array_column(self::getFields($tableName), 'field'), true);
        });

        return array_values($result);
    }

    /**
     * Chart options getter.
     *
     * @param \Search\Model\Entity\SavedSearch $savedSearch Saved search entity
     * @return mixed[]
     */
    public static function getChartOptions(SavedSearch $savedSearch): array
    {
        $aggregate = array_filter((array)$savedSearch->get('fields'), function ($item) {
            return 1 === preg_match(AggregateInterface::AGGREGATE_PATTERN, $item);
        });

        if ([] === $aggregate) {
            return [];
        }

        preg_match(AggregateInterface::AGGREGATE_PATTERN, array_values($aggregate)[0], $matches);
        $aggregate = $matches[0];
        $aggregateType = $matches[1];
        $aggregateFieldAliased = $matches[2];
        list(, $aggregateField) = pluginSplit($aggregateFieldAliased);

        $table = TableRegistry::getTableLocator()->get($savedSearch->get('model'));

        $query = $table->find('search', Manager::getOptionsFromRequest([
            'criteria' => $savedSearch->get('criteria'),
            'fields' => $savedSearch->get('fields'),
            'conjunction' => $savedSearch->get('conjunction'),
            'sort' => $savedSearch->get('order_by_field'),
            'direction' => $savedSearch->get('order_by_direction'),
            'group_by' => $savedSearch->get('group_by'),
        ], []));

        $query->formatResults(new \App\ORM\PrettyFormatter());
        $query->formatResults(new \App\ORM\FlatFormatter());

        $rowLabel = sprintf('%s (%s)', $aggregateField, $aggregateType);
        list(, $rowValue) = $savedSearch->get('group_by') ? pluginSplit($savedSearch->get('group_by')) : ['', $aggregateField];
        $filters = self::getFields($savedSearch->get('model'));
        $rows = [];

        foreach ($query->all() as $entity) {
            $row = [$rowLabel => $entity[$aggregate]];
            $key = array_search($aggregateFieldAliased, array_column($filters, 'field'));
            $row[$rowValue] = $savedSearch->get('group_by') ? $entity[$savedSearch->get('group_by')] : $filters[$key]['label'];

            $rows[] = $row;
        }

        $result = [];
        foreach (self::CHARTS as $chart) {
            $options = [
                'icon' => $chart['icon'],
                'id' => Inflector::delimit($chart['type']) . '_' . uniqid(),
                'chart' => $chart['type'],
                'slug' => $savedSearch->get('name'),
            ];

            switch ($chart['type']) {
                case 'bar':
                case 'pie':
                    $widget = new $chart['class']();
                    $widget->setConfig([
                        'modelName' => $savedSearch->get('model'),
                        'info' => [
                            'columns' => implode(',', [$rowLabel, $rowValue]),
                            'x_axis' => $rowValue,
                            'y_axis' => $rowLabel,
                        ],
                    ]);

                    $widget->setContainerId($options);
                    $options += $widget->getChartData($rows);
                    break;
                case 'funnelChart':
                    $data = [];
                    foreach ($rows as $row) {
                        $data[] = [
                            'value' => Hash::get($row, $rowLabel),
                            'label' => Hash::get($row, $rowValue),
                        ];
                    }

                    $options += [
                        'options' => [
                            'resize' => true,
                            'hideHover' => true,
                            'labels' => [
                                Inflector::humanize($rowLabel),
                                Inflector::humanize($rowValue),
                            ],
                            'xkey' => [$rowValue],
                            'ykeys' => [$rowLabel],
                            'dataChart' => [
                                'type' => $chart['type'],
                                'data' => $data,
                            ],
                        ],
                    ];

                    break;
            }

            $result[] = $options;
        }

        return $result;
    }

    /**
     * Associations labels getter.
     *
     * @param \Cake\ORM\Table $table Table instance
     * @return mixed[]
     */
    private static function getAssociationLabels(Table $table): array
    {
        $result = [];
        foreach ($table->associations() as $association) {
            $result[$association->getName()] = sprintf(
                '%s (%s)',
                App::shortName(get_class($association->getTarget()), 'Model/Table', 'Table'),
                Inflector::humanize(implode(', ', (array)$association->getForeignKey()))
            );
        }

        return $result;
    }

    /**
     * Method that retrieves target table schema
     *
     * @param \Cake\ORM\Table $table Table instance
     * @param bool $withAssociated flag for including associations fields
     * @return mixed[]
     */
    private static function getTableSchema(Table $table, bool $withAssociated = true): array
    {
        $result = self::getFieldSchemaByTable($table);
        if ($withAssociated) {
            $result = array_merge($result, self::includeAssociated($table));
        }

        return $result;
    }

    /**
     * Returns display fields from provided Table's system search.
     *
     * @param string $tableName Table name
     * @return string[]
     */
    private static function getDisplayFieldsFromSystemSearch(string $tableName): array
    {
        $entity = TableRegistry::getTableLocator()
            ->get('Search.SavedSearches')
            ->find('all')
            ->where(['SavedSearches.model' => $tableName, 'SavedSearches.system' => true])
            ->enableHydration(true)
            ->first();

        Assert::nullOrIsInstanceOf($entity, SavedSearch::class);

        return null !== $entity ? (array)$entity->get('fields') : [];
    }

    /**
     * Returns display fields from provided Table's index View fields.
     *
     * @param string $tableName Table name
     * @return string[]
     */
    private static function getDisplayFieldsFromView(string $tableName): array
    {
        list(, $module) = pluginSplit($tableName);
        $fields = [];
        try {
            $fields = ModuleRegistry::getModule($module)->getView('index');
        } catch (MissingModuleException $e) {
            // @ignoreException
        }

        $columns = TableRegistry::getTableLocator()
            ->get($tableName)
            ->getSchema()
            ->columns();

        return array_filter(
            array_map(function ($field) {
                return $field[0];
            }, $fields),
            function ($item) use ($columns) {
                return in_array($item, $columns, true);
            }
        );
    }

    /**
     * Returns display fields from provided table's database columns.
     *
     * @param string $tableName Table name
     * @return string[]
     */
    private static function getDisplayFieldsFromDatabaseColumns(string $tableName): array
    {
        $table = TableRegistry::getTableLocator()->get($tableName);

        $result = array_keys(
            array_filter(
                $table->getSchema()->typeMap(),
                function ($item) {
                    return 'string' === $item;
                }
            )
        );

        return array_slice($result, 0, 6);
    }

    /**
     * Field schema getter by Table instance.
     *
     * @param \Cake\ORM\Table $table Table instance
     * @return mixed[]
     */
    private static function getFieldSchemaByTable(Table $table): array
    {
        $moduleName = App::shortName(get_class($table), 'Model/Table', 'Table');

        $result = [];
        foreach (Model::fields($moduleName) as $field) {
            if (in_array($field['type'], ['uuid', 'base64'])) {
                continue;
            }

            if (in_array($field['name'], ['trashed'])) {
                continue;
            }

            $item = [
                'type' => $field['type'],
                'label' => $field['label'],
                'searchable' => !in_array('non-searchable', $field['meta']),
            ];

            if (array_key_exists('options', $field)) {
                $item['options'] = $field['options'];
            }

            if (array_key_exists('display_field', $field)) {
                $item['display_field'] = $field['display_field'];
            }

            if (array_key_exists('source', $field)) {
                $item['source'] = $field['source'];
            }

            $item['field'] = $table->aliasField($field['name']);

            $result[] = $item;
        }

        return $result;
    }

    /**
     * Get associated tables searchable fields.
     *
     * @param \Cake\ORM\Table $table Table instance
     * @return mixed[]
     */
    private static function includeAssociated(Table $table): array
    {
        $result = [];

        foreach ($table->associations() as $association) {
            // skip non-supported associations
            if (! in_array($association->type(), self::SUPPORTED_ASSOCIATIONS)) {
                continue;
            }

            $targetTable = $association->getTarget();
            if ($targetTable instanceof \Burzum\FileStorage\Model\Table\FileStorageTable) {
                continue;
            }

            // skip associations with itself
            if ($targetTable->getTable() === $table->getTable()) {
                continue;
            }

            // fetch associated model searchable fields
            $fields = self::getTableSchema($targetTable, false);

            $fields = array_map(function ($item) use ($association) {
                $item['association'] = $association->type();

                return $item;
            }, $fields);

            $result = array_merge($result, $fields);
        }

        return $result;
    }
}
