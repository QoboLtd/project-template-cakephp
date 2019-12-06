<?php

namespace App\Event\Controller\Api;

use Cake\Core\App;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\RepositoryInterface;
use Cake\Event\EventListenerInterface;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\View\View;
use CsvMigrations\FieldHandlers\FieldHandlerFactory;
use CsvMigrations\Utility\FileUpload;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Webmozart\Assert\Assert;

abstract class BaseActionListener implements EventListenerInterface
{
    /**
     * Pretty format identifier
     */
    public const FORMAT_PRETTY = 'pretty';

    /**
     * Include menus identifier
     */
    public const FLAG_INCLUDE_MENUS = 'menus';

    /**
     * Property name for menu items
     */
    public const MENU_PROPERTY_NAME = '_Menus';

    /**
     * FieldHandlerFactory instance.
     *
     * @var \CsvMigrations\FieldHandlers\FieldHandlerFactory
     */
    private $factory = null;

    /**
     * View instance.
     *
     * @var \Cake\View\View
     */
    private $view = null;

    /**
     * FileUpload instance.
     *
     * @var \CsvMigrations\Utility\FileUpload
     */
    private $fileUpload = null;

    /**
     * Current controller name.
     *
     * @var string
     */
    private $controllerName = '';

    /**
     * Fetch and attach associated files to provided entity.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity
     * @param \Cake\ORM\Table $table Table instance
     * @return void
     */
    protected function attachFiles(EntityInterface $entity, Table $table): void
    {
        $primaryKey = $table->getPrimaryKey();
        if (! is_string($primaryKey)) {
            throw new RuntimeException('Primary key must be a string');
        }

        $fileUpload = $this->getFileUpload($table);

        foreach ($this->getFileAssociations($table) as $association) {
            $conditions = $association->getConditions();
            if (! is_array($conditions)) {
                continue;
            }

            if (! array_key_exists('model_field', $conditions)) {
                continue;
            }

            $entity->set(
                $conditions['model_field'],
                $fileUpload->getFiles($conditions['model_field'], $entity->get($primaryKey))
            );
        }
    }

    /**
     * Method responsible for retrieving current table's file associations.
     *
     * @param \Cake\ORM\Table $table Table instance
     * @return \Cake\ORM\Association[]
     */
    protected function getFileAssociations(Table $table): array
    {
        $result = [];
        foreach ($table->associations() as $association) {
            if (FileUpload::FILE_STORAGE_TABLE_NAME !== $association->className()) {
                continue;
            }

            $result[] = $association;
        }

        return $result;
    }

    /**
     * Convert Entity resource values to strings.
     * Temporary fix for bug with resources and json_encode() (see link).
     *
     * @param  \Cake\Datasource\EntityInterface $entity Entity
     * @return void
     * @link https://github.com/cakephp/cakephp/issues/9658
     */
    protected function resourceToString(EntityInterface $entity): void
    {
        foreach (array_keys($entity->toArray()) as $field) {
            if (is_resource($entity->get($field))) {
                $entity->set($field, stream_get_contents($entity->get($field)));
            }
        }
    }

    /**
     * Method that renders Entity values through Field Handler Factory.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity instance
     * @param \Cake\ORM\Table $table Table instance
     * @param string[] $fields Fields to prettify
     * @return void
     */
    protected function prettify(EntityInterface $entity, Table $table, array $fields = []): void
    {
        $fields = empty($fields) ? array_keys($entity->toArray()) : $fields;

        /**
         * @var \CsvMigrations\FieldHandlers\FieldHandlerFactory
         */
        $factory = $this->getFieldHandlerFactory();

        //Find fields that has the following suffixes
        $prettifySuffixesFields = preg_grep("/^.*\_(unit|amount|currency)$/", $fields);

        foreach ($fields as $field) {
            // Fix alias set by pagination hask
            $table->setAlias(Inflector::camelize($table->getTable()));

            if (!in_array($field, $prettifySuffixesFields)) {
                $entity->set($field, $factory->renderValue($table, $field, $entity->get($field), ['entity' => $entity]));
                continue;
            }

            //Process prettified field
            $unit = preg_match("/^.*\_unit$/", $field);
            $amount = preg_match("/^.*\_amount$/", $field);
            $currency = preg_match("/^.*\_currency$/", $field);

            $fieldDefinitions = [];

            if ($unit) {
                $fieldDefinitions = [
                    'type' => 'list(units_area)'
                ];
            } elseif ($amount) {
                $fieldDefinitions = [
                    'type' => 'decimal'
                ];
            } elseif ($currency) {
                $fieldDefinitions = [
                    'type' => 'currency(currencies)'
                ];
            }

            $entity->set($field, $factory->renderValue($table, $field, $entity->get($field), ['entity' => $entity, 'fieldDefinitions' => $fieldDefinitions]));
        }
    }

    /**
     * Query order clause getter.
     *
     * This is a temporary solution for multi-column sort support,
     * until crud plugin adds relevant functionality.
     * @link https://github.com/FriendsOfCake/crud/issues/522
     * @link https://github.com/cakephp/cakephp/issues/7324
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request instance
     * @param \Cake\ORM\Table $table Table instance
     * @return mixed[]
     */
    protected function getOrderClause(ServerRequestInterface $request, Table $table): array
    {
        $sortParam = Hash::get($request->getQueryParams(), 'sort', '');
        $directionParam = Hash::get($request->getQueryParams(), 'direction', 'ASC');
        $directionParam = is_string($directionParam) ? $directionParam : 'ASC';

        if (! is_string($sortParam) || '' === $sortParam) {
            return [];
        }

        $columns = [];
        foreach (explode(',', $sortParam) as $column) {
            $columns[] = $table->aliasField($column);
        }

        return array_fill_keys($columns, $directionParam);
    }

    /**
     * Method that retrieves and attaches menu elements to API response.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity instance
     * @param \Cake\ORM\Table $table Table instance
     * @param mixed[] $user User info
     * @return void
     */
    protected function attachMenu(EntityInterface $entity, Table $table, array $user): void
    {
        $data = [
            'plugin' => false,
            'controller' => $this->getControllerName($table),
            'displayField' => $table->getDisplayField(),
            'entity' => $entity,
            'user' => $user
        ];

        $entity->set(static::MENU_PROPERTY_NAME, $this->getView()->element('Module/Menu/index_actions', $data));
    }

    /**
     * Method that retrieves and attaches menu elements to API response.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity instance
     * @param \Cake\ORM\Table $table Table instance
     * @param mixed[] $user User info
     * @param mixed[] $data for extra fields like origin Id
     * @return void
     */
    protected function attachRelatedMenu(EntityInterface $entity, Table $table, array $user, array $data): void
    {
        list($plugin, $controller) = pluginSplit($this->getControllerName($table));

        $data += [
            'plugin' => false,
            'controller' => $controller,
            'displayField' => $table->getDisplayField(),
            'entity' => $entity,
            'user' => $user
        ];

        $entity->set(static::MENU_PROPERTY_NAME, $this->getView()->element('Module/Menu/related_actions', $data));
    }

    /**
     * FieldHandlerFactory instance getter.
     *
     * @return \CsvMigrations\FieldHandlers\FieldHandlerFactory
     */
    private function getFieldHandlerFactory(): FieldHandlerFactory
    {
        if (null === $this->factory) {
            $this->factory = new FieldHandlerFactory();
        }

        return $this->factory;
    }

    /**
     * View instance getter.
     *
     * @return \Cake\View\View
     */
    private function getView(): View
    {
        if (null === $this->view) {
            $this->view = new View();
        }

        return $this->view;
    }

    /**
     * FileUpload instance getter.
     *
     * @param \Cake\Datasource\RepositoryInterface $table Table instance
     * @return \CsvMigrations\Utility\FileUpload
     */
    private function getFileUpload(RepositoryInterface $table): FileUpload
    {
        if (null === $this->fileUpload) {
            Assert::isInstanceOf($table, Table::class);

            $this->fileUpload = new FileUpload($table);
        }

        return $this->fileUpload;
    }

    /**
     * Current controller name getter.
     *
     * @param \Cake\Datasource\RepositoryInterface $table Table instance
     * @return string
     */
    private function getControllerName(RepositoryInterface $table): string
    {
        if ('' === $this->controllerName) {
            $this->controllerName = App::shortName(get_class($table), 'Model/Table', 'Table');
        }

        return $this->controllerName;
    }
}
