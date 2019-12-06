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

use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use CsvMigrations\FieldHandlers\FieldHandlerFactory;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;

$config = (new ModuleConfig(ConfigType::MODULE(), $this->name))->parse();

$alias = isset($config->table->alias) ? $config->table->alias : Inflector::humanize(Inflector::underscore($this->name));

$table = TableRegistry::get(empty($this->plugin) ? $this->name : $this->plugin . '.' . $tableName);
$displayField = (new FieldHandlerFactory($this))->renderValue(
    $table,
    $table->getDisplayField(),
    $entity->get($table->getDisplayField()),
    ['entity' => $entity]
);

$options = [
    'entity' => $entity,
    'fields' => $fields,
    'title' => ['page' => __d('Qobo/ProjectTemplateCakephp', 'Edit {0} ', $displayField), 'alias' => $alias, 'link' => $this->request->getParam('controller')],
    'handlerOptions' => ['entity' => $entity],
    'hasPanels' => property_exists($config, 'panels')
];
echo $this->element('Module/post', ['options' => $options]);
