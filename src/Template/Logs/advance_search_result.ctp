<?php
	use Cake\Core\Configure;

	$typeStyles = Configure::read('DatabaseLog.typeStyles');
	$age = Configure::read('DatabaseLog.maxLength');
?>

<section class="content">
    <div class="row">
        <div class="col-md-12">

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

            <?php $displayed_date = ''; ?>
			<ul class="timeline">
			    <?php foreach ($data['data'] as $log) : ?>
			    <?php
			    	$typeFieldExist = array_key_exists($module.'.type', $log);
			    	$createdFieldExist = array_key_exists($module.'.created', $log);
			        $iconStyle = $typeFieldExist && !empty($typeStyles[$log[$module.'.type']]['icon']) ? $typeStyles[$log[$module.'.type']]['icon'] : 'fa fa-wrench bg-gray';
			        $headerStyle = $typeFieldExist && !empty($typeStyles[$log[$module.'.type']]['header']) ? $typeStyles[$log[$module.'.type']]['header'] : 'bg-gray';

			        if( $createdFieldExist ) {
				        $date = $log[$module.'.created'];
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
				    }
			    ?>

			    <!-- timeline item -->
			    <li>
			        <i class="<?= $iconStyle ?>"></i>
			        <div class="timeline-item">
			        	<?php if ($createdFieldExist): ?>
			            	<span class="time"><i class="fa fa-clock-o"></i> <?= $log[$module.'.created'] ?></span>
			            <?php endif ?>
			            <h2 class="timeline-header <?= $headerStyle ?>">
			            	<?php if ($typeFieldExist): ?>
			                	<b><?= ucfirst($log[$module.'.type']); ?></b>
			                <?php endif ?>
			                <?= $this->Html->link('#' . $log[$module.'.id'], ['action' => 'view', $log[$module.'.id']]) ?>
			            </h2>
			            <div class="timeline-body">
			                <div class="box-body">
			                	<?php if (array_key_exists($module.'.hostname',$log) || array_key_exists($module.'.ip',$log)): ?>
								    <div class="row">
								    	<?php if (array_key_exists($module.'.hostname',$log)): ?>
									        <div class="col-xs-4 col-md-2 text-right"><strong><?= __d('Qobo/ProjectTemplateCakephp', 'Hostname'); ?></strong></div>
									        <div class="col-xs-8 col-md-4"><?= h($log[$module.'.hostname']); ?></div>
									    <?php endif ?>
									    <?php if (array_key_exists($module.'.ip',$log)): ?>
									        <div class="col-xs-4 col-md-2 text-right"><strong><?= __d('Qobo/ProjectTemplateCakephp', 'IP'); ?></strong></div>
									        <div class="col-xs-8 col-md-4"><?= h($log[$module.'.ip']); ?></div>
									    <?php endif ?>
								    </div>
							    <?php endif ?>
							    <?php if (array_key_exists($module.'.uri',$log)): ?>
								    <div class="row">
								        <div class="col-xs-4 col-md-2 text-right"><strong><?= __d('Qobo/ProjectTemplateCakephp', 'Uri'); ?></strong></div>
								        <div class="col-xs-8 col-md-4"><?= h($log[$module.'.uri']); ?></div>
								    </div>
								<?php endif ?>
								<?php if (array_key_exists($module.'.refer',$log)): ?>
								    <div class="row">
								        <div class="col-md-2 text-right"><strong><?= __d('Qobo/ProjectTemplateCakephp', 'Referrer'); ?></strong></div>
								        <div class="col-md-10"><?= h($log[$module.'.refer']); ?></div>
								    </div>
							    <?php endif ?>
							    <?php if (array_key_exists($module.'.message',$log)): ?>
								    <div class="row" style="margin-top:20px;">
								        <div class="col-md-2 text-right"><strong><?= __d('Qobo/ProjectTemplateCakephp', 'Message'); ?></strong></div>
								        <div class="col-md-10"><pre><small><?= trim(h($log[$module.'.message'])); ?></small></pre></div>
								    </div>
							    <?php endif ?>
							</div> <!-- .box-body -->
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