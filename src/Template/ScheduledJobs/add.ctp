<?php
/**
 * @var \App\View\AppView $this
 */
use CsvMigrations\FieldHandlers\FieldHandlerFactory;

$fhf = new FieldHandlerFactory($this);

$tableName = 'ScheduledJobs';
?>
<section class="content-header">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h4><?php echo __('Add Scheduled Job'); ?></h4>
        </div>
        <div class="col-xs-12 col-md-6"></div>
    </div>
</section>
<section class="content">
<?= $this->Form->create($entity) ?>
    <div class="box box-solid">
        <div class="box-header with-border">
            <h3 class="box-title"><?= __('Details');?></h3>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                <?php
                    $definition = [
                        'name' => 'name',
                        'type' => 'string',
                        'required' => true,
                    ];
                    $inputField = $fhf->renderInput($tableName, 'name', $entity->name, ['fieldDefinitions' => $definition]);
                    echo $inputField;
                ?>
                         </div>
               <div class="col-xs-12 col-md-6">
                <?php
                    $definition = [
                        'name' => 'active',
                        'type' => 'boolean',
                        'required' => false,
                    ];
                    $inputField = $fhf->renderInput($tableName, 'active', true, [
                        'fieldDefinitions' => $definition,
                    ]);
                    echo $inputField;
                ?>
               </div>
            </div>
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <?php
                        $definition = [
                            'name' => 'job',
                            'type' => 'list',
                            'required' => false,
                        ];

                        $inputField = $fhf->renderInput($tableName, 'job', $entity->job, [
                            'fieldDefinitions' => $definition,
                            'selectOptions' => $commands,
                        ]);
                        echo $inputField;
                    ?>
                </div>

                <div class="col-xs-12 col-md-6">
                    <?php
                        $definition = [
                            'name' => 'priority',
                            'type' => 'integer',
                            'required' => false,
                        ];
                        $inputField = $fhf->renderInput($tableName, 'priority', $entity->priority, [
                            'fieldDefinitions' => $definition,
                        ]);
                        echo $inputField;
                    ?>
                </div>
            </div>

            <div class="row">
                 <div class="col-xs-12 col-md-6">
                    <?php
                        $definition = [
                            'name' => 'recurrence',
                            'type' => 'string',
                            'required' => false,
                        ];
                        $inputField = $fhf->renderInput($tableName, 'recurrence', $entity->options, [
                            'fieldDefinitions' => $definition,
                        ]);
                        echo $inputField;
                    ?>
                </div>

                <div class="col-xs-12 col-md-6">
                    <?php
                        $definition = [
                            'name' => 'options',
                            'type' => 'text',
                            'required' => false,
                        ];
                        $inputField = $fhf->renderInput($tableName, 'options', $entity->options, [
                            'fieldDefinitions' => $definition,
                        ]);
                        echo $inputField;
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <?php
                        $definition = [
                            'name' => 'start_date',
                            'type' => 'datetime',
                            'required' => false,
                        ];
                        $inputField = $fhf->renderInput($tableName, 'start_date', $entity->start_date, [
                            'fieldDefinitions' => $definition,
                        ]);
                        echo $inputField;
                    ?>
                </div>

                <div class="col-xs-12 col-md-6">
                    <?php
                        $definition = [
                            'name' => 'end_date',
                            'type' => 'datetime',
                            'required' => false,
                        ];
                        $inputField = $fhf->renderInput($tableName, 'end_date', $entity->end_date, [
                            'fieldDefinitions' => $definition,
                        ]);
                        echo $inputField;
                    ?>
                </div>

            </div>

        </div> <!-- box-body -->
    </div>
<?php
echo $this->Form->button(__('Submit'), ['name' => 'btn_operation', 'value' => 'submit', 'class' => 'btn btn-primary']);
echo '&nbsp;';
echo $this->Form->button(__('Cancel'), ['name' => 'btn_operation', 'value' => 'cancel', 'class' => 'btn btn-primary']);
echo $this->Form->end();
echo $this->element('CsvMigrations.common_js_libs');
?>
</section>
