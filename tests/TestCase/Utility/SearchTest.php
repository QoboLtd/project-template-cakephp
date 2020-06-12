<?php

namespace App\Test\TestCase\Utility;

use App\Utility\Search;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Search\Model\Entity\SavedSearch;

class SearchTest extends TestCase
{
    public $fixtures = [
        'app.saved_searches',
        'app.things',
        'app.users',
        'plugin.CakeDC/Users.social_accounts',
        'plugin.CsvMigrations.dblists',
    ];

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @param string $modelName
     * @param mixed[] $expected
     * @dataProvider filtersProvider
     */
    public function testGetFilters(string $modelName, array $expected): void
    {
        $result = Search::getFields($modelName);

        usort($result, function (array $a, array $b) {
            return strcmp($a['field'], $b['field']);
        });

        foreach ($expected as $key => $value) {
            $key = array_search($value['field'], array_column($result, 'field'), true);
            unset($result[$key]['options']);
            $this->assertSame($value, $result[$key]);
        }
    }

    public function testGetDisplayFieldsFromView(): void
    {
        $expected = [
            'Things.name',
            'Things.gender',
            'Things.assigned_to',
            'Things.created',
            'Things.modified',
        ];

        $this->assertSame($expected, Search::getDisplayFields('Things'));
    }

    public function testGetDisplayFieldsFromSystemSearch(): void
    {
        $table = TableRegistry::getTableLocator()->get('Search.SavedSearches');
        $table->deleteAll([]);

        $expected = ['Things.name'];

        // create system search
        $table->saveOrFail(
            $table->newEntity([
                'name' => 'A name',
                'model' => 'Things',
                'user_id' => '00000000-0000-0000-0000-000000000002',
                'system' => true,
                'fields' => $expected,
            ])
        );

        $this->assertSame($expected, Search::getDisplayFields('Things'));
    }

    public function testGetDisplayFieldsWithDisplayFieldBeingTheFirstFilter(): void
    {
        $table = TableRegistry::getTableLocator()->get('Search.SavedSearches');
        $table->deleteAll([]);

        $expected = ['Things.name'];
        $this->assertSame(
            current($expected),
            Search::getFields('Things')[0]['field'],
            'Pre-test assertion, if $expected does not match filters first field, adjust $expected accordingly'
        );

        $table->saveOrFail(
            $table->newEntity([
                'name' => 'A name',
                'model' => 'Things',
                'user_id' => '00000000-0000-0000-0000-000000000002',
                'system' => true,
                'fields' => $expected,
            ])
        );

        $this->assertSame($expected, Search::getDisplayFields('Things'));
    }

    public function testGetDisplayFieldsFromDatabaseColumns(): void
    {
        $table = TableRegistry::getTableLocator()->get('Search.SavedSearches');
        $table->deleteAll([]);

        $expected = [
            'SavedSearches.conjunction',
            'SavedSearches.group_by',
            'SavedSearches.model',
            'SavedSearches.name',
            'SavedSearches.order_by_direction',
            'SavedSearches.order_by_field',
        ];

        $displayFields = Search::getDisplayFields('Search.SavedSearches');
        sort($displayFields);

        $this->assertSame($expected, $displayFields);
    }

    /**
     * @param mixed[] $expected
     * @dataProvider chartOptionsProvider
     */
    public function testGetChartOptions(array $expected): void
    {
        $savedSearch = new SavedSearch([
            'name' => 'Things grouped by created date',
            'model' => 'Things',
            'fields' => ['Things.created', 'COUNT(Things.created)'],
            'group_by' => 'Things.created',
        ]);

        $result = Search::getChartOptions($savedSearch);

        foreach ($expected as $key => $value) {
            // id is dynamic
            unset($result[$key]['id']);
            $this->assertSame($value, $result[$key]);
        }
    }

    public function testGetChartOptionsWithoutGroupByOrAggregate(): void
    {
        $savedSearch = new SavedSearch([
            'name' => 'Things NOT grouped by',
            'model' => 'Things',
        ]);

        $this->assertSame([], Search::getChartOptions($savedSearch));
    }

    public function testGetChartOptionsWithGroupByButNotAggregate(): void
    {
        $savedSearch = new SavedSearch([
            'name' => 'Things NOT grouped by',
            'model' => 'Things',
            'fields' => ['Things.created'],
            'group_by' => 'Things.created',
        ]);

        $this->assertSame([], Search::getChartOptions($savedSearch));
    }

    /**
     * @param mixed[] $expected
     * @dataProvider chartOptionsWithAggregateProvider
     */
    public function testGetChartOptionsWithAggregateButNotGroupBy(array $expected): void
    {
        $savedSearch = new SavedSearch([
            'name' => 'Things NOT grouped by',
            'model' => 'Things',
            'fields' => ['COUNT(Things.created)'],
        ]);

        $result = Search::getChartOptions($savedSearch);

        foreach ($expected as $key => $value) {
            // id is dynamic
            unset($result[$key]['id']);
            $this->assertSame($value, $result[$key]);
        }
    }

    /**
     * @return mixed[]
     */
    public function chartOptionsProvider(): array
    {
        return [
            [
                [
                    ['icon' => 'filter', 'chart' => 'funnelChart', 'slug' => 'Things grouped by created date', 'options' => ['resize' => true, 'hideHover' => true, 'labels' => ['Created (COUNT)', 'Created'], 'xkey' => ['created'], 'ykeys' => ['created (COUNT)'], 'dataChart' => ['type' => 'funnelChart', 'data' => [['value' => 3, 'label' => '2018-01-18 15:47']]]]],
                    ['icon' => 'pie-chart', 'chart' => 'pie', 'slug' => 'Things grouped by created date', 'options' => ['resize' => true, 'hideHover' => true, 'dataChart' => ['type' => 'pie', 'data' => ['labels' => ['2018-01-18 15:47'], 'datasets' => [['backgroundColor' => ['#c7004c'], 'data' => [3]]]]]]],
                    ['icon' => 'bar-chart', 'chart' => 'bar', 'slug' => 'Things grouped by created date', 'options' => ['resize' => true, 'hideHover' => true, 'dataChart' => ['type' => 'bar', 'data' => ['labels' => ['2018-01-18 15:47'], 'datasets' => [['label' => 'Created (COUNT)', 'backgroundColor' => ['#0b8c0d'], 'data' => [3]]]], 'options' => ['legend' => ['display' => false], 'scales' => ['yAxes' => [['ticks' => ['beginAtZero' => true]]], 'xAxes' => [['ticks' => ['autoSkip' => false]]]]]]]],
                ],
            ],
        ];
    }

    /**
     * @return mixed[]
     */
    public function chartOptionsWithAggregateProvider(): array
    {
        return [
            [
                [
                    ['icon' => 'filter', 'chart' => 'funnelChart', 'slug' => 'Things NOT grouped by', 'options' => ['resize' => true, 'hideHover' => true, 'labels' => ['Created (COUNT)', 'Created'], 'xkey' => ['created'], 'ykeys' => ['created (COUNT)'], 'dataChart' => ['type' => 'funnelChart', 'data' => [['value' => 3, 'label' => 'Created']]]]],
                    ['icon' => 'pie-chart', 'chart' => 'pie', 'slug' => 'Things NOT grouped by', 'options' => ['resize' => true, 'hideHover' => true, 'dataChart' => ['type' => 'pie', 'data' => ['labels' => ['Created'], 'datasets' => [['backgroundColor' => ['#a06ee1'], 'data' => [3]]]]]]],
                    ['icon' => 'bar-chart', 'chart' => 'bar', 'slug' => 'Things NOT grouped by', 'options' => ['resize' => true, 'hideHover' => true, 'dataChart' => ['type' => 'bar', 'data' => ['labels' => ['Created'], 'datasets' => [['label' => 'Created (COUNT)', 'backgroundColor' => ['#bc1b68'], 'data' => [3]]]], 'options' => ['legend' => ['display' => false], 'scales' => ['yAxes' => [['ticks' => ['beginAtZero' => true]]], 'xAxes' => [['ticks' => ['autoSkip' => false]]]]]]]],
                ],
            ],
        ];
    }

    /**
     * @return mixed[]
     */
    public function filtersProvider(): array
    {
        return [
            [
                'Things',
                [
                    ['type' => 'datetime', 'label' => 'Activation Date', 'searchable' => true, 'field' => 'AssignedToUsers.activation_date', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'boolean', 'label' => 'Active', 'searchable' => true, 'field' => 'AssignedToUsers.active', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'text', 'label' => 'Additional Data', 'searchable' => true, 'field' => 'AssignedToUsers.additional_data', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Api Token', 'searchable' => true, 'field' => 'AssignedToUsers.api_token', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'date', 'label' => 'Birthdate', 'searchable' => true, 'field' => 'AssignedToUsers.birthdate', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Company', 'searchable' => true, 'field' => 'AssignedToUsers.company', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Country', 'searchable' => true, 'field' => 'AssignedToUsers.country', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'datetime', 'label' => 'Created', 'searchable' => true, 'field' => 'AssignedToUsers.created', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Department', 'searchable' => true, 'field' => 'AssignedToUsers.department', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Email', 'searchable' => true, 'field' => 'AssignedToUsers.email', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'text', 'label' => 'Extras', 'searchable' => true, 'field' => 'AssignedToUsers.extras', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Fax', 'searchable' => true, 'field' => 'AssignedToUsers.fax', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'First Name', 'searchable' => true, 'field' => 'AssignedToUsers.first_name', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Gender', 'searchable' => true, 'field' => 'AssignedToUsers.gender', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Initials', 'searchable' => true, 'field' => 'AssignedToUsers.initials', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'boolean', 'label' => 'Is Superuser', 'searchable' => true, 'field' => 'AssignedToUsers.is_superuser', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'boolean', 'label' => 'Is Supervisor', 'searchable' => true, 'field' => 'AssignedToUsers.is_supervisor', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Last Name', 'searchable' => true, 'field' => 'AssignedToUsers.last_name', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'datetime', 'label' => 'Modified', 'searchable' => true, 'field' => 'AssignedToUsers.modified', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Password', 'searchable' => true, 'field' => 'AssignedToUsers.password', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Phone Extension', 'searchable' => true, 'field' => 'AssignedToUsers.phone_extension', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Phone Home', 'searchable' => true, 'field' => 'AssignedToUsers.phone_home', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Phone Mobile', 'searchable' => true, 'field' => 'AssignedToUsers.phone_mobile', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Phone Office', 'searchable' => true, 'field' => 'AssignedToUsers.phone_office', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Position', 'searchable' => true, 'field' => 'AssignedToUsers.position', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Role', 'searchable' => true, 'field' => 'AssignedToUsers.role', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Secret', 'searchable' => true, 'field' => 'AssignedToUsers.secret', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'boolean', 'label' => 'Secret Verified', 'searchable' => true, 'field' => 'AssignedToUsers.secret_verified', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Team', 'searchable' => true, 'field' => 'AssignedToUsers.team', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Token', 'searchable' => true, 'field' => 'AssignedToUsers.token', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'datetime', 'label' => 'Token Expires', 'searchable' => true, 'field' => 'AssignedToUsers.token_expires', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'datetime', 'label' => 'Tos Date', 'searchable' => true, 'field' => 'AssignedToUsers.tos_date', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'Username', 'searchable' => true, 'field' => 'AssignedToUsers.username', 'association' => 'manyToOne', 'group' => 'Assigned To'],
                    ['type' => 'datetime', 'label' => 'Activation Date', 'searchable' => true, 'field' => 'CreatedByUsers.activation_date', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'boolean', 'label' => 'Active', 'searchable' => true, 'field' => 'CreatedByUsers.active', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'text', 'label' => 'Additional Data', 'searchable' => true, 'field' => 'CreatedByUsers.additional_data', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Api Token', 'searchable' => true, 'field' => 'CreatedByUsers.api_token', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'date', 'label' => 'Birthdate', 'searchable' => true, 'field' => 'CreatedByUsers.birthdate', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Company', 'searchable' => true, 'field' => 'CreatedByUsers.company', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Country', 'searchable' => true, 'field' => 'CreatedByUsers.country', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'datetime', 'label' => 'Created', 'searchable' => true, 'field' => 'CreatedByUsers.created', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Department', 'searchable' => true, 'field' => 'CreatedByUsers.department', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Email', 'searchable' => true, 'field' => 'CreatedByUsers.email', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'text', 'label' => 'Extras', 'searchable' => true, 'field' => 'CreatedByUsers.extras', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Fax', 'searchable' => true, 'field' => 'CreatedByUsers.fax', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'First Name', 'searchable' => true, 'field' => 'CreatedByUsers.first_name', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Gender', 'searchable' => true, 'field' => 'CreatedByUsers.gender', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Initials', 'searchable' => true, 'field' => 'CreatedByUsers.initials', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'boolean', 'label' => 'Is Superuser', 'searchable' => true, 'field' => 'CreatedByUsers.is_superuser', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'boolean', 'label' => 'Is Supervisor', 'searchable' => true, 'field' => 'CreatedByUsers.is_supervisor', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Last Name', 'searchable' => true, 'field' => 'CreatedByUsers.last_name', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'datetime', 'label' => 'Modified', 'searchable' => true, 'field' => 'CreatedByUsers.modified', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Password', 'searchable' => true, 'field' => 'CreatedByUsers.password', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Phone Extension', 'searchable' => true, 'field' => 'CreatedByUsers.phone_extension', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Phone Home', 'searchable' => true, 'field' => 'CreatedByUsers.phone_home', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Phone Mobile', 'searchable' => true, 'field' => 'CreatedByUsers.phone_mobile', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Phone Office', 'searchable' => true, 'field' => 'CreatedByUsers.phone_office', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Position', 'searchable' => true, 'field' => 'CreatedByUsers.position', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Role', 'searchable' => true, 'field' => 'CreatedByUsers.role', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Secret', 'searchable' => true, 'field' => 'CreatedByUsers.secret', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'boolean', 'label' => 'Secret Verified', 'searchable' => true, 'field' => 'CreatedByUsers.secret_verified', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Team', 'searchable' => true, 'field' => 'CreatedByUsers.team', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Token', 'searchable' => true, 'field' => 'CreatedByUsers.token', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'datetime', 'label' => 'Token Expires', 'searchable' => true, 'field' => 'CreatedByUsers.token_expires', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'datetime', 'label' => 'Tos Date', 'searchable' => true, 'field' => 'CreatedByUsers.tos_date', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'Username', 'searchable' => true, 'field' => 'CreatedByUsers.username', 'association' => 'manyToOne', 'group' => 'Created By'],
                    ['type' => 'datetime', 'label' => 'Activation Date', 'searchable' => true, 'field' => 'ModifiedByUsers.activation_date', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'boolean', 'label' => 'Active', 'searchable' => true, 'field' => 'ModifiedByUsers.active', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'text', 'label' => 'Additional Data', 'searchable' => true, 'field' => 'ModifiedByUsers.additional_data', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Api Token', 'searchable' => true, 'field' => 'ModifiedByUsers.api_token', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'date', 'label' => 'Birthdate', 'searchable' => true, 'field' => 'ModifiedByUsers.birthdate', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Company', 'searchable' => true, 'field' => 'ModifiedByUsers.company', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Country', 'searchable' => true, 'field' => 'ModifiedByUsers.country', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'datetime', 'label' => 'Created', 'searchable' => true, 'field' => 'ModifiedByUsers.created', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Department', 'searchable' => true, 'field' => 'ModifiedByUsers.department', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Email', 'searchable' => true, 'field' => 'ModifiedByUsers.email', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'text', 'label' => 'Extras', 'searchable' => true, 'field' => 'ModifiedByUsers.extras', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Fax', 'searchable' => true, 'field' => 'ModifiedByUsers.fax', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'First Name', 'searchable' => true, 'field' => 'ModifiedByUsers.first_name', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Gender', 'searchable' => true, 'field' => 'ModifiedByUsers.gender', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Initials', 'searchable' => true, 'field' => 'ModifiedByUsers.initials', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'boolean', 'label' => 'Is Superuser', 'searchable' => true, 'field' => 'ModifiedByUsers.is_superuser', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'boolean', 'label' => 'Is Supervisor', 'searchable' => true, 'field' => 'ModifiedByUsers.is_supervisor', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Last Name', 'searchable' => true, 'field' => 'ModifiedByUsers.last_name', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'datetime', 'label' => 'Modified', 'searchable' => true, 'field' => 'ModifiedByUsers.modified', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Password', 'searchable' => true, 'field' => 'ModifiedByUsers.password', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Phone Extension', 'searchable' => true, 'field' => 'ModifiedByUsers.phone_extension', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Phone Home', 'searchable' => true, 'field' => 'ModifiedByUsers.phone_home', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Phone Mobile', 'searchable' => true, 'field' => 'ModifiedByUsers.phone_mobile', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Phone Office', 'searchable' => true, 'field' => 'ModifiedByUsers.phone_office', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Position', 'searchable' => true, 'field' => 'ModifiedByUsers.position', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Role', 'searchable' => true, 'field' => 'ModifiedByUsers.role', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Secret', 'searchable' => true, 'field' => 'ModifiedByUsers.secret', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'boolean', 'label' => 'Secret Verified', 'searchable' => true, 'field' => 'ModifiedByUsers.secret_verified', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Team', 'searchable' => true, 'field' => 'ModifiedByUsers.team', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Token', 'searchable' => true, 'field' => 'ModifiedByUsers.token', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'datetime', 'label' => 'Token Expires', 'searchable' => true, 'field' => 'ModifiedByUsers.token_expires', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'datetime', 'label' => 'Tos Date', 'searchable' => true, 'field' => 'ModifiedByUsers.tos_date', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'Username', 'searchable' => true, 'field' => 'ModifiedByUsers.username', 'association' => 'manyToOne', 'group' => 'Modified By'],
                    ['type' => 'reminder', 'label' => 'Appointment', 'searchable' => true, 'field' => 'Things.appointment', 'group' => 'Things'],
                    ['type' => 'decimal', 'label' => 'Area Amount', 'searchable' => true, 'field' => 'Things.area_amount', 'group' => 'Things'],
                    ['type' => 'list', 'label' => 'Area Unit', 'searchable' => true, 'field' => 'Things.area_unit', 'group' => 'Things'],
                    ['type' => 'related', 'label' => 'Assigned To', 'searchable' => true, 'display_field' => 'name', 'source' => 'users', 'field' => 'Things.assigned_to', 'group' => 'Things'],
                    ['type' => 'blob', 'label' => 'Bio', 'searchable' => true, 'field' => 'Things.bio', 'group' => 'Things'],
                    ['type' => 'country', 'label' => 'Country', 'searchable' => true, 'field' => 'Things.country', 'group' => 'Things'],
                    ['type' => 'datetime', 'label' => 'Created', 'searchable' => true, 'field' => 'Things.created', 'group' => 'Things'],
                    ['type' => 'related', 'label' => 'Created By', 'searchable' => true, 'display_field' => 'name', 'source' => 'users', 'field' => 'Things.created_by', 'group' => 'Things'],
                    ['type' => 'currency', 'label' => 'Currency', 'searchable' => true, 'field' => 'Things.currency', 'group' => 'Things'],
                    ['type' => 'date', 'label' => 'Date Of Birth', 'searchable' => true, 'field' => 'Things.date_of_birth', 'group' => 'Things'],
                    ['type' => 'text', 'label' => 'label description', 'searchable' => true, 'field' => 'Things.description', 'group' => 'Things'],
                    ['type' => 'email', 'label' => 'Email', 'searchable' => true, 'field' => 'Things.email', 'group' => 'Things'],
                    ['type' => 'list', 'label' => 'Gender', 'searchable' => true, 'field' => 'Things.gender', 'group' => 'Things'],
                    ['type' => 'list', 'label' => 'Language', 'searchable' => true, 'field' => 'Things.language', 'group' => 'Things'],
                    ['type' => 'integer', 'label' => 'Level', 'searchable' => true, 'field' => 'Things.level', 'group' => 'Things'],
                    ['type' => 'datetime', 'label' => 'Modified', 'searchable' => true, 'field' => 'Things.modified', 'group' => 'Things'],
                    ['type' => 'related', 'label' => 'Modified By', 'searchable' => true, 'display_field' => 'name', 'source' => 'users', 'field' => 'Things.modified_by', 'group' => 'Things'],
                    ['type' => 'string', 'label' => 'label name', 'searchable' => true, 'field' => 'Things.name', 'group' => 'Things'],
                    ['type' => 'phone', 'label' => 'Phone', 'searchable' => true, 'field' => 'Things.phone', 'group' => 'Things'],
                    ['type' => 'related', 'label' => 'Primary Thing', 'searchable' => true, 'display_field' => 'name', 'source' => 'things', 'field' => 'Things.primary_thing', 'group' => 'Things'],
                    ['type' => 'decimal', 'label' => 'Rate', 'searchable' => true, 'field' => 'Things.rate', 'group' => 'Things'],
                    ['type' => 'decimal', 'label' => 'Salary Amount', 'searchable' => true, 'field' => 'Things.salary_amount', 'group' => 'Things'],
                    ['type' => 'list', 'label' => 'Salary Currency', 'searchable' => true, 'field' => 'Things.salary_currency', 'group' => 'Things'],
                    ['type' => 'datetime', 'label' => 'Sample Date', 'searchable' => true, 'field' => 'Things.sample_date', 'group' => 'Things'],
                    ['type' => 'sublist', 'label' => 'Test List', 'searchable' => true, 'field' => 'Things.test_list', 'group' => 'Things'],
                    ['type' => 'decimal', 'label' => 'Testmetric Amount', 'searchable' => true, 'field' => 'Things.testmetric_amount', 'group' => 'Things'],
                    ['type' => 'list', 'label' => 'Testmetric Unit', 'searchable' => true, 'field' => 'Things.testmetric_unit', 'group' => 'Things'],
                    ['type' => 'decimal', 'label' => 'Testmoney Amount', 'searchable' => true, 'field' => 'Things.testmoney_amount', 'group' => 'Things'],
                    ['type' => 'list', 'label' => 'Testmoney Currency', 'searchable' => true, 'field' => 'Things.testmoney_currency', 'group' => 'Things'],
                    ['type' => 'list', 'label' => 'Title', 'searchable' => true, 'field' => 'Things.title', 'group' => 'Things'],
                    ['type' => 'boolean', 'label' => 'Vip', 'searchable' => true, 'field' => 'Things.vip', 'group' => 'Things'],
                    ['type' => 'url', 'label' => 'Website', 'searchable' => true, 'field' => 'Things.website', 'group' => 'Things'],
                    ['type' => 'time', 'label' => 'Work Start', 'searchable' => true, 'field' => 'Things.work_start', 'group' => 'Things'],
                ],
            ],
            [
                'Users',
                [
                    ['type' => 'reminder', 'label' => 'Appointment', 'searchable' => true, 'field' => 'AssignedToThings.appointment', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'decimal', 'label' => 'Area Amount', 'searchable' => true, 'field' => 'AssignedToThings.area_amount', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'list', 'label' => 'Area Unit', 'searchable' => true, 'field' => 'AssignedToThings.area_unit', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'related', 'label' => 'Assigned To', 'searchable' => true, 'display_field' => 'name', 'source' => 'users', 'field' => 'AssignedToThings.assigned_to', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'blob', 'label' => 'Bio', 'searchable' => true, 'field' => 'AssignedToThings.bio', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'country', 'label' => 'Country', 'searchable' => true, 'field' => 'AssignedToThings.country', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'datetime', 'label' => 'Created', 'searchable' => true, 'field' => 'AssignedToThings.created', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'related', 'label' => 'Created By', 'searchable' => true, 'display_field' => 'name', 'source' => 'users', 'field' => 'AssignedToThings.created_by', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'currency', 'label' => 'Currency', 'searchable' => true, 'field' => 'AssignedToThings.currency', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'date', 'label' => 'Date Of Birth', 'searchable' => true, 'field' => 'AssignedToThings.date_of_birth', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'text', 'label' => 'label description', 'searchable' => true, 'field' => 'AssignedToThings.description', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'email', 'label' => 'Email', 'searchable' => true, 'field' => 'AssignedToThings.email', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'list', 'label' => 'Gender', 'searchable' => true, 'field' => 'AssignedToThings.gender', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'list', 'label' => 'Language', 'searchable' => true, 'field' => 'AssignedToThings.language', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'integer', 'label' => 'Level', 'searchable' => true, 'field' => 'AssignedToThings.level', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'datetime', 'label' => 'Modified', 'searchable' => true, 'field' => 'AssignedToThings.modified', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'related', 'label' => 'Modified By', 'searchable' => true, 'display_field' => 'name', 'source' => 'users', 'field' => 'AssignedToThings.modified_by', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'string', 'label' => 'label name', 'searchable' => true, 'field' => 'AssignedToThings.name', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'phone', 'label' => 'Phone', 'searchable' => true, 'field' => 'AssignedToThings.phone', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'related', 'label' => 'Primary Thing', 'searchable' => true, 'display_field' => 'name', 'source' => 'things', 'field' => 'AssignedToThings.primary_thing', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'decimal', 'label' => 'Rate', 'searchable' => true, 'field' => 'AssignedToThings.rate', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'decimal', 'label' => 'Salary Amount', 'searchable' => true, 'field' => 'AssignedToThings.salary_amount', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'list', 'label' => 'Salary Currency', 'searchable' => true, 'field' => 'AssignedToThings.salary_currency', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'datetime', 'label' => 'Sample Date', 'searchable' => true, 'field' => 'AssignedToThings.sample_date', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'sublist', 'label' => 'Test List', 'searchable' => true, 'field' => 'AssignedToThings.test_list', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'decimal', 'label' => 'Testmetric Amount', 'searchable' => true, 'field' => 'AssignedToThings.testmetric_amount', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'list', 'label' => 'Testmetric Unit', 'searchable' => true, 'field' => 'AssignedToThings.testmetric_unit', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'decimal', 'label' => 'Testmoney Amount', 'searchable' => true, 'field' => 'AssignedToThings.testmoney_amount', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'list', 'label' => 'Testmoney Currency', 'searchable' => true, 'field' => 'AssignedToThings.testmoney_currency', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'list', 'label' => 'Title', 'searchable' => true, 'field' => 'AssignedToThings.title', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'boolean', 'label' => 'Vip', 'searchable' => true, 'field' => 'AssignedToThings.vip', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'url', 'label' => 'Website', 'searchable' => true, 'field' => 'AssignedToThings.website', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'time', 'label' => 'Work Start', 'searchable' => true, 'field' => 'AssignedToThings.work_start', 'association' => 'oneToMany', 'group' => 'Assigned To'],
                    ['type' => 'reminder', 'label' => 'Appointment', 'searchable' => true, 'field' => 'CreatedByThings.appointment', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'decimal', 'label' => 'Area Amount', 'searchable' => true, 'field' => 'CreatedByThings.area_amount', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'list', 'label' => 'Area Unit', 'searchable' => true, 'field' => 'CreatedByThings.area_unit', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'related', 'label' => 'Assigned To', 'searchable' => true, 'display_field' => 'name', 'source' => 'users', 'field' => 'CreatedByThings.assigned_to', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'blob', 'label' => 'Bio', 'searchable' => true, 'field' => 'CreatedByThings.bio', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'country', 'label' => 'Country', 'searchable' => true, 'field' => 'CreatedByThings.country', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'datetime', 'label' => 'Created', 'searchable' => true, 'field' => 'CreatedByThings.created', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'related', 'label' => 'Created By', 'searchable' => true, 'display_field' => 'name', 'source' => 'users', 'field' => 'CreatedByThings.created_by', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'currency', 'label' => 'Currency', 'searchable' => true, 'field' => 'CreatedByThings.currency', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'date', 'label' => 'Date Of Birth', 'searchable' => true, 'field' => 'CreatedByThings.date_of_birth', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'text', 'label' => 'label description', 'searchable' => true, 'field' => 'CreatedByThings.description', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'email', 'label' => 'Email', 'searchable' => true, 'field' => 'CreatedByThings.email', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'list', 'label' => 'Gender', 'searchable' => true, 'field' => 'CreatedByThings.gender', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'list', 'label' => 'Language', 'searchable' => true, 'field' => 'CreatedByThings.language', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'integer', 'label' => 'Level', 'searchable' => true, 'field' => 'CreatedByThings.level', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'datetime', 'label' => 'Modified', 'searchable' => true, 'field' => 'CreatedByThings.modified', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'related', 'label' => 'Modified By', 'searchable' => true, 'display_field' => 'name', 'source' => 'users', 'field' => 'CreatedByThings.modified_by', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'string', 'label' => 'label name', 'searchable' => true, 'field' => 'CreatedByThings.name', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'phone', 'label' => 'Phone', 'searchable' => true, 'field' => 'CreatedByThings.phone', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'related', 'label' => 'Primary Thing', 'searchable' => true, 'display_field' => 'name', 'source' => 'things', 'field' => 'CreatedByThings.primary_thing', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'decimal', 'label' => 'Rate', 'searchable' => true, 'field' => 'CreatedByThings.rate', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'decimal', 'label' => 'Salary Amount', 'searchable' => true, 'field' => 'CreatedByThings.salary_amount', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'list', 'label' => 'Salary Currency', 'searchable' => true, 'field' => 'CreatedByThings.salary_currency', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'datetime', 'label' => 'Sample Date', 'searchable' => true, 'field' => 'CreatedByThings.sample_date', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'sublist', 'label' => 'Test List', 'searchable' => true, 'field' => 'CreatedByThings.test_list', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'decimal', 'label' => 'Testmetric Amount', 'searchable' => true, 'field' => 'CreatedByThings.testmetric_amount', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'list', 'label' => 'Testmetric Unit', 'searchable' => true, 'field' => 'CreatedByThings.testmetric_unit', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'decimal', 'label' => 'Testmoney Amount', 'searchable' => true, 'field' => 'CreatedByThings.testmoney_amount', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'list', 'label' => 'Testmoney Currency', 'searchable' => true, 'field' => 'CreatedByThings.testmoney_currency', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'list', 'label' => 'Title', 'searchable' => true, 'field' => 'CreatedByThings.title', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'boolean', 'label' => 'Vip', 'searchable' => true, 'field' => 'CreatedByThings.vip', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'url', 'label' => 'Website', 'searchable' => true, 'field' => 'CreatedByThings.website', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'time', 'label' => 'Work Start', 'searchable' => true, 'field' => 'CreatedByThings.work_start', 'association' => 'oneToMany', 'group' => 'Created By'],
                    ['type' => 'reminder', 'label' => 'Appointment', 'searchable' => true, 'field' => 'ModifiedByThings.appointment', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'decimal', 'label' => 'Area Amount', 'searchable' => true, 'field' => 'ModifiedByThings.area_amount', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'list', 'label' => 'Area Unit', 'searchable' => true, 'field' => 'ModifiedByThings.area_unit', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'related', 'label' => 'Assigned To', 'searchable' => true, 'display_field' => 'name', 'source' => 'users', 'field' => 'ModifiedByThings.assigned_to', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'blob', 'label' => 'Bio', 'searchable' => true, 'field' => 'ModifiedByThings.bio', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'country', 'label' => 'Country', 'searchable' => true, 'field' => 'ModifiedByThings.country', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'datetime', 'label' => 'Created', 'searchable' => true, 'field' => 'ModifiedByThings.created', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'related', 'label' => 'Created By', 'searchable' => true, 'display_field' => 'name', 'source' => 'users', 'field' => 'ModifiedByThings.created_by', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'currency', 'label' => 'Currency', 'searchable' => true, 'field' => 'ModifiedByThings.currency', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'date', 'label' => 'Date Of Birth', 'searchable' => true, 'field' => 'ModifiedByThings.date_of_birth', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'text', 'label' => 'label description', 'searchable' => true, 'field' => 'ModifiedByThings.description', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'email', 'label' => 'Email', 'searchable' => true, 'field' => 'ModifiedByThings.email', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'list', 'label' => 'Gender', 'searchable' => true, 'field' => 'ModifiedByThings.gender', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'list', 'label' => 'Language', 'searchable' => true, 'field' => 'ModifiedByThings.language', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'integer', 'label' => 'Level', 'searchable' => true, 'field' => 'ModifiedByThings.level', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'datetime', 'label' => 'Modified', 'searchable' => true, 'field' => 'ModifiedByThings.modified', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'related', 'label' => 'Modified By', 'searchable' => true, 'display_field' => 'name', 'source' => 'users', 'field' => 'ModifiedByThings.modified_by', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'string', 'label' => 'label name', 'searchable' => true, 'field' => 'ModifiedByThings.name', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'phone', 'label' => 'Phone', 'searchable' => true, 'field' => 'ModifiedByThings.phone', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'related', 'label' => 'Primary Thing', 'searchable' => true, 'display_field' => 'name', 'source' => 'things', 'field' => 'ModifiedByThings.primary_thing', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'decimal', 'label' => 'Rate', 'searchable' => true, 'field' => 'ModifiedByThings.rate', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'decimal', 'label' => 'Salary Amount', 'searchable' => true, 'field' => 'ModifiedByThings.salary_amount', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'list', 'label' => 'Salary Currency', 'searchable' => true, 'field' => 'ModifiedByThings.salary_currency', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'datetime', 'label' => 'Sample Date', 'searchable' => true, 'field' => 'ModifiedByThings.sample_date', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'sublist', 'label' => 'Test List', 'searchable' => true, 'field' => 'ModifiedByThings.test_list', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'decimal', 'label' => 'Testmetric Amount', 'searchable' => true, 'field' => 'ModifiedByThings.testmetric_amount', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'list', 'label' => 'Testmetric Unit', 'searchable' => true, 'field' => 'ModifiedByThings.testmetric_unit', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'decimal', 'label' => 'Testmoney Amount', 'searchable' => true, 'field' => 'ModifiedByThings.testmoney_amount', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'list', 'label' => 'Testmoney Currency', 'searchable' => true, 'field' => 'ModifiedByThings.testmoney_currency', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'list', 'label' => 'Title', 'searchable' => true, 'field' => 'ModifiedByThings.title', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'boolean', 'label' => 'Vip', 'searchable' => true, 'field' => 'ModifiedByThings.vip', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'url', 'label' => 'Website', 'searchable' => true, 'field' => 'ModifiedByThings.website', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'time', 'label' => 'Work Start', 'searchable' => true, 'field' => 'ModifiedByThings.work_start', 'association' => 'oneToMany', 'group' => 'Modified By'],
                    ['type' => 'boolean', 'label' => 'Active', 'searchable' => true, 'field' => 'SocialAccounts.active', 'association' => 'oneToMany', 'group' => 'User Id'],
                    ['type' => 'string', 'label' => 'Avatar', 'searchable' => true, 'field' => 'SocialAccounts.avatar', 'association' => 'oneToMany', 'group' => 'User Id'],
                    ['type' => 'datetime', 'label' => 'Created', 'searchable' => true, 'field' => 'SocialAccounts.created', 'association' => 'oneToMany', 'group' => 'User Id'],
                    ['type' => 'text', 'label' => 'Data', 'searchable' => true, 'field' => 'SocialAccounts.data', 'association' => 'oneToMany', 'group' => 'User Id'],
                    ['type' => 'text', 'label' => 'Description', 'searchable' => true, 'field' => 'SocialAccounts.description', 'association' => 'oneToMany', 'group' => 'User Id'],
                    ['type' => 'datetime', 'label' => 'Modified', 'searchable' => true, 'field' => 'SocialAccounts.modified', 'association' => 'oneToMany', 'group' => 'User Id'],
                    ['type' => 'string', 'label' => 'Provider', 'searchable' => true, 'field' => 'SocialAccounts.provider', 'association' => 'oneToMany', 'group' => 'User Id'],
                    ['type' => 'string', 'label' => 'Reference', 'searchable' => true, 'field' => 'SocialAccounts.reference', 'association' => 'oneToMany', 'group' => 'User Id'],
                    ['type' => 'string', 'label' => 'Token', 'searchable' => true, 'field' => 'SocialAccounts.token', 'association' => 'oneToMany', 'group' => 'User Id'],
                    ['type' => 'datetime', 'label' => 'Token Expires', 'searchable' => true, 'field' => 'SocialAccounts.token_expires', 'association' => 'oneToMany', 'group' => 'User Id'],
                    ['type' => 'string', 'label' => 'Token Secret', 'searchable' => true, 'field' => 'SocialAccounts.token_secret', 'association' => 'oneToMany', 'group' => 'User Id'],
                    ['type' => 'string', 'label' => 'Username', 'searchable' => true, 'field' => 'SocialAccounts.username', 'association' => 'oneToMany', 'group' => 'User Id'],
                    ['type' => 'datetime', 'label' => 'Activation Date', 'searchable' => true, 'field' => 'Users.activation_date', 'group' => 'Users'],
                    ['type' => 'boolean', 'label' => 'Active', 'searchable' => true, 'field' => 'Users.active', 'group' => 'Users'],
                    ['type' => 'text', 'label' => 'Additional Data', 'searchable' => true, 'field' => 'Users.additional_data', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Api Token', 'searchable' => true, 'field' => 'Users.api_token', 'group' => 'Users'],
                    ['type' => 'date', 'label' => 'Birthdate', 'searchable' => true, 'field' => 'Users.birthdate', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Company', 'searchable' => true, 'field' => 'Users.company', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Country', 'searchable' => true, 'field' => 'Users.country', 'group' => 'Users'],
                    ['type' => 'datetime', 'label' => 'Created', 'searchable' => true, 'field' => 'Users.created', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Department', 'searchable' => true, 'field' => 'Users.department', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Email', 'searchable' => true, 'field' => 'Users.email', 'group' => 'Users'],
                    ['type' => 'text', 'label' => 'Extras', 'searchable' => true, 'field' => 'Users.extras', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Fax', 'searchable' => true, 'field' => 'Users.fax', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'First Name', 'searchable' => true, 'field' => 'Users.first_name', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Gender', 'searchable' => true, 'field' => 'Users.gender', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Initials', 'searchable' => true, 'field' => 'Users.initials', 'group' => 'Users'],
                    ['type' => 'boolean', 'label' => 'Is Superuser', 'searchable' => true, 'field' => 'Users.is_superuser', 'group' => 'Users'],
                    ['type' => 'boolean', 'label' => 'Is Supervisor', 'searchable' => true, 'field' => 'Users.is_supervisor', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Last Name', 'searchable' => true, 'field' => 'Users.last_name', 'group' => 'Users'],
                    ['type' => 'datetime', 'label' => 'Modified', 'searchable' => true, 'field' => 'Users.modified', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Password', 'searchable' => true, 'field' => 'Users.password', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Phone Extension', 'searchable' => true, 'field' => 'Users.phone_extension', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Phone Home', 'searchable' => true, 'field' => 'Users.phone_home', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Phone Mobile', 'searchable' => true, 'field' => 'Users.phone_mobile', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Phone Office', 'searchable' => true, 'field' => 'Users.phone_office', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Position', 'searchable' => true, 'field' => 'Users.position', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Role', 'searchable' => true, 'field' => 'Users.role', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Secret', 'searchable' => true, 'field' => 'Users.secret', 'group' => 'Users'],
                    ['type' => 'boolean', 'label' => 'Secret Verified', 'searchable' => true, 'field' => 'Users.secret_verified', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Team', 'searchable' => true, 'field' => 'Users.team', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Token', 'searchable' => true, 'field' => 'Users.token', 'group' => 'Users'],
                    ['type' => 'datetime', 'label' => 'Token Expires', 'searchable' => true, 'field' => 'Users.token_expires', 'group' => 'Users'],
                    ['type' => 'datetime', 'label' => 'Tos Date', 'searchable' => true, 'field' => 'Users.tos_date', 'group' => 'Users'],
                    ['type' => 'string', 'label' => 'Username', 'searchable' => true, 'field' => 'Users.username', 'group' => 'Users'],
                ],
            ],
        ];
    }
}
