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
use Qobo\Utils\Module\ModuleRegistry;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;

$factory = new FieldHandlerFactory($this);

$defaultOptions = [
    'title' => null,
    'entity' => null,
    'fields' => [],
];
if (empty($options)) {
    $options = [];
}
$options = array_merge($defaultOptions, $options);

// get table name
$tableName = $this->name;
if (!empty($this->plugin)) {
    $tableName = $this->plugin . '.' . $tableName;
}

// get table instance
$table = TableRegistry::getTableLocator()->get($tableName);

// generate title
if (!$options['title']) {
    $config = ModuleRegistry::getModule($this->name)->getConfig()
    $options['title'] = $this->Html->link(
        __(isset($config->table->alias) ? $config->table->alias : __(Inflector::humanize(Inflector::underscore($this->name)))),
        ['plugin' => $this->plugin, 'controller' => $this->name, 'action' => 'index']
    );
    $options['title'] .= ' &raquo; ';
    $options['title'] .= $factory->renderValue(
        $table,
        $table->getDisplayField(),
        $options['entity']->get($table->getDisplayField()),
        ['entity' => $options['entity']]
    );
}
?>
<section class="content-header">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h4><?= $options['title'] ?></h4>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="pull-right">
            <div class="btn-group btn-group-sm" role="group">
                <?= $this->element('Module/Menu/view_top', [
                    'options' => $options, 'displayField' => $table->getDisplayField()
                ]); ?>
            </div>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <?= $this->element('Module/View/fields', ['options' => $options, 'table' => $table]) ?>
    <?= $this->element('CsvMigrations.common_js_libs'); // loading common setup for typeahead/panel/etc libs ?>
    <?= $this->Html->script('Qobo/Utils.dataTables.init', ['block' => 'scriptBottom']) ?>
    <hr />
    <div class="row associated-records">
        <div class="col-xs-12">
            <?= $this->element('Module/associated', [
                'options' => $options, 'table' => $table, 'factory' => $factory, 'entity' => $options['entity']
            ], ['plugin' => false]) ?>
        </div>
    </div>
</section>
