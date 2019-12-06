<?php
use Cake\Core\Configure;
?>
<section class="content-header">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h4><?= __d('Qobo/ProjectTemplateCakephp', 'System Searches'); ?></h4>
        </div>
    </div>
</section>
<section class="content">
    <div class="row">
        <div class="col-xs-12 col-md-10 col-lg-6">
            <div class="box box-primary">
                <div class="box-body">
                    <table class="table table-hover table-condensed table-vertical-align" data-datatable="1">
                        <thead>
                            <tr>
                                <th><?= __d('Qobo/ProjectTemplateCakephp', 'Name') ?></th>
                                <th><?= __d('Qobo/ProjectTemplateCakephp', 'Model') ?></th>
                                <th class="actions"><?= __d('Qobo/ProjectTemplateCakephp', 'Actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($entities as $entity) : ?>
                            <tr>
                                <td>
                                    <?= h($entity->get('name')) ?>
                                </td>
                                <td>
                                    <?= h($entity->get('model')) ?>
                                </td>
                                <td class="actions">
                                    <div class="btn-group btn-group-xs" role="group">
                                    <?= $this->Html->link(
                                        '<i class="fa fa-eye"></i>',
                                        [
                                            'plugin' => false,
                                            'controller' => $entity->get('model'),
                                            'action' => 'search',
                                            $entity->get('id')],
                                        [
                                            'title' => __d('Qobo/ProjectTemplateCakephp', 'View'),
                                            'class' => 'btn btn-default btn-sm',
                                            'escape' => false
                                        ]
                                    ); ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
echo $this->Html->css('Qobo/Utils./plugins/datatables/css/dataTables.bootstrap.min', ['block' => 'css']);
echo $this->Html->script(
    [
        'Qobo/Utils./plugins/datatables/datatables.min',
        'Qobo/Utils./plugins/datatables/js/dataTables.bootstrap.min',
    ],
    ['block' => 'scriptBottom']
);
echo $this->Html->scriptBlock(
    '$("table[data-datatable=\"1\"]").DataTable({
        stateSave: true,
        stateDuration: ' . (int)(Configure::read('Session.timeout') * 60) . '
    });',
    ['block' => 'scriptBottom']
);
