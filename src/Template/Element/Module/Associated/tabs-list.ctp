<?php
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Qobo\Utils\Module\ModuleRegistry as ModuleRegistryAlias;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;

$config = (new ModuleConfig(ConfigType::MODULE(), $this->name))->parseToArray();
$config = ModuleRegistryAlias::getModule($this->name)->getConfig();

$labels = Hash::get($config, 'associationLabels', []);
$setLabels = [];
?>
<ul id="relatedTabs" class="nav nav-tabs responsive-tabs" role="tablist">
    <?php $active = 'active'; ?>
    <?php foreach ($associations as $association) : ?>
        <?php
        $containerId = Inflector::underscore($association->getAlias());

        list(, $tableName) = pluginSplit($association->className());
        $mc = new ModuleConfig(ConfigType::MODULE(), $tableName);
        $config = $mc->parse();

        $label = '<span class="fa fa-' . $config->table->icon . '"></span> ';

        if (array_key_exists($association->getAlias(), $labels)) {
            $label .= __($labels[$association->getAlias()]);
        } else {
            $label .= isset($config->table->alias) ?
                __($config->table->alias) :
                __(Inflector::humanize(Inflector::delimit($tableName)));
        }

        if (in_array($label, $setLabels)) {
            $mcFields = new ModuleConfig(ConfigType::FIELDS(), $tableName);
            $configFields = $mcFields->parseToArray();

            if (array_key_exists($association->getForeignKey(),$configFields) && array_key_exists('label',$configFields[$association->getForeignKey()]) ) {
                $label .= ' (' . $configFields[$association->getForeignKey()]['label'] . ')';
            }else{
                $label .= ' (' . Inflector::humanize(Inflector::delimit($association->getForeignKey())) . ')';
            }
        }

        $setLabels[] = $label;
        ?>
        <li role="presentation" class="<?= $active ?>">
            <?= $this->Html->link($label, '#' . $containerId, [
                'role' => 'tab', 'data-toggle' => 'tab', 'escape' => false, 'class' => $containerId
            ]);?>
        </li>
        <?php $active = ''; ?>
    <?php endforeach; ?>
</ul>
