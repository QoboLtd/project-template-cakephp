<?php
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use CsvMigrations\FieldHandlers\FieldHandlerFactory;

$fhf = new FieldHandlerFactory($this);

echo $this->Html->css(
    [
        'AdminLTE./bower_components/select2/dist/css/select2.min',
        'Qobo/Utils.select2-bootstrap.min',
        'Qobo/Utils.select2-style',
    ],
    ['block' => 'css']
);

echo $this->Html->script(
    [
        'AdminLTE./bower_components/select2/dist/js/select2.full.min',
        'Qobo/Utils.select2.init',
    ],
    ['block' => 'scriptBottom']
);

$this->Html->scriptStart(array('block' => 'scriptBottom', 'inline' => false)); ?>

$(document).ready(function(){
	$('#settings-search').on('input',function(){
		let search = $(this).val();
		let labels = $('.tab-content').find('label')
		$('.tab-content').find("input").css("background-color","white")
		if(!(search.length > 2)){
			return
		}
		$.each(labels,function(label,data){
			let lab = $(data).text().toUpperCase()
			if(~lab.indexOf(search.toUpperCase())){
				let id = $(data).attr('for')
				$('.tab-content').find('#'+id).css("background-color","aquamarine")
				$('form').find('.active').removeClass('active');
				// active section
				let col = $('.tab-content').find('#'+id).closest('.tab-pane')
				col.addClass('active')
				let panel = $('.tab-content').find('.active').attr('id')
				let tab = $('#'+panel).parent().closest('.tab-pane')
				// active col
				$("a[href='#"+ col.attr('id') +"']").parent().addClass('active')
				// active panel right
				tab.addClass('active')
				// active tab
				$("a[data-tab='"+ tab.attr('id') +"']").parent().addClass('active')
			}
		})
	})

	$('.tab-content input').on('click',function(){
		$(this).css("background-color","white")
	})
});

<?php $this->Html->scriptEnd(); ?>
<section class="content-header">
	<h1><?= __d('Qobo/ProjectTemplateCakephp', 'Settings'); ?>
		<?= isset($afterTitle) ? ' » '. $afterTitle : '' ?>
		<?= isset($linkToGenerator) ? '<h4><a href=/settings/generator/>settings.php file builder utility</a></h4>' : '' ?>
	</h1>
</section>
<section class="content">
	<div class="row">
		<div class="col-md-12">
			<?= $this->Form->create($settings); ?>
			<div class="nav-tabs-custom">
				<ul class="nav nav-tabs">
					<?php
					// Tab
					$first = true;
					foreach ($data as $tab => $columns) :
						$id_tab = str_replace(' ','_',$tab);
						echo $first ? '<li class="active">' : '<li>';
						?>
						<a href="#<?= $id_tab ?>" data-tab="<?= $id_tab ?>" data-toggle="tab" aria-expanded="true"><?= $tab ?></a></li>
						<?php
						$first = false;
					endforeach;
					?>
					<li class="pull-right">
						<div class="navbar-form" role="search">
								<input type="text" class="form-control" id="settings-search" placeholder="Search">
						</div>
					</li>
				</ul>
				<div class="tab-content">
					<?php
					$first = true;
					foreach ($data as $tab => $columns) :
						$id_tab = str_replace(' ','_',$tab);
						echo $first ? '<div class="tab-pane active" id="' . $id_tab . '">' : '<div class="tab-pane" id="' . $id_tab . '">';
						?>
						<div class="row">
							<div class="col-md-3">
								<ul class="nav nav-pills nav-stacked">
									<?php
									$first = true;
									foreach ($columns as $column => $tab) :
										$active = $first ? 'class="active"' : '';
										$id_column = str_replace(' ','_',$column);
										?>
											<li <?= $active ?>><a href="#<?= $id_tab .'_'. $id_column ?>" data-toggle="tab"><?= $column ?></a></li>
										<?php
										$first = false;
									endforeach;
									?>
								</ul>
							</div>
							<div class="tab-content col-md-9">
								<?php
								$first = true;
								// Columns
								foreach ($columns as $column => $sections) :
									$active = $first ? 'active' : '';
									$id_column = str_replace(' ','_',$column);
									?>
										<div class="tab-pane <?= $active ?>" id="<?= $id_tab .'_'. $id_column ?>">
									<?php
									// Section
									foreach ($sections as $section => $fields) :
										?>
											<div class="box box-primary">
											<div class="box-header">
											<h3 class="box-title"><?= $section ?></h3>
											</div>
											<div class="box-body">
										<?php
										// Fields
										foreach ($fields as $field => $fieldValue) :
											$value = $configure[$fieldValue['alias']];
											$alias = 'Settings.' . $fieldValue['alias'];
											$definition = [
												'type'  => $fieldValue['type'],
												'value' => $value,
												'name'  => $alias,
											];
                                            $fieldDefinition = [
                                                'fieldDefinitions' => $definition,
                                                'label' => $field
                                            ];
                                            if ($fieldValue['type'] === 'list' && !empty($fieldValue['selectOptions'])) {
                                                $fieldDefinition['selectOptions'] = $fieldValue['selectOptions'];
                                            }
                                            echo $fhf->renderInput('settings', $alias, $value, $fieldDefinition);
                                            if(isset($fieldValue['help']) ):
												?>
													<span class="help-block"><?= $fieldValue['help'] ?></span>
												<?php
											endif;
										endforeach;
										?>
										</div>
										</div>
									<?php
									endforeach;
									?>
									</div>
								<?php
									$first = false;
								endforeach;
								?>
							</div>
						</div>
						</div>
					<?php
						$first = false;
					endforeach;
					?>
				</div>
			</div>
			<?php
				echo $this->Form->button(__d('Qobo/ProjectTemplateCakephp', 'Submit'), ['class' => 'btn btn-primary','value' => 'submit']);
				echo $this->Form->end();
			?>
		</div>
	</div>
</section>
