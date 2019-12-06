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

$factory = new FieldHandlerFactory($this);


$options = [
    'entity' => $entity,
    'fields' => $fields,
    'title' => null,
];


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
$table = TableRegistry::get($tableName);

// generate title
if (!$options['title']) {
    $config = (new ModuleConfig(ConfigType::MODULE(), $this->name))->parse();
    $options['title'] = $this->Html->link(
        __d('Qobo/ProjectTemplateCakephp', 'Scheduled Job'),
        ['controller' => 'ScheduledJobs', 'action' => 'view', $entity->scheduled_job_id]
    );

    $options['title'] .= ' &raquo; ';
    $options['title'] .= $factory->renderValue(
        $table,
        $table->getDisplayField(),
        $options['entity']->get($table->getDisplayField()),
        ['entity' => $options['entity']]
    );
}

if (!$this->request->query('embedded')) : ?>
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
<?php endif;

$embeddedFields = [];
$embeddedDirty = false;

foreach ($options['fields'] as $panelName => $panelFields) : ?>
    <?php
    if ($this->request->query('embedded')) {
        $panelName = Inflector::singularize(Inflector::humanize($this->name)) . ': ' . $panelName;
    }
    ?>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title"><?= $panelName; ?></h3>
            <div class="box-tools pull-right">
                <button type="button" class="btn btn-box-tool" data-widget="collapse">
                    <i class="fa fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="box-body">
        <?php foreach ($panelFields as $subFields) : ?>
            <div class="row">
            <?php foreach ($subFields as $field) : ?>
                <?php if (trim($field['name'])) : ?>
                    <?php
                    if (!$embeddedDirty) {
                        // embedded field
                        if ('EMBEDDED' === $field['name']) {
                            $embeddedDirty = true;
                        }

                        if (!$embeddedDirty) { // non-embedded field
                            echo $this->element('Module/Field/value', [
                                'factory' => $factory, 'field' => $field, 'options' => $options
                            ]);
                        }
                    } else {
                        $embeddedFields[] = $field['name'];
                        $embeddedDirty = false;
                    }
                    ?>
                <?php else : ?>
                        <div class="col-xs-4 col-md-2 text-right">&nbsp;</div>
                        <div class="col-xs-8 col-md-4">&nbsp;</div>
                <?php endif; ?>
                <div class="clearfix visible-xs visible-sm"></div>
            <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        </div>
    </div>
    <?php
    if (empty($embeddedFields)) {
        continue;
    }

    echo $this->element('Module/Embedded/fields', [
        'fields' => $embeddedFields, 'table' => $table, 'options' => $options
    ]);

    $embeddedFields = [];
    ?>
<?php endforeach; ?>
<?php if (!$this->request->query('embedded')) : ?>
    <?= $this->element('CsvMigrations.common_js_libs'); // loading common setup for typeahead/panel/etc libs ?>
    <?= $this->Html->script('Qobo/Utils.dataTables.init', ['block' => 'scriptBottom']) ?>
    <hr />
    <div class="row associated-records">
        <div class="col-xs-12">
            <?= $this->element('Module/associated', [
                'options' => $options, 'table' => $table, 'factory' => $factory, 'entity' => $options['entity']
            ]) ?>
        </div>
    </div>
</section>
<?php endif; ?>
