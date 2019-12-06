<?php
use Cake\Core\Configure;

echo $this->Html->css('Qobo/Utils./plugins/datatables/css/dataTables.bootstrap.min', ['block' => 'css']);

echo $this->Html->script(
    [
        'Qobo/Utils./plugins/datatables/datatables.min',
        'Qobo/Utils./plugins/datatables/js/dataTables.bootstrap.min',
        'Qobo/Utils.dataTables.init'
    ],
    ['block' => 'scriptBottom']
);

$dtOptions = [
    'table_id' => '.table-datatable',
    'state' => ['duration' => (int)(Configure::read('Session.timeout') * 60)],
    'order' => [0, 'asc'],
    'ajax' => [
        'token' => Configure::read('API.token'),
        'url' => $this->Url->build([
            'prefix' => 'api',
            'plugin' => $this->plugin,
            'controller' => $this->name,
            'action' => $this->request->getParam('action')
        ]) . '.json',
        'columns' => ['name', 'active', 'job', 'recurrence', 'start_date', 'end_date', 'last_run_date', '_Menus'],
        'extras' => ['format' => 'pretty', 'menus' => 1]
    ],
];

echo $this->Html->scriptBlock(
    'new DataTablesInit(' . json_encode($dtOptions) . ');',
    ['block' => 'scriptBottom']
);
?>
<section class="content-header">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h4><?= __d('Qobo/ProjectTemplateCakephp', 'Scheduled Jobs'); ?></h4>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="pull-right">
                <div class="btn-group btn-group-sm" role="group">
                    <?= $this->element('Module/Menu/index_top', ['user' => $user]); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <table class="table table-hover table-condensed table-vertical-align table-datatable" width="100%">
                <thead>
                    <tr>
                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'Name'); ?></th>
                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'Active');?></th>
                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'Job');?></th>
                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'Recurrence'); ?></th>
                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'Start');?></th>
                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'End');?></th>
                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'Last Run Date');?></th>
                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'Actions');?></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>
