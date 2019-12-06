<?php
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use CsvMigrations\Utility\Field;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;

list($plugin, $controller) = pluginSplit($model);
$table = TableRegistry::getTableLocator()->get($controller);

$formOptions = [
    'url' => ['prefix' => 'api', 'plugin' => $plugin, 'controller' => $controller, 'action' => 'add'],
    'name' => Inflector::dasherize($controller),
    'type' => 'file',
    'templates' => [
        'inputContainerError' => '<div class="form-group input {{type}}{{required}} has-error">{{content}}{{error}}</div>',
        'error' => '<div class="error-message help-block">{{content}}</div>',
    ],
    'data-embedded-display-field' => TableRegistry::getTableLocator()->get($model)->getDisplayField(),
    'data-embedded-field-id' => $field,
    'data-embedded' => true
];

if (isset($associationName) && $associationName) {
    $formOptions['data-embedded-association-name'] = $associationName;
}

if (isset($relatedModel) && $relatedModel && isset($relatedId) && $relatedId) {
    $formOptions['data-embedded-related-model'] = $relatedModel;
    $formOptions['data-embedded-related-id'] = $relatedId;
}

$config = (new ModuleConfig(ConfigType::MODULE(), $controller))->parseToArray();

if (array_key_exists('panels', $config)) {
    $formOptions['data-panels-url'] = $this->Url->build([
            'prefix' => 'api',
            'plugin' => $plugin,
            'controller' => $controller,
            'action' => 'panels'
        ]);
}
?>
<section class="content-header">
    <h4><?= Hash::get($config, 'table.alias', Inflector::humanize(Inflector::underscore($controller))) ?> &raquo; <?= __d('Qobo/ProjectTemplateCakephp', 'Create') ?></h4>
</section>
<section class="content">
<?php
echo $this->Form->create(null, $formOptions);

$fields = Field::getCsvView($table, 'add', true, true);

echo $this->element('Module/Form/fields', [
    'options' => [
        'entity' => $table->newEntity(),
        'fields' => $fields,
        'handlerOptions' => ['entity' => null]
    ]
]);

echo $this->Form->button(
    __d('Qobo/ProjectTemplateCakephp', 'Submit'),
    ['name' => 'btn_operation', 'value' => 'submit', 'class' => 'btn btn-primary']
);

echo $this->Html->link(
    __d('Qobo/ProjectTemplateCakephp', 'Cancel'),
    ['action' => 'index'],
    ['class' => 'btn btn-link', 'role' => 'button', 'aria-label' => 'Close', 'data-dismiss' => 'modal']
);
echo $this->Form->end();

echo $this->element('Module/Form/fields_embedded', ['fields' => $fields]);

echo $this->Html->script('CsvMigrations.embedded', ['block' => 'scriptBottom']);
?>
</section>
