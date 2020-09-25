<?php

namespace App\Utility;

use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Qobo\Utils\Module\Exception\MissingModuleException;
use Qobo\Utils\Module\ModuleRegistry;
use Webmozart\Assert\Assert;

final class Field
{
    private const TYPE_PATTERN = '/(.*?)\((.*?)\)/';
    private const RELATED_TYPE = 'related';

    /**
     * @var string
     */
    private $model;

    /**
     * @var string
     */
    private $moduleName;

    /**
     * @var string
     */
    private $field;

    /**
     * @var \Cake\ORM\Table
     */
    private $table;

    /**
     * @param string $model Model name
     * @param string $field Field name
     */
    public function __construct(string $model, string $field)
    {
        Assert::stringNotEmpty($model);
        Assert::stringNotEmpty($field);

        $this->model = $model;
        $this->table = TableRegistry::getTableLocator()->get($model);
        Assert::true($this->table->getSchema()->hasColumn($field));

        list(, $this->moduleName) = pluginSplit($this->model);
        $this->field = $field;
    }

    /**
     * Field state getter.
     *
     * @return mixed[]
     */
    public function state(): array
    {
        $result = [
            'name' => $this->name(),
            'label' => $this->label(),
            'type' => $this->type(),
            'db_type' => $this->databaseType(),
            'meta' => $this->meta(),
        ];

        $list = new FieldList($this->model, $this->field);
        if ($list->has()) {
            $result['options'] = $list->options();
        }

        if (self::RELATED_TYPE === $result['type']) {
            $relatedModel = $this->getRelatedModelFromFile();
            $result['source'] = Inflector::dasherize($relatedModel);
            $result['display_field'] = TableRegistry::getTableLocator()->get($relatedModel)->getDisplayField();
        }

        return $result;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function label(): string
    {
        $config = [];
        try {
            $config = ModuleRegistry::getModule($this->moduleName)->getFields();
        } catch (MissingModuleException $e) {
            // @ignoreException
        }

        $default = substr($this->field, -3) === '_id' ? substr($this->field, 0, -3) : $this->field;
        $default = Inflector::humanize(Inflector::underscore($default));

        return Hash::get($config, $this->field . '.label', $default);
    }

    /**
     * @return string
     */
    public function type(): string
    {
        $type = $this->getTypeFromFile();

        return '' !== $type ? $type : (string)$this->table->getSchema()->getColumnType($this->field);
    }

    /**
     * @return string
     */
    public function databaseType(): string
    {
        $type = $this->table->getSchema()->getColumnType($this->field);
        Assert::string($type);

        return $type;
    }

    /**
     * @return string[]
     */
    public function meta(): array
    {
        $config = $result = [];
        try {
            $config = ModuleRegistry::getModule($this->moduleName)->getMigration();
        } catch (MissingModuleException $e) {
            // @ignoreException
        }

        foreach ($this->table->getSchema()->constraints() as $item) {
            $constraint = $this->table->getSchema()->getConstraint($item);
            if (null === $constraint) {
                continue;
            }

            if ('primary' !== Hash::get($constraint, 'type')) {
                continue;
            }

            if (! in_array($this->field, (array)Hash::get($constraint, 'columns'), true)) {
                continue;
            }

            $result = array_merge($result, ['required', 'unique']);
        }

        if (Hash::get($config, $this->field . '.required')) {
            $result[] = 'required';
        }

        foreach ($this->table->getSchema()->constraints() as $item) {
            $constraint = $this->table->getSchema()->getConstraint($item);
            if (null === $constraint) {
                continue;
            }

            if ('unique' !== Hash::get($constraint, 'type')) {
                continue;
            }

            if (! in_array($this->field, (array)Hash::get($constraint, 'columns'), true)) {
                continue;
            }

            $result[] = 'unique';
        }

        if (Hash::get($config, $this->field . '.unique')) {
            $result[] = 'unique';
        }

        if (Hash::get($config, $this->field . '.non-searchable')) {
            $result[] = 'non-searchable';
        }

        return array_unique($result);
    }

    /**
     * @return string
     */
    private function getTypeFromFile(): string
    {
        $config = [];
        try {
            $config = ModuleRegistry::getModule($this->moduleName)->getMigration();
        } catch (MissingModuleException $e) {
            // @ignoreException
        }
        if ([] === $config) {
            return '';
        }

        $type = Hash::get($config, $this->field . '.type');
        if (null !== $type) {
            return 1 === preg_match(self::TYPE_PATTERN, $type, $matches) ? $matches[1] : $type;
        }

        $combinedFields = ['_amount' => 'decimal', '_currency' => 'list', '_unit' => 'list'];
        /**
         * Handles the special cases of combined fields, this will go away
         * once we properly separate database column and UI field definitions.
         */
        foreach ($combinedFields as $fieldSuffix => $fieldType) {
            $strlen = strlen($fieldSuffix);
            if ($fieldSuffix === substr($this->field, -$strlen, $strlen)) {
                return $fieldType;
            }
        }

        return '';
    }

    /**
     * @return string
     */
    private function getRelatedModelFromFile(): string
    {
        $config = ModuleRegistry::getModule($this->moduleName)->getMigration();

        $type = Hash::get($config, $this->field . '.type');
        Assert::stringNotEmpty($type);

        preg_match(self::TYPE_PATTERN, $type, $matches);
        Assert::keyExists($matches, 2);
        Assert::stringNotEmpty($matches[2]);

        return $matches[2];
    }
}
