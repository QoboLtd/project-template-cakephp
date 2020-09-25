<?php

/**
 * Copyright (c) Qobo Ltd. (https://www.qobo.biz)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Qobo Ltd. (https://www.qobo.biz)
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Service;

use App\Utility\Field;
use App\Utility\Model;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\ORM\Table;
use Search\Aggregate\AbstractAggregate;
use Webmozart\Assert\Assert;

final class Export
{
    private const QUERY_LIMIT = 10;
    private const WRITE_MODES = ['w', 'a'];

    /**
     * @var \Cake\ORM\Table
     */
    private $table;

    /**
     * @var string[]
     */
    private $ids = [];

    /**
     * @var string[]
     */
    private $fields;

    /**
     * @var bool
     */
    private $formatted = false;

    /**
     * @var \Cake\Filesystem\File|null
     */
    private $file = null;

    /**
     * @var \DateTimeImmutable
     */
    private $createdAt;

    /**
     * Constructor.
     *
     * @param \Cake\ORM\Table $table Search name
     * @param string[] $fields IDs to be exported
     * @param bool $formatted Format flag
     * @return void
     */
    private function __construct(Table $table, array $fields, bool $formatted = false)
    {
        Assert::isList($fields);
        Assert::notEmpty($fields);

        $this->table = $table;
        $this->fields = array_map(function ($item) {
            return $this->table->aliasField($item);
        }, $fields);
        $this->formatted = $formatted;
        $this->createdAt = new \DateTimeImmutable();
    }

    /**
     * Exports data into a CSV by provided IDs and returns its filename.
     *
     * @param string[] $ids IDs to be exported
     * @param \Cake\ORM\Table $table Search name
     * @param string[] $fields Fields to export values from
     * @param bool $formatted Format flag
     * @return self
     */
    public static function fromIds(array $ids, Table $table, array $fields, bool $formatted = false): self
    {
        Assert::isList($ids);
        Assert::notEmpty($ids);

        $instance = new self($table, $fields, $formatted);
        $instance->ids = $ids;

        $instance->execute($formatted);

        return $instance;
    }

    /**
     * Extract and return model name from table object.
     *
     * @return string
     */
    private function modelName(): string
    {
        return App::shortName(get_class($this->table), 'Model/Table', 'Table');
    }

    /**
     * Executes export functionality.
     *
     * @param bool $formatted Format flag
     * @return void
     */
    private function execute(bool $formatted): void
    {
        $primaryKey = $this->table->getPrimaryKey();
        Assert::string($primaryKey);

        $options = [
            'fields' => $this->fields,
            'data' => [
                ['field' => $this->table->aliasField($primaryKey), 'operator' => 'is', 'value' => $this->ids],
            ],
        ];

        $query = $this->table->find('search', $options);
        $query->formatResults($formatted ? new \App\ORM\LabeledFormatter() : new \App\ORM\RawFormatter());
        $query->formatResults(new \App\ORM\FlatFormatter());
        $pages = ceil($query->count() / self::QUERY_LIMIT) + 1;

        $this->write([$this->headers()], 'w');
        for ($page = 1; $page < $pages; $page++) {
            $data = $query->page($page, self::QUERY_LIMIT)->toArray();
            $data = array_map(function ($item) {
                return array_replace(array_fill_keys($this->fields, null), $item->toArray());
            }, $data);

            $this->write($data, 'a');
        }
    }

    /**
     * Create and return CSV file for exporting data into.
     *
     * @return \Cake\Filesystem\File
     */
    private function file(): File
    {
        if (null !== $this->file) {
            return $this->file;
        }

        $file = new File($this->path(), true);

        if (! $file->writable()) {
            throw new \RuntimeException(sprintf('Export file is not writable: %s.', $file->pwd()));
        }

        $this->file = $file;

        return $this->file;
    }

    /**
     * URL getter.
     *
     * @return string
     */
    public function url(): string
    {
        $url = Configure::readOrFail('Export.url');
        Assert::stringNotEmpty($url);

        return '/' . trim($url, '/') . '/' . $this->filename();
    }

    /**
     * Full path getter.
     *
     * @return string
     */
    public function path(): string
    {
        return self::basePath() . $this->filename();
    }

    /**
     * Base path getter.
     *
     * @return string
     */
    public static function basePath(): string
    {
        $path = Configure::readOrFail('Export.path');
        Assert::stringNotEmpty($path);

        return rtrim($path, DS) . DS;
    }

    /**
     * Filename generator.
     *
     * @return string
     */
    private function filename(): string
    {
        $filename = sprintf(
            '%s - %s',
            $this->modelName(),
            $this->createdAt->format('YmdHis')
        );

        return $filename . '.csv';
    }

    /**
     * Get export headers.
     *
     * @return string[]
     */
    private function headers(): array
    {
        if ([] === $this->fields) {
            return [];
        }

        $associations = Model::associations($this->modelName());

        $result = [];
        foreach ($this->fields as $field) {
            $extraInfo = [];
            if (AbstractAggregate::isAggregate($field)) {
                $extraInfo[] = AbstractAggregate::extractAggregate($field);
                $field = AbstractAggregate::extractFieldName($field);
            }

            list($modelName, $fieldName) = pluginSplit($field);
            $key = array_search($modelName, array_column($associations, 'name'));
            if (false !== $key) {
                $extraInfo[] = $associations[$key]['label'];
                $modelName = $associations[$key]['model'];
            }

            $label = (new Field($modelName, $fieldName))->label();

            $result[] = [] !== $extraInfo ? $label . ' - ' . implode(' :: ', $extraInfo) : $label;
        }

        return $result;
    }

    /**
     * Writes to export file.
     *
     * @param mixed[] $data Data to write into the file
     * @param string $mode Overwrite or append mode
     * @return void
     */
    private function write(array $data, string $mode = 'a'): void
    {
        if (! in_array($mode, self::WRITE_MODES, true)) {
            throw new \InvalidArgumentException(sprintf('Unsupported write mode: %s', $mode));
        }

        $handler = fopen($this->file()->pwd(), $mode);

        if (! is_resource($handler)) {
            throw new \RuntimeException(sprintf('Export interrupted: failed to bind resource to a stream.'));
        }

        foreach ($data as $row) {
            if (false === fputcsv($handler, $row)) {
                throw new \RuntimeException(sprintf('Export interrupted: failed to write data into the file.'));
            }
        }

        fclose($handler);
    }
}
