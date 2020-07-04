<?php
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Qobo\Utils\Module\ModuleRegistry;
use RolesCapabilities\Access\AccessFactory;

if (! $options['entity']->get($field['name'])) {
    return;
}

$isTranslatable = function ($tableName, $fieldName) {
    $config = ModuleRegistry::getModule(Inflector::camelize($tableName))->getConfig();
    if (! Hash::get($config, 'table.translatable', false)) {
        return false;
    }

    $config = ModuleRegistry::getModule(Inflector::camelize($tableName))->getFields();

    return Hash::get($config, $fieldName . '.translatable', false);
};

if (! $isTranslatable($tableName, $field['name'])) {
    return;
}

$accessFactory = new AccessFactory();
$url = ['plugin' => 'Translations', 'controller' => 'Translations', 'action' => 'addOrUpdate'];
if (! $accessFactory->hasAccess($url, $user)) {
    return;
}

echo $this->Html->link(
    '<i class="fa fa-globe"></i>',
    '#translations_translate_id_modal',
    [
        'data-toggle' => 'modal',
        'data-record' => $options['entity']->get(TableRegistry::getTableLocator()->get($tableName)->getPrimaryKey()),
        'data-model' => $tableName,
        'data-field' => $field['name'],
        'data-value' => $options['entity']->get($field['name']),
        'escape' => false
    ]
);
?>&nbsp;
<?= $this->element('Translations.modal_add') ?>
