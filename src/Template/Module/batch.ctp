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

use Cake\Utility\Inflector;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;

$config = (new ModuleConfig(ConfigType::MODULE(), $this->name))->parse();
$alias = isset($config->table->alias) ? $config->table->alias : Inflector::humanize(Inflector::underscore($this->name));

$options = [
    'title' => ['page' => __('Batch Edit'), 'alias' => $alias, 'link' => $this->request->getParam('controller')],
    'entity' => $entity,
    'fields' => $fields,
    'handlerOptions' => [
        'attributes' => [
            'data-batch' => 'field',
            'disabled' => true
        ]
    ],
    'hasPanels' => property_exists($config, 'panels')
];
echo $this->element('Module/post', ['options' => $options]);

echo $this->Html->script('view-batch', ['block' => 'scriptBottom']);

$formUrl = $this->Url->build([
    'plugin' => $this->request->getParam('plugin'),
    'controller' => $this->request->getParam('controller'),
    'action' => $this->request->getParam('action')
]);

echo $this->Html->scriptBlock(
    '$("form[action=\'' . $formUrl . '\']").viewBatch({
        batch_ids: ' . json_encode($this->request->getData('batch.ids')) . ',
        redirect_url: "' . $this->request->referer() . '",
        target_id: "*[data-batch=\'field\']",
        disable_id: "*[data-batch=\'disable\']",
        enable_id: "*[data-batch=\'enable\']",
        wrapper_id: ".field-wrapper"
    })',
    ['block' => 'scriptBottom']
);
