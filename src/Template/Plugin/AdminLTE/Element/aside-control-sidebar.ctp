<?php
use App\Menu\MenuName;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;
use CsvMigrations\FieldHandlers\FieldHandlerFactory;
use Qobo\Utils\ModuleConfig\ConfigType;
use Qobo\Utils\ModuleConfig\ModuleConfig;

$factory = new FieldHandlerFactory();

$cacheKey = 'recent_activity_' . $user['id'];
$history = Cache::read($cacheKey);
if (false === $history) {
    $history = TableRegistry::get('LogAudit')
        ->find('all')
        ->select(['source', 'primary_key', 'timestamp'])
        ->limit(10)
        ->where(['user_id' => $user['id']])
        ->distinct(['primary_key'])
        ->order(['timestamp' => 'DESC'])
        ->all();

    Cache::write($cacheKey, $history);
}

$hasActivity = false;
?>
<aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        <li class="active"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-clock-o"></i></a></li>
        <li><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
        <!-- Home tab content -->
        <div class="tab-pane active" id="control-sidebar-home-tab">
            <h3 class="control-sidebar-heading">Recent Activity</h3>
            <ul class="control-sidebar-menu">
                <?php foreach ($history as $item) : ?>
                <?php
                $table = TableRegistry::get(Inflector::camelize($item['source']));
                try {
                    $entity = $table->get($item['primary_key']);
                } catch (Exception $e) {
                    // skip non-found records
                    continue;
                }

                $config = (new ModuleConfig(ConfigType::MODULE(), Inflector::camelize($item['source'])))->parse();

                $hasActivity = true;
                $icon = isset($config->table->icon) ? $config->table->icon : Configure::read('Menu.defaults.icon');
                $title = isset($config->table->alias) ? $config->table->alias : Inflector::humanize(Inflector::underscore($item['source']));
                $heading = $factory->renderValue($table, $table->getDisplayField(), $entity->get($table->getDisplayField()), ['renderAs' => 'plain']);
                ?>
                <li>
                    <a href="<?= $this->Url->build(['plugin' => null, 'controller' => $item['source'], 'action' => 'view', $item['primary_key']]) ?>">
                        <i class="menu-icon fa fa-<?= $icon ?> bg-primary" title="<?= $title ?>"></i>
                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading"><?= $heading ?></h4>
                            <p><?= $item['timestamp']->i18nFormat('yyyy-MM-dd HH:mm') ?></p>
                        </div>
                    </a>
                </li>
                <?php endforeach; ?>
                <?php if (!$hasActivity) : ?>
                <li>
                    <a href="#">
                        <i class="menu-icon fa fa-ban bg-primary"></i>
                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading"><?= __d('Qobo/ProjectTemplateCakephp', 'No recent activity') ?></h4>
                            <p><?= date('Y-m-d H:i:s') ?></p>
                        </div>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
        <!-- /.tab-pane -->

        <!-- Settings tab content -->
        <div class="tab-pane control-sidebar-settings-tab" id="control-sidebar-settings-tab">
            <section class="sidebar">
                <?= $this->element('Menu.menu', [
                    'name' => MenuName::ADMIN,
                    'user' => $user,
                    'renderer' => 'Menu\\MenuBuilder\\MainMenuRenderAdminLte'
                ]) ?>
            </section>
        </div>
        <!-- /.tab-pane -->
    </div>
</aside>
