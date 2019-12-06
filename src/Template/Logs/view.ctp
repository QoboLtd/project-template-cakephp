<?php
/**
 * CakePHP DatabaseLog Plugin
 *
 * Licensed under The MIT License.
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @link https://github.com/dereuromark/CakePHP-DatabaseLog
 */
?>
<?php
use Cake\Core\Configure;

echo $this->Html->css('database-logs', ['block' => 'css']);

$typeStyles = Configure::read('DatabaseLog.typeStyles');

$title = $this->Html->link(__d('Qobo/ProjectTemplateCakephp', 'Logs'), ['plugin' => false, 'controller' => 'Logs', 'action' => 'index']);
$title .= ' &raquo; ' . h($log['id']);

$headerStyle = !empty($typeStyles[$log['type']]['header']) ? $typeStyles[$log['type']]['header'] : 'bg-gray';
?>
<section class="content-header">
    <h1><?= $title ?></h1>
</section>
<section class="content">
    <div class="box box-solid">
        <div class="box-header <?= $headerStyle ?>">
            <span class="time pull-right"><i class="fa fa-clock-o"></i> <?= $log['created']->i18nFormat('yyyy-MM-dd HH:mm:ss') ?></span>
            <h2 class="box-title"><b><?= h(ucfirst($log['type'])) ?></b></h3>
        </div>
        <?= $this->element('Log/message', ['log' => $log]); ?>
    </div>
</section>
