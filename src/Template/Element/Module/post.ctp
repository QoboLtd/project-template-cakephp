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
use CsvMigrations\CsvMigrationsUtils;

$defaultOptions = [
    'handlerOptions' => [],
    'title' => null,
    'entity' => null,
    'fields' => [],
];
if (empty($options)) {
    $options = [];
}
$options = array_merge($defaultOptions, $options);

$formOptions = [
    'url' => [
        'plugin' => $this->request->getParam('plugin'),
        'controller' => $this->request->getParam('controller'),
        'action' => $this->request->getParam('action')
    ],
    'name' => Inflector::dasherize($this->name),
    'type' => 'file',
    'templates' => [
        'inputContainerError' => '<div class="form-group input {{type}}{{required}} has-error">{{content}}{{error}}</div>',
        'error' => '<div class="error-message help-block">{{content}}</div>',
    ]
];

if ($options['hasPanels']) {
    $formOptions['data-panels-url'] = $this->Url->build([
            'prefix' => 'api',
            'plugin' => $this->request->getParam('plugin'),
            'controller' => $this->request->getParam('controller'),
            'action' => 'panels'
        ]);
}

$linkTitle = is_array($options['title']) ? $this->Html->link(
            __($options['title']['alias']),
            [
                'plugin' => $this->plugin,
                'controller' => $options['title']['link'],
                'action' => 'index'
            ]
        ) . ' &raquo; ' . $options['title']['page'] : (string)$options['title'];

?>
<section class="content-header">
    <h4><?= $linkTitle ?></h4>
</section>
<section class="content">
    <?php
    /**
     * Conversion logic
     * @todo probably this has to be moved to another plugin
     */

    if (!$this->request->getParam('pass.conversion')) {
        echo $this->Form->create($options['entity'], $formOptions);
    }

    if (!empty($options['fields'])) {
        echo $this->element('Module/Form/fields', ['options' => $options]);

        // Custom redirect parent module.
        // @see `cakephp-utils` config JSON schema `parentItem` definition
        if (!empty($this->request->getQuery('_parent'))) {
            $parent = $this->request->getQuery('_parent');
            echo $this->Form->hidden('_parent', ['value' => $parent]);
        }
    }

    /**
     * Conversion logic
     * @todo probably this has to be moved to another plugin
     */
    if (!$this->request->getParam('pass.conversion')) {
        echo $this->Form->button(__('Submit'), [
            'name' => 'btn_operation', 'value' => 'submit', 'class' => 'btn btn-primary'
        ]);

        echo $this->Html->link(__('Cancel'), ['action' => 'index'], ['class' => 'btn btn-link', 'role' => 'button']);
        echo $this->Form->end();

        // Fetch embedded module(s)
        echo $this->element('Module/Form/fields_embedded', ['fields' => $options['fields']]);
    }
    ?>
</section>
<?php
/**
 * @todo  Load when needed.
 * - When there is file input
 * - load these files only if foreign/related field exists
 */
echo $this->element('CsvMigrations.common_js_libs');
