<?php
use Cake\Core\Configure;

echo $this->Html->css('database-logs', ['block' => 'css']);

$typeStyles = Configure::read('DatabaseLog.typeStyles');
$age = Configure::read('DatabaseLog.maxLength');

?>
<section class="content-header">
    <h1><?= __d('Qobo/ProjectTemplateCakephp', 'Logs'); ?>
        <div class="pull-right">
            <div class="btn-group btn-group-sm" role="group">
            <?php echo $this->Form->postLink(
                '<i class="fa fa-trash"></i> ' . __d('Qobo/ProjectTemplateCakephp', 'Delete duplicates'),
                ['plugin' => false, 'controller' => 'Logs', 'action' => 'removeDuplicates'],
                [
                    'title' => __d('Qobo/ProjectTemplateCakephp', 'Delete duplicates'),
                    'confirm' => 'Are you sure? This action will truncate all duplicate logs from the database.',
                    'escape' => false,
                    'class' => 'btn btn-danger'
                ]
            );

            if ($age) {
                echo $this->Form->postLink(
                    '<i class="fa fa-trash-o"></i> ' . __d('Qobo/ProjectTemplateCakephp', 'Delete old'),
                    ['plugin' => false, 'controller' => 'Logs', 'action' => 'gc'],
                    [
                        'title' => __d('Qobo/ProjectTemplateCakephp', 'Delete old logs'),
                        'confirm' => 'Are you sure? This action will delete all logs older than ' . ltrim($age, '-') . '.',
                        'escape' => false,
                        'class' => 'btn btn-danger'
                    ]
                );
            }

            if ('localhost' === $this->request->getEnv('SERVER_NAME')) {
                echo $this->Form->postLink(
                    '<i class="fa fa-trash-o"></i> ' . __d('Qobo/ProjectTemplateCakephp', 'Truncate all logs'),
                    ['plugin' => false, 'controller' => 'Logs', 'action' => 'reset'],
                    [
                        'title' => __d('Qobo/ProjectTemplateCakephp', 'Truncate all logs'),
                        'confirm' => 'Are you sure? This action will truncate all logs from the database.',
                        'escape' => false,
                        'class' => 'btn btn-danger'
                    ]
                );
            }
            ?>
            </div>
        </div>
    </h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-body">
                    <div class="btn-group btn-group-sm" role="group" aria-label="...">
                        <?= $this->Html->link(
                            __d('Qobo/ProjectTemplateCakephp', 'All'),
                            ['controller' => 'Logs', 'action' => 'index'],
                            ['class' => 'btn btn-default']
                        );?>
                        <?php
                        // sort types by importance
                        $types = array_intersect(array_keys($typeStyles), $types);
                        foreach ($types as $type) {
                            $buttonStyle = !empty($typeStyles[$type]['button']) ? $typeStyles[$type]['button'] : 'btn btn-default';
                            echo $this->Html->link(
                                ucfirst($type),
                                ['controller' => 'Logs', 'action' => 'index', '?' => ['type' => $type]],
                                ['class' => $buttonStyle]
                            );
                        }
                        ?>
                    </div>
                    <?= $this->element('DatabaseLog.admin_filter'); ?>
                </div>
            </div>

            <?php $displayed_date = ''; ?>
            <ul class="timeline">
                <?php foreach ($logs as $log) : ?>
                <?php
                    $iconStyle = !empty($typeStyles[$log['type']]['icon']) ? $typeStyles[$log['type']]['icon'] : 'fa fa-wrench bg-gray';
                    $headerStyle = !empty($typeStyles[$log['type']]['header']) ? $typeStyles[$log['type']]['header'] : 'bg-gray';
                    $date = $log['created']->i18nFormat('yyyy-MM-dd');
                    if ($displayed_date != $date) {
                        $displayed_date = $date;
                        ?>
                        <!-- timeline time label -->
                        <li class="time-label">
                            <span class="bg-blue">
                                <?= $displayed_date ?>
                            </span>
                        </li>
                        <!-- /.timeline-label -->
                        <?php
                    }
                ?>

                <!-- timeline item -->
                <li>
                    <i class="<?= $iconStyle ?>"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fa fa-clock-o"></i> <?= $log['created']->i18nFormat('yyyy-MM-dd HH:mm:ss') ?></span>
                        <h2 class="timeline-header <?= $headerStyle ?>">
                            <b><?= ucfirst($log['type']); ?></b>
                            <?= $this->Html->link('#' . $log['id'], ['action' => 'view', $log['id']]) ?>
                        </h2>
                        <div class="timeline-body">
                            <?= $this->element('Log/message', ['log' => $log]); ?>
                        </div> <!-- .timeline-body -->
                    </div>
                </li>
                <?php endforeach; ?>
                <!-- END timeline item -->

            </ul>

            <!-- Timeline end -->
            <div class="box box-primary">
                <div class="box-body">
                    <div class="paginator">
                        <?= $this->Paginator->counter([
                            'format' => __d('Qobo/ProjectTemplateCakephp', 'Showing {{start}} to {{end}} of {{count}} entries')
                        ]) ?>
                        <ul class="pagination pagination-sm no-margin pull-right">
                            <?= $this->Paginator->prev('&laquo;', ['escape' => false]) ?>
                            <?= $this->Paginator->numbers() ?>
                            <?= $this->Paginator->next('&raquo;', ['escape' => false]) ?>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
