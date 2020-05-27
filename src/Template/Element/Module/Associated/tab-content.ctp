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

use Cake\Core\Configure;
use Cake\Utility\Inflector;
use CsvMigrations\FieldHandlers\CsvField;
use Qobo\Utils\Module\ModuleRegistry;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;

list(, $module) = pluginSplit($association->className());
$config = ModuleRegistry::getModule($module)->getView('index');
$fields = array_column($config, 0);

$options = [
    'associationName' => $association->getName(),
    'originTable' => $table->getTable(),
    'id' => $this->request->getParam('pass.0'),
    'format' => 'pretty',
    'menus' => true
];

$tableId = 'table-' . Inflector::underscore($association->getAlias());

$dtOptions = [
    'table_id' => '#' . $tableId,
    'state' => ['duration' => (int)(Configure::read('Session.timeout') * 60)],
    'ajax' => [
        'token' => Configure::read('CsvMigrations.api.token'),
        'url' => $url,
        'extras' => $options,
        'columns' => call_user_func(function () use ($fields) {
            $fields[] = '_Menus';

            return $fields;
        }),
        'virtualColumns' => call_user_func(function () use ($module) {
            $config = ModuleRegistry::getModule($module)->getConfig();

            return $config['virtualFields'] ? (array)$config['virtualFields'] : [];
        }),
        'combinedColumns' => call_user_func(function () use ($fields, $factory, $module) {
            $config = ModuleRegistry::getModule($module)->getMigration();

            $result = [];
            foreach ($fields as $field) {
                if (!isset($config[$field])) {
                    continue;
                }

                $csvField = new CsvField((array)$config[$field]);
                // convert CSV field to DB field(s)
                $dbFields = $factory->fieldToDb($csvField, $module, $field);
                // non-combined field
                if (isset($dbFields[$field])) {
                    continue;
                }

                foreach ($factory->fieldToDb($csvField, $module, $field) as $dbField) {
                    $result[$field][] = $dbField->getName();
                }
            }

            return $result;
        }),
    ],
];

echo $this->Html->scriptBlock("
$('#relatedTabs a.$containerId').on('click', function() {
    if ( ! $.fn.DataTable.isDataTable('#$tableId') ) {
        new DataTablesInit(" . json_encode($dtOptions) . ");
    } else {
        $('#$tableId').DataTable().ajax.reload();
    }
});
", [
    'block' => 'scriptBottom'
]);
?>
<div class="table-responsive">
    <table id="<?= $tableId ?>" class="table table-hover table-condensed table-vertical-align table-datatable" width="100%">
        <thead>
            <tr>
            <?php foreach ($fields as $field) : ?>
                <th><?= $factory->renderName($association->className(), $field) ?></th>
            <?php endforeach; ?>
                <th><?= __('Actions');?></th>
            </tr>
        </thead>
    </table>
</div>
