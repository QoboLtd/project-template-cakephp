<?php
use App\Feature\Factory as FeatureFactory;
use CsvMigrations\FieldHandlers\FieldHandlerFactory;
use RolesCapabilities\Access\AccessFactory;

$fhf = new FieldHandlerFactory($this);

echo $this->Html->css('AdminLTE./bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker3.min', ['block' => 'css']);
echo $this->Html->script('AdminLTE./bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min', ['block' => 'scriptBottom']);
?>
<section class="content-header">
    <h1>User Profile</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-3">
            <!-- Profile Image -->
            <div class="box box-primary">
                <div class="box-body box-profile">
                <?= $this->Html->tag('img', false, ['src' => $user['image_src'], 'class' => 'profile-user-img img-responsive img-circle']) ?>

                <h3 class="profile-username text-center"><?= $user['name']; ?></h3>

                <p class="text-muted text-center">System User</p>

                </div>
                <!-- /.box-body -->
            </div>
        </div>
        <div class="col-md-9">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#info" data-toggle="tab">Info</a></li>
                    <li><a href="#settings" data-toggle="tab">Settings</a></li>
                </ul>
                <div class="tab-content">
                    <div class="active tab-pane" id="info">
                        <dl class="dl-horizontal">
                            <dt><?= __d('Qobo/ProjectTemplateCakephp', 'ID') ?></dt>
                            <dd><?= !empty($user['id']) ? h($user['id']) : '&nbsp;' ?></dd>
                            <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Username') ?></dt>
                            <dd><?= !empty($user['username']) ? h($user['username']) : '&nbsp;' ?></dd>
                            <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Email') ?></dt>
                            <dd><?= !empty($user['email']) ? h($user['email']) : '&nbsp;' ?></dd>
                            <dt><?= __d('Qobo/ProjectTemplateCakephp', 'First Name') ?></dt>
                            <dd><?= !empty($user['first_name']) ? h($user['first_name']) : '&nbsp;' ?></dd>
                            <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Last Name') ?></dt>
                            <dd><?= !empty($user['last_name']) ? h($user['last_name']) : '&nbsp;' ?></dd>
                            <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Country') ?></dt>
                            <dd><?php
                            if (!empty($user['country'])) {
                                $definition = [
                                    'name' => 'country',
                                    'type' => 'list(countries)',
                                    'required' => false
                                ];
                                echo $fhf->renderValue('Users', 'country', $user['country'], ['fieldDefinitions' => $definition]);
                            }
                            ?></dd>
                            <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Initials') ?></dt>
                            <dd><?= !empty($user['initials']) ? h($user['initials']) : '&nbsp;' ?></dd>
                            <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Gender') ?></dt>
                            <dd><?php
                            if (!empty($user['gender'])) {
                                $definition = [
                                    'name' => 'gender',
                                    'type' => 'list(genders)',
                                    'required' => false
                                ];
                                echo $fhf->renderValue('Users', 'gender', $user['gender'], ['fieldDefinitions' => $definition]);
                            }
                            ?></dd>
                            <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Birthdate') ?></dt>
                            <dd><?= !empty($user['birthdate']) ? $user['birthdate']->i18nFormat('yyyy-MM-dd') : '&nbsp;' ?></dd>
                            <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Phone Office') ?></dt>
                            <dd><?= !empty($user['phone_office']) ? h($user['phone_office']) : '&nbsp;' ?></dd>
                            <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Phone Home') ?></dt>
                            <dd><?= !empty($user['phone_home']) ? h($user['phone_home']) : '&nbsp;' ?></dd>
                            <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Phone Mobile') ?></dt>
                            <dd><?= !empty($user['phone_mobile']) ? h($user['phone_mobile']) : '&nbsp;' ?></dd>
                            <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Phone Extension') ?></dt>
                            <dd><?= !empty($user['phone_extension']) ? h($user['phone_extension']) : '&nbsp;' ?></dd>
                        </dl>
                        <?= $this->Html->link(
                            '<i class="fa fa-lock"></i> ' . __d('Users', 'Change Password'),
                            ['controller' => 'Users', 'action' => 'changePassword'],
                            ['escape' => false, 'class' => 'btn btn-default btn-sm']
                        ); ?>
                        <?php
                            $factory = new AccessFactory();
                            $feature = FeatureFactory::get('Module' . DS . 'Settings');

                            if($feature->isActive() && $factory->hasAccess(['controller' => 'settings', 'action' => 'my'], $user)):
                        ?>
                            <?= $this->Html->link(
                                '<i class="fa fa-gears"></i>' . __d('Users', 'User Settings'),
                                ['controller' => 'settings', 'action' => 'my'],
                                ['class' => 'btn btn-default btn-sm', 'escape' => false]
                            ) ?>
                        <?php endif; ?>
                    </div>
                    <!-- /.tab-pane -->

                    <div class="tab-pane" id="settings">
                        <?= $this->Form->create(null, [
                            'url' => [
                                'controller' => 'Users',
                                'action' => 'edit-profile'
                            ]
                        ]); ?>
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                            <?= $this->Form->control('Users.username', [
                                'placeholder' => __d('Qobo/ProjectTemplateCakephp', 'Username'),
                                'value' => !empty($user['username']) ? h($user['username']) : null
                            ]); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                            <?= $this->Form->control('Users.first_name', [
                                'placeholder' => __d('Qobo/ProjectTemplateCakephp', 'First Name'),
                                'value' => !empty($user['first_name']) ? h($user['first_name']) : null
                            ]); ?>
                            </div>
                            <div class="col-xs-12 col-md-6">
                            <?= $this->Form->control('Users.last_name', [
                                'placeholder' => __d('Qobo/ProjectTemplateCakephp', 'Last Name'),
                                'value' => !empty($user['last_name']) ? h($user['last_name']) : null
                            ]); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                            <?= $this->Form->label('Users.country'); ?>
                            <?= $fhf->renderInput('Users', 'country', $user, [
                                'fieldDefinitions' => [
                                    'name' => 'country',
                                    'type' => 'list(countries)',
                                    'required' => false,
                                ],
                                'label' => false
                            ]); ?>
                            </div>
                            <div class="col-xs-12 col-md-6">
                            <?= $this->Form->control('Users.initials', [
                                'placeholder' => __d('Qobo/ProjectTemplateCakephp', 'Initials'),
                                'value' => !empty($user['initials']) ? h($user['initials']) : null
                            ]); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                            <?= $this->Form->label('Users.gender'); ?>
                            <?= $fhf->renderInput('Users', 'gender', $user, [
                                'fieldDefinitions' => [
                                    'name' => 'gender',
                                    'type' => 'list(genders)',
                                    'required' => false,
                                ],
                                'label' => false
                            ]); ?>
                            </div>
                            <div class="col-xs-12 col-md-6">
                            <?= $this->Form->control('Users.birthdate', [
                                'type' => 'text',
                                'label' => 'Birthdate',
                                'data-provide' => 'datepicker',
                                'autocomplete' => 'off',
                                'data-date-format' => 'yyyy-mm-dd',
                                'data-date-autoclose' => true,
                                'value' => !empty($user['birthdate']) ? $user['birthdate']->i18nFormat('yyyy-MM-dd') : null,
                                'templates' => [
                                    'input' => '<div class="input-group">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="{{type}}" name="{{name}}"{{attrs}}/>
                                    </div>'
                                ]
                            ]); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                            <?= $this->Form->control('Users.email', [
                                'placeholder' => __d('Qobo/ProjectTemplateCakephp', 'Email'),
                                'value' => !empty($user['email']) ? h($user['email']) : null
                            ]); ?>
                            </div>
                            <div class="col-xs-12 col-md-6">
                            <?= $this->Form->control('Users.phone_office', [
                                'placeholder' => __d('Qobo/ProjectTemplateCakephp', 'Office Phone'),
                                'value' => !empty($user['phone_office']) ? h($user['phone_office']) : null
                            ]); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                            <?= $this->Form->control('Users.phone_home', [
                                'placeholder' => __d('Qobo/ProjectTemplateCakephp', 'Home Phone'),
                                'value' => !empty($user['phone_home']) ? h($user['phone_home']) : null
                            ]); ?>
                            </div>
                            <div class="col-xs-12 col-md-6">
                            <?= $this->Form->control('Users.phone_mobile', [
                                'placeholder' => __d('Qobo/ProjectTemplateCakephp', 'Mobile Phone'),
                                'value' => !empty($user['phone_mobile']) ? h($user['phone_mobile']) : null
                            ]); ?>
                            </div>
                            <div class="col-xs-12 col-md-6">
                            <?= $this->Form->control('Users.phone_extension', [
                                'placeholder' => __d('Qobo/ProjectTemplateCakephp', 'Phone Extension'),
                                'value' => !empty($user['phone_extension']) ? h($user['phone_extension']) : null
                            ]); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-md-6">
                                <?= $this->Form->button(__d('Qobo/ProjectTemplateCakephp', 'Submit'), ['class' => 'btn btn-primary']) ?>
                            </div>
                        </div>
                        <?= $this->Form->end() ?>
                    </div>
                    <!-- /.tab-pane -->
                </div>
                <!-- /.tab-content -->
            </div>
            <!-- /.nav-tabs-custom -->
        </div>
    </div>
</section>
