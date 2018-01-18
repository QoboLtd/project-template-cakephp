<?php
use Cake\Core\Configure;

echo $this->Html->css('Qobo/Utils./plugins/datatables/css/dataTables.bootstrap.min', ['block' => 'css']);

echo $this->Html->script(
    [
        'Qobo/Utils./plugins/datatables/datatables.min',
        'Qobo/Utils./plugins/datatables/js/dataTables.bootstrap.min',
        'Qobo/Utils.dataTables.init',
        'CsvMigrations.view-index'
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
            'action' => $this->request->param('action')
        ]) . '.json',
        'columns' => ['name', 'active', 'job', 'recurrence', 'start_date', 'end_date', '_Menus'],
        'extras' => ['format' => 'pretty', 'menus' => 1]
    ],
];

echo $this->Html->scriptBlock(
    '// initialize index view functionality
    view_index.init({
        token: "' . Configure::read('API.token') . '",
        // initialize dataTable
        datatable: new DataTablesInit(' . json_encode($dtOptions) . ')
    });',
    ['block' => 'scriptBottom']
);
?>
<section class="content-header">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h4><?php echo __('Integrations'); ?></h4>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="pull-right">
                <div class="btn-group btn-group-sm" role="group">
                    <?php echo $this->element('CsvMigrations.Menu/index_top', ['user' => $user]);?>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="box box-solid">
        <div class="box-body">
            <table class="table table-hover table-condensed table-vertical-align table-datatable" width="100%">
                <thead>
                    <tr>
                        <th><?= __('Name'); ?></th>
                        <th><?= __('Active');?></th>
                        <th><?= __('Job');?></th>
                        <th><?= __('Recurrence'); ?></th>
                        <th><?= __('Start');?></th>
                        <th><?= __('End');?></th>
                        <th><?= __('Actions');?></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</section>
