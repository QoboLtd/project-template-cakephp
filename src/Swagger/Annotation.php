<?php

namespace App\Swagger;

use Cake\Core\App;
use Cake\Database\Exception;
use Cake\Database\Type;
use Cake\Database\TypeInterface;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Cake\Utility\Text;
use CsvMigrations\FieldHandlers\Config\ListConfig;
use CsvMigrations\FieldHandlers\CsvField;
use CsvMigrations\FieldHandlers\FieldHandlerFactory;
use CsvMigrations\FieldHandlers\Provider\SelectOptions\ListSelectOptions;
use CsvMigrations\Model\Table\DblistsTable;
use Qobo\Utils\Module\ModuleRegistry;
use Qobo\Utils\Utility;
use Webmozart\Assert\Assert;

class Annotation
{
    /**
     * Annotation content.
     *
     * @var string
     */
    protected $content = '';

    /**
     * Class name to generate annotations for.
     *
     * @var string
     */
    protected $className = '';

    /**
     * Full path name of the file to generate annotations for.
     *
     * @var string
     */
    protected $path = '';

    /**
     * Flag for including Swagger Info annotation.
     *
     * @var bool
     */
    protected $withInfo = false;

    /**
     * Swagger annotations.
     *
     * @var array
     */
    protected $annotations = [
        'info' => '/**
            @SWG\Swagger(
                @SWG\Info(
                    title="API Documentation",
                    description="Interactive API documentation powered by Swagger.io",
                    termsOfService="http://swagger.io/terms/",
                    version="{{version}}"
                ),
                @SWG\SecurityScheme(
                    securityDefinition="Bearer",
                    description="Json Web Tokens (JWT)",
                    type="apiKey",
                    name="token",
                    in="query"
                )
            )
        */',
        'definition' => '/**
            @SWG\Definition(
                definition="{{definition}}",
                required={{required}},
                {{properties}}
            )
         */',
        'property' => '
            @SWG\Property(
                property="{{property}}",
                {{options}}
            )',
        'paths' => '/**
            @SWG\Get(
                path="/api/{{module_url}}",
                summary="Retrieve a list of {{module_human_plural}}",
                tags={"{{module_human_plural}}"},
                produces={"application/json"},
                @SWG\Parameter(
                    name="conditions[id][]",
                    description="Conditions",
                    in="query",
                    required=false,
                    type="string",
                    default={{random_uuid}}
                ),
                @SWG\Parameter(
                    name="conditions[name][]",
                    description="Conditions",
                    in="query",
                    required=false,
                    type="string",
                    default={{random_name2}}
                ),
                @SWG\Parameter(
                    name="conditions[name][]",
                    description="Conditions",
                    in="query",
                    required=false,
                    type="string",
                    default={{random_name}}
                ),
                @SWG\Parameter(
                    name="limit",
                    description="Results limit",
                    in="query",
                    required=false,
                    type="integer",
                    default=""
                ),
                @SWG\Parameter(
                    name="sort",
                    description="Sort results by field",
                    in="query",
                    required=false,
                    type="string",
                    enum={ {{sort_fields}} }
                ),
                @SWG\Parameter(
                    name="direction",
                    description="Sorting direction",
                    in="query",
                    required=false,
                    type="string",
                    enum={"asc", "desc"}
                ),
                @SWG\Parameter(
                    name="format",
                    description="Prettify response values",
                    in="query",
                    required=false,
                    type="string",
                    enum={"pretty"}
                ),
                @SWG\Response(
                    response="200",
                    description="Successful operation",
                    @SWG\Schema(
                        title="Response",
                        @SWG\Property(
                            type="boolean",
                            property="success"
                        ),
                        @SWG\Property(
                            property="data",
                            type="array",
                            @SWG\Items(
                                ref="#/definitions/{{module_singular}}"
                            ),
                        ),
                        @SWG\Property(
                            title="pagination",
                            property="pagination",
                            @SWG\Property(
                                property="page_count",
                                type="integer",
                                example=1
                            ),
                            @SWG\Property(
                                property="current_page",
                                type="integer",
                                example=1
                            ),
                            @SWG\Property(
                                property="has_next_page",
                                type="boolean",
                                example=true
                            ),
                            @SWG\Property(
                                property="has_prev_page",
                                type="boolean",
                                example=false
                            ),
                            @SWG\Property(
                                property="count",
                                type="integer",
                                example=2
                            ),
                            @SWG\Property(
                                property="limit",
                                type="integer",
                                example=4
                            )
                        ),
                    )
                ),
                @SWG\Response(
                    response="500",
                    description="Unsuccessful operation"
                )
            )

            @SWG\Get(
                path="/api/{{module_url}}/view/{id}",
                summary="Retrieve a {{module_human_singular}} by ID",
                tags={"{{module_human_plural}}"},
                consumes={"application/json"},
                produces={"application/json"},
                @SWG\Parameter(
                    name="id",
                    description="{{module_human_singular}} ID",
                    in="path",
                    required=true,
                    type="string",
                    default=""
                ),
                @SWG\Response(
                    response="200",
                    description="Successful operation",
                    @SWG\Schema(
                        ref="#/definitions/{{module_singular}}"
                    )
                ),
                @SWG\Response(
                    response="404",
                    description="Not found"
                )
            )

            @SWG\Get(
                path="/api/{{module_url}}/lookup",
                summary="Retrieve a list of records {{module_human_singular}} by name",
                tags={"{{module_human_plural}}"},
                produces={"application/json"},
                @SWG\Parameter(
                    name="query",
                    description="{{module_human_singular}} Name",
                    in="query",
                    required=false,
                    type="string",
                    default=""
                ),
                @SWG\Parameter(
                    name="limit",
                    description="Limit results",
                    in="query",
                    required=false,
                    type="integer",
                    default=""
                ),
                @SWG\Parameter(
                    name="page",
                    description="Page Number",
                    in="query",
                    required=false,
                    type="integer",
                    default=""
                ),
                @SWG\Response(
                    response="200",
                    description="Successful operation",
                    @SWG\Schema(
                        title="Response",
                        @SWG\Property(
                            type="boolean",
                            property="success"
                        ),
                        @SWG\Property(
                            type="object",
                            property="data",
                            example={{{random_uuid}}:{{random_name}}, {{random_uuid}}:{{random_name}}}
                        ),
                        @SWG\Property(
                            title="pagination",
                            property="pagination",
                            @SWG\Property(
                                property="page_count",
                                type="integer",
                                example=1
                            ),
                            @SWG\Property(
                                property="current_page",
                                type="integer",
                                example=1
                            ),
                            @SWG\Property(
                                property="has_next_page",
                                type="boolean",
                                example=true
                            ),
                            @SWG\Property(
                                property="has_prev_page",
                                type="boolean",
                                example=false
                            ),
                            @SWG\Property(
                                property="count",
                                type="integer",
                                example=2
                            ),
                            @SWG\Property(
                                property="limit",
                                type="integer",
                                example=4
                            )
                        ),
                    )
                ),
                @SWG\Response(
                    response="500",
                    description="Unsuccessful operation"
                )
            )
            @SWG\Post(
                path="/api/{{module_url}}/search",
                summary="Search {{module_human_singular}}",
                tags={"{{module_human_plural}}"},
                consumes={"application/json"},
                produces={"application/json"},
                @SWG\Parameter(
                    name="body",
                    in="body",
                    description="{{module_human_singular}} object for search",
                    required=true,
                    @SWG\Schema(
                        @SWG\Property(
                            property="direction",
                            type="string",
                            format="list",
                            example="DESC",
                            description="Sorting direction"
                        ),
                        @SWG\Property(
                            property="fields",
                            type="array",
                            items= {
                                "type": "string"
                            },
                            description="",
                            example={{search_fields}}
                        ),
                        @SWG\Property(
                            property="limit",
                            type="string",
                            description="",
                            example=10
                        ),
                        @SWG\Property(
                            property="page",
                            type="string",
                            description="Page number",
                            example=1
                        ),
                        @SWG\Property(
                            property="sort",
                            type="string",
                            description="Sorting field",
                            example="{{module_human_plural}}.name"
                        ),
                        @SWG\Property(
                            property="criteria",
                            description="Criteria field",
                            @SWG\Property(
                                property="{{module_human_plural}}.name",
                                type="object",
                                description="Criteria",
                                @SWG\Property(
                                    property={{random_uuid}},
                                    description="Criteria field",
                                    @SWG\Property(
                                        property="type",
                                        type="string",
                                        description="Field type",
                                        example="string",
                                        enum={"string", "boolean", "list", "related", "integer"},

                                    ),
                                    @SWG\Property(
                                        property="operator",
                                        type="string",
                                        description="Operator",
                                        example="contains",
                                        enum={"contains", "not_contains", "is", "is_not", "less", "greater"},
                                    ),
                                    @SWG\Property(
                                        property="value",
                                        type="array",
                                        items= {
                                            "type": "string",
                                            "type": "integer"
                                        },
                                        description="Search value",
                                    ),
                                    example={{search_criteria_example}},
                                ),
                                example={{search_criteria_example}},
                            )
                        ),
                        @SWG\Property(
                            property="group_by",
                            type="string",
                            description="Group by field",
                            example=""
                        ),
                        @SWG\Property(
                            property="conjunction",
                            type="string",
                            description="Conjunction field",
                            enum={"AND","OR"}
                        )

                    ),
                ),
                @SWG\Response(
                    response="200",
                    description="Successful operation",
                    @SWG\Schema(
                        title="Response",
                        @SWG\Property(
                            type="boolean",
                            property="success"
                        ),
                        @SWG\Property(
                            type="array",
                            property="data",
                            items= {
                                "type": "array"
                            },
                            example={
                                "{{module_human_plural}}.id": {{random_uuid}},
                                "_permissions": {
                                    "view": true,
                                    "edit": true,
                                    "delete": true
                                }
                            }
                        ),
                        @SWG\Property(
                            title="pagination",
                            property="pagination",
                            @SWG\Property(
                                property="page_count",
                                type="integer",
                                example=1
                            ),
                            @SWG\Property(
                                property="current_page",
                                type="integer",
                                example=1
                            ),
                            @SWG\Property(
                                property="has_next_page",
                                type="boolean",
                                example=true
                            ),
                            @SWG\Property(
                                property="has_prev_page",
                                type="boolean",
                                example=false
                            ),
                            @SWG\Property(
                                property="count",
                                type="integer",
                                example=2
                            ),
                            @SWG\Property(
                                property="limit",
                                type="integer",
                                example=4
                            )
                        ),
                    )
                ),
                @SWG\Response(
                    response="500",
                    description="Unsuccessful operation"
                ),
              ),
            )

            @SWG\Post(
                path="/api/{{module_url}}/add",
                summary="Add new {{module_human_singular}}",
                tags={"{{module_human_plural}}"},
                consumes={"application/json"},
                produces={"application/json"},
                @SWG\Parameter(
                    name="body",
                    in="body",
                    description="{{module_human_singular}} object to be added to the system",
                    required=true,
                    @SWG\Schema(ref="#/definitions/{{module_singular}}")
                ),
                @SWG\Response(
                    response="201",
                    description="Successful operation"
                )
            )

            @SWG\Put(
                path="/api/{{module_url}}/edit/{id}",
                summary="Edit an existing {{module_human_singular}}",
                tags={"{{module_human_plural}}"},
                consumes={"application/json"},
                produces={"application/json"},
                @SWG\Parameter(
                    name="id",
                    description="{{module_human_singular}} ID",
                    in="path",
                    required=true,
                    type="string",
                    default=""
                ),
                @SWG\Parameter(
                    name="body",
                    in="body",
                    description="{{module_human_singular}} name",
                    required=true,
                    @SWG\Schema(ref="#/definitions/{{module_singular}}")
                ),
                @SWG\Response(
                    response="200",
                    description="Successful operation"
                ),
                @SWG\Response(
                    response="404",
                    description="Not found"
                )
            )

            @SWG\Delete(
                path="/api/{{module_url}}/delete/{id}",
                summary="Delete a {{module_human_singular}}",
                tags={"{{module_human_plural}}"},
                consumes={"application/json"},
                produces={"application/json"},
                @SWG\Parameter(
                    name="id",
                    description="{{module_human_singular}} ID",
                    in="path",
                    required=true,
                    type="string",
                    default=""
                ),
                @SWG\Response(
                    response="200",
                    description="Successful operation"
                ),
                @SWG\Response(
                    response="404",
                    description="Not found"
                )
            )
        */',
    ];

    /**
     * Constructor method.
     *
     * @param string $className Class name
     * @param string $path File path
     * @param bool $withInfo Info annotation flag
     * @return void
     */
    public function __construct(string $className, string $path, bool $withInfo = false)
    {
        $this->className = $className;

        $this->path = $path;
        $this->withInfo = $withInfo;
    }

    /**
     * Swagger annotation content getter.
     *
     * @return string
     */
    public function getContent(): string
    {
        if ('' !== trim($this->content)) {
            return $this->content;
        }

        $replacement = [$this->getInfo(), $this->getDefinition($this->getProperties()), $this->getPaths()];
        $replacement = implode("\n", $replacement) . "\n$1";
        $content = (string)file_get_contents($this->path);

        $pattern = '/(^class\s)/im';
        $content = preg_replace($pattern, $replacement, $content);
        $content = trim((string)$content);

        $this->setContent($content);

        return $content;
    }

    /**
     * Swagger annotation content setter.
     *
     * @param string $content The content
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * Swagge Info annotation generator.
     *
     * @return string
     */
    protected function getInfo(): string
    {
        if (! $this->withInfo) {
            return '';
        }

        $versions = Utility::getApiVersions(App::path('Controller/Api')[0]);

        $placeholders = [
            '{{version}}' => $versions[0]['number'],
        ];

        return str_replace(
            array_keys($placeholders),
            array_values($placeholders),
            $this->annotations['info']
        );
    }

    /**
     * Generates and returns swagger properties annotation.
     *
     * It uses current table's column definitions to generate
     * swagger property annotation on the fly.
     *
     * @return string
     */
    protected function getProperties(): string
    {
        $columns = $this->getColumnsFromConfig();
        if (empty($columns)) {
            $columns = $this->getColumnsFromSchema();
        }

        $result = [];
        foreach ($columns as $column) {
            $placeholders = [
                '{{property}}' => $column['db_field']->getName(),
                '{{options}}' => $this->getPropertyOptionsAsString($column),
            ];

            $result[] = str_replace(
                array_keys($placeholders),
                array_values($placeholders),
                $this->annotations['property']
            );
        }

        return implode(',', $result);
    }

    /**
     * Retrieve column definitions from module configuration.
     *
     * @return mixed[]
     */
    private function getColumnsFromConfig(): array
    {
        $factory = new FieldHandlerFactory();
        $table = TableRegistry::getTableLocator()->get($this->className);
        $config = ModuleRegistry::getModule($this->className)->getMigration();

        $result = [];
        foreach ($config as $column) {
            $csvField = new CsvField($column);
            foreach ($factory->fieldToDb(new CsvField($column), $table) as $field) {
                $result[] = ['field' => $csvField, 'db_field' => $field];
            }
        }

        return $result;
    }

    /**
     * Retrieve column definitions from table schema.
     *
     * @return mixed[]
     */
    private function getColumnsFromSchema(): array
    {
        $result = [];

        $factory = new FieldHandlerFactory();
        $table = TableRegistry::getTableLocator()->get($this->className);
        $columns = array_diff($table->getSchema()->columns(), $table->newEntity()->getHidden());
        foreach ($columns as $column) {
            $type = $table->getSchema()->getColumnType($column);
            /** @var string|\Cake\Database\TypeInterface|null */
            $typeMap = Type::getMap($type);
            $typeMap = $typeMap instanceof TypeInterface ? $typeMap->getName() : $typeMap;

            // handle custom database types
            if (null === $typeMap || false === strpos($typeMap, 'Cake\\Database\\Type\\')) {
                $type = 'string';
            }
            $column = [
                'name' => $column,
                'type' => $type,
                'required' => ! $table->getSchema()->isNullable($column),
                'non-searchable' => null,
                'unique' => null,
            ];

            $csvField = new CsvField($column);
            foreach ($factory->fieldToDb(new CsvField($column), $table) as $field) {
                $result[] = ['field' => $csvField, 'db_field' => $field];
            }
        }

        return $result;
    }

    /**
     * Returns property options as stirng.
     *
     * @param mixed[] $conf Field conf
     * @return string
     */
    protected function getPropertyOptionsAsString(array $conf): string
    {
        $result = [];
        foreach ($this->getPropertyOptions($conf) as $key => $value) {
            switch (gettype($value)) {
                case 'boolean':
                    $value = $value ? 'true' : 'false';
                    break;

                case 'string':
                    $value = '"' . $value . '"';
                    break;

                case 'array':
                    $value = json_encode($value, JSON_FORCE_OBJECT);
                    break;

                default:
                    $value = '"' . $value . '"';
            }

            $result[] = sprintf('%s=%s', $key, $value);
        }

        return implode(",\n\t\t", $result);
    }

    /**
     * Returns property options.
     *
     * @param mixed[] $conf Field conf
     * @return mixed[]
     */
    protected function getPropertyOptions(array $conf): array
    {
        $type = $conf['field']->getType();
        if (in_array($conf['field']->getType(), ['money', 'metric'])) {
            $type = 'string' === $conf['db_field']->getType() ? 'list' : $conf['db_field']->getType();
        }

        $result = [];
        switch ($type) {
            case 'uuid':
                $result = ['type' => 'string', 'format' => 'uuid', 'description' => 'UUID', 'example' => Text::uuid()];
                break;

            case 'related':
            case 'files':
            case 'images':
                $result = [
                    'type' => 'string', 'format' => 'uuid', 'description' => 'UUID (related)', 'example' => Text::uuid(),
                ];
                break;

            case 'text':
                $result = [
                    'type' => 'string',
                    'format' => 'text',
                    'example' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod ' .
                        'tempor incididunt ut labore et dolore magna aliqua.',
                ];
                break;

            case 'boolean':
                $result = ['type' => 'boolean', 'format' => 'boolean', 'example' => true];
                break;

            case 'datetime':
            case 'reminder':
                $result = [
                    'type' => 'string',
                    'format' => 'date-time',
                    'example' => Time::now()->i18nFormat('yyyy-MM-dd HH:mm:ss'),
                ];
                break;

            case 'date':
                $result = ['type' => 'string', 'format' => 'date', 'example' => Time::now()->i18nFormat('yyyy-MM-dd')];
                break;

            case 'time':
                $result = ['type' => 'string', 'format' => 'time', 'example' => Time::now()->i18nFormat('HH:mm:ss')];
                break;

            case 'decimal':
                $result = [
                    'type' => 'number',
                    'format' => 'float',
                    'example' => (mt_rand() / mt_getrandmax()) * (mt_getrandmax() / 1000),
                ];
                break;

            case 'integer':
                $result = ['type' => 'integer', 'format' => 'integer', 'example' => mt_rand()];
                break;

            case 'list':
            case 'sublist':
            case 'dblist':
                $options = 'dblist' === $type ?
                    $this->getDatabaseList($conf['field']->getLimit()) :
                    $this->getList($conf['field']->getLimit());
                $options = empty($options) ? [''] : $options;
                $opts = array_keys($options);
                $description = '';
                foreach ($options as $k => $v) {
                    $description .= sprintf("\n* '%s' : '%s'", $k, $v);
                }
                $result = [
                    'type' => 'string',
                    'format' => 'list',
                    'example' => $opts[array_rand($opts)],
                    'enum' => $opts,
                    'description' => $description,
                ];
                break;

            case 'email':
                $result = ['type' => 'string', 'format' => 'email', 'example' => str_shuffle('abcdef') . '@qobo.biz'];
                break;

            case 'phone':
                $result = [
                    'type' => 'string', 'format' => 'phone', 'example' => '+357-' . (string)rand(95000000, 99999999),
                ];
                break;

            case 'url':
                $result = ['type' => 'string', 'format' => 'url', 'example' => Router::url('/', true) . uniqid()];
                break;

            case 'blob':
                $result = [
                    'type' => 'string',
                    'format' => 'blob',
                    'example' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAAK0lEQV' .
                        'R42u3NMQEAAAQAMA79wwhIDI6twLKqJw6kWCwWi8VisVgsFov/xgt4rjQnLeKjCAAAAABJRU5ErkJggg==',
                ];
                break;

            case 'string':
            default:
                $result = ['type' => 'string', 'format' => 'string', 'example' => 'Lorem ipsum'];
        }

        return $result;
    }

    /**
     * File based list getter.
     *
     * @param string $listName List name
     * @return mixed[]
     */
    private function getList(string $listName): array
    {
        $provider = new ListSelectOptions(new ListConfig($listName, $this->className));
        $options = $provider->provide($listName, ['flatten' => true, 'filter' => true]);

        return $options;
    }

    /**
     * Database list getter.
     *
     * @param string $listName List name
     * @return mixed[]
     */
    private function getDatabaseList(string $listName): array
    {
        $table = TableRegistry::getTableLocator()->get('CsvMigrations.Dblists');
        Assert::isInstanceOf($table, DblistsTable::class);

        return $table->getOptions($listName);
    }

    /**
     * Generates and returns swagger definition (model) annotation.
     *
     * It uses current table's column definitions to construct a list
     * of required columns and uses properties argument to generate
     * definition annotation.
     *
     * @param  string $properties Swagger properties annotations
     * @return string
     */
    protected function getDefinition(string $properties): string
    {
        $result = '';
        $table = TableRegistry::getTableLocator()->get($this->className);

        $entity = $table->newEntity();
        try {
            $columns = $table->getSchema()->columns();
            $columns = array_diff($columns, $entity->getHidden());
        } catch (Exception $e) {
            return $result;
        }

        $required = [];
        foreach ($columns as $column) {
            $data = $table->getSchema()->getColumn($column);
            if ($data['null']) {
                continue;
            }
            $required[] = $column;
        }

        $placeholders = [
            '{{definition}}' => Inflector::singularize($this->className),
            '{{required}}' => json_encode($required, JSON_FORCE_OBJECT),
            '{{properties}}' => $properties,
        ];

        $result = str_replace(
            array_keys($placeholders),
            array_values($placeholders),
            $this->annotations['definition']
        );

        return $result;
    }

    /**
     * Generates and returns swagger paths (controller) annotation.
     *
     * It uses current table's column definitions to construct a list
     * of all visible columns to be used as sorting fields and generates
     * paths annotations on the fly.
     *
     * @return string
     */
    protected function getPaths(): string
    {
        $table = TableRegistry::getTableLocator()->get($this->className);

        $entity = $table->newEntity();
        try {
            $fields = $table->getSchema()->columns();
            $fields = array_diff($fields, $entity->getHidden());
            sort($fields);
        } catch (Exception $e) {
            return '';
        }

        $placeholders = [
            '{{module_human_singular}}' => Inflector::singularize(Inflector::humanize(Inflector::underscore($this->className))),
            '{{module_human_plural}}' => Inflector::pluralize(Inflector::humanize(Inflector::underscore($this->className))),
            '{{module_singular}}' => Inflector::singularize($this->className),
            '{{module_url}}' => Inflector::dasherize($this->className),
            '{{sort_fields}}' => '"' . implode('", "', $fields) . '"',
            '{{random_uuid}}' => '"' . Text::uuid() . '"',
            '{{random_name}}' => '"' . self::randomName() . '"',
            '{{random_name2}}' => '"' . self::randomName() . '"',
            '{{search_fields}}' => self::getSearchFieldsObjectAsString(),
            '{{search_criteria_example}}' => self::getSearchExampleObjectAsString(),
        ];

        return str_replace(
            array_keys($placeholders),
            array_values($placeholders),
            $this->annotations['paths']
        );
    }

    /**
     * Return Search fields object as string
     * @return string
     */
    protected function getSearchFieldsObjectAsString(): string
    {
        $columns = $this->getColumnsFromConfig();
        if (empty($columns)) {
            $columns = $this->getColumnsFromSchema();
        }

        $result = [];
        foreach ($columns as $column) {
            $result[] = $this->className . '.' . $column['db_field']->getName();
        }

        return (string)json_encode($result, JSON_FORCE_OBJECT);
    }

    /**
     * Return search examples for Search endpoint
     * @return string
     */
    protected function getSearchExampleObjectAsString(): string
    {
        $examples = [];

        $examples[] = [
            (string)Text::uuid() => ["type" => "boolean", "operator" => "is", "value" => 1],
            (string)Text::uuid() => ["type" => "boolean", "operator" => "is_not", "value" => 8],
        ];

        $examples[] = [
            (string)Text::uuid() => ["type" => "string", "operator" => "contains", "value" => 1],
        ];
        $examples[] = [
            (string)Text::uuid() => ["type" => "string", "operator" => "not_contains", "value" => 1],
        ];

        // //List Examples
        $examples[] = [
            (string)Text::uuid() => ["type" => "list", "operator" => "is", "value" => [self::randomName()]],
        ];
        $examples[] = [
            (string)Text::uuid() => ["type" => "list", "operator" => "is", "value" => [self::randomName(), self::randomName()]],
        ];

        // //Related Examples
        $examples[] = [
            (string)Text::uuid() => ["type" => "related", "operator" => "is", "value" => [(string)Text::uuid()]],
        ];
        $examples[] = [
            (string)Text::uuid() => ["type" => "related", "operator" => "is", "value" => [(string)Text::uuid(), (string)Text::uuid()]],
        ];

        // //Integer Examples
        $examples[] = [
            (string)Text::uuid() => ["type" => "integer", "operator" => "greater", "value" => "1"],
            (string)Text::uuid() => ["type" => "integer", "operator" => "less", "value" => "5"],
        ];
        $examples[] = [
            (string)Text::uuid() => ["type" => "integer", "operator" => "less", "value" => "5"],
        ];
        $examples[] = [
            (string)Text::uuid() => ["type" => "integer", "operator" => "is", "value" => "6"],
        ];
        $examples[] = [
            (string)Text::uuid() => ["type" => "integer", "operator" => "is_not", "value" => "7"],
        ];

        $randomNumber = random_int(0, count($examples) - 1);

        return (string)json_encode($examples[$randomNumber], JSON_FORCE_OBJECT);
    }

    /**
     * Return a random name
     * @return string
     */
    protected function randomName(): string
    {
        $names = [
            'Roxie',
            'Toi',
            'Kori',
            'Paris',
            'Alessandra',
            'Jone',
            'Dawn',
            'Prince',
            'Tammie',
            'Penny',
            'Lincoln',
            'Carlo',
            'Kayleigh',
            'Angila',
            'Ruben',
            'Lu',
            'Hattie',
            'Claretta',
            'Carrol',
            'Estefana',
            'Rosalina',
            'Sidney',
            'Callie',
            'Emely',
            'Robt',
            'Vena',
            'Rubie',
            'Scarlet',
            'Ernest',
            'Keneth',
            'Eleanora',
            'Freeda',
            'Donya',
            'Sylvester',
            'Charleen',
            'Robyn',
            'Wiley',
            'Pandora',
            'Krystal',
            'Bonita',
            'Londa',
            'Jay',
            'Mimi',
            'Britney',
            'Bobbie',
            'Christy',
            'Denisse',
            'Waltraud',
            'Noah',
            'Barbra',
        ];

        $randomNumber = random_int(0, count($names) - 1);

        return (string)$names[$randomNumber];
    }
}
