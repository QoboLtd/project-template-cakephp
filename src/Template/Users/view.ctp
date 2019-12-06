<?php
use CsvMigrations\FieldHandlers\FieldHandlerFactory;

$factory = new FieldHandlerFactory($this);
?>
<section class="content-header">
    <h1><?= $this->Html->link(__d('Qobo/ProjectTemplateCakephp', 'Users'), ['action' => 'index']) . ' &raquo; ' . h($Users->username) ?></h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <i class="fa fa-user"></i>

                    <h3 class="box-title">User Information</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'ID') ?></dt>
                        <dd><?= $Users->has('id') ? h($Users->id) : '&nbsp;' ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Username') ?></dt>
                        <dd><?= $Users->has('username') ? h($Users->username) : '&nbsp;' ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Active') ?></dt>
                        <dd><?= $Users->has('active') && $Users->active ? __d('Qobo/ProjectTemplateCakephp', 'Yes') : __('No') ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Created') ?></dt>
                        <dd><?= $Users->has('created') ? $Users->created->i18nFormat('yyyy-MM-dd HH:mm') : '&nbsp;' ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Modified') ?></dt>
                        <dd><?= $Users->has('modified') ? $Users->modified->i18nFormat('yyyy-MM-dd HH:mm') : '&nbsp;' ?></dd>
                    </dl>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <i class="fa fa-info-circle"></i>

                    <h3 class="box-title">Personal Details</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'First Name') ?></dt>
                        <dd><?= $Users->has('first_name') ? h($Users->first_name) : '&nbsp;' ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Last Name') ?></dt>
                        <dd><?= $Users->has('last_name') ? h($Users->last_name) : '&nbsp;' ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Country') ?></dt>
                        <dd><?= $factory->renderValue('Users', 'country', $Users, ['fieldDefinitions' => [
                            'name' => 'country',
                            'type' => 'list(countries)',
                            'required' => false
                        ]]) ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Initials') ?></dt>
                        <dd><?= $Users->has('initials') ? h($Users->initials) : '&nbsp;' ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Gender') ?></dt>
                        <dd><?= $factory->renderValue('Users', 'gender', $Users, ['fieldDefinitions' => [
                            'name' => 'gender',
                            'type' => 'list(genders)',
                            'required' => false
                        ]]) ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Birthdate') ?></dt>
                        <dd><?= $Users->has('birthdate') ? $Users->birthdate->i18nFormat('yyyy-MM-dd') : '&nbsp;' ?></dd>
                    </dl>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <i class="fa fa-phone"></i>

                    <h3 class="box-title">Contact Details</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Email') ?></dt>
                        <dd><?= $Users->has('email') ? h($Users->email) : '&nbsp;' ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Phone Office') ?></dt>
                        <dd><?= $Users->has('phone_office') ? h($Users->phone_office) : '&nbsp;' ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Phone Home') ?></dt>
                        <dd><?= $Users->has('phone_home') ? h($Users->phone_home) : '&nbsp;' ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Phone Mobile') ?></dt>
                        <dd><?= $Users->has('phone_mobile') ? h($Users->phone_mobile) : '&nbsp;' ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Phone Extension') ?></dt>
                        <dd><?= $Users->has('phone_extension') ? h($Users->phone_extension) : '&nbsp;' ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Fax') ?></dt>
                        <dd><?= $Users->has('fax') ? h($Users->fax) : '&nbsp;' ?></dd>
                    </dl>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <i class="fa fa-building"></i>

                    <h3 class="box-title">Company Details</h3>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Company') ?></dt>
                        <dd><?= $Users->has('company') ? h($Users->company) : '&nbsp;' ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Department') ?></dt>
                        <dd><?= $Users->has('department') ? h($Users->department) : '&nbsp;' ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Team') ?></dt>
                        <dd><?= $Users->has('team') ? h($Users->team) : '&nbsp;' ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Position') ?></dt>
                        <dd><?= $Users->has('position') ? h($Users->position) : '&nbsp;' ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Reports To') ?></dt>
                        <dd><?= $factory->renderValue('Users', 'reports_to', $Users, ['fieldDefinitions' => [
                            'name' => 'reports_to',
                            'type' => 'related(Users)',
                            'required' => false
                        ]]) ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Is Supervisor') ?></dt>
                        <dd><?= $Users->has('is_supervisor') && $Users->get('is_supervisor') ? __d('Qobo/ProjectTemplateCakephp', 'Yes') : __('No') ?></dd>
                    </dl>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /.box -->
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul id="relatedTabs" class="nav nav-tabs" role="tablist">
                <?php if (! empty($userGroups)) : ?>
                    <li role="presentation" class="active">
                        <?= $this->Html->link(__d('Qobo/ProjectTemplateCakephp', 'Groups'), '#groups', [
                            'aria-controls' => 'groups',
                            'role' => 'tab',
                            'data-toggle' => 'tab',
                        ]);?>
                    </li>
                <?php endif ?>
                <?php if (! empty($subordinates)) : ?>
                    <li role="presentation">
                        <?= $this->Html->link(__d('Qobo/ProjectTemplateCakephp', 'Subordinates'), '#subordinates', [
                            'aria-controls' => 'subordinates',
                            'role' => 'tab',
                            'data-toggle' => 'tab',
                        ]);?>
                    </li>
                <?php endif ?>
                </ul>
                <div class="tab-content">
                <?php if (! empty($userGroups)) : ?>
                    <div role="tabpanel" class="tab-pane active" id="groups">
                        <div class="table-responsive">
                            <table class="table table-hover table-condensed table-vertical-align">
                                <thead>
                                    <tr>
                                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'Name');?></th>
                                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'Description');?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userGroups as $group) : ?>
                                        <tr>
                                            <td><?= $this->Html->link($group->get('name'), [
                                                'plugin' => 'Groups',
                                                'controller' => 'Groups',
                                                'action' => 'view',
                                                $group->get('id')
                                            ]) ?></td>
                                            <td><?= $group->get('description') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif ?>
                <?php if (! empty($subordinates)) : ?>
                    <div role="tabpanel" class="tab-pane" id="subordinates">
                        <div class="table-responsive">
                            <table class="table table-hover table-condensed table-vertical-align">
                                <thead>
                                    <tr>
                                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'Name') ?></th>
                                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'Department') ?></th>
                                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'Team') ?></th>
                                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'Position') ?></th>
                                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'Phone Office') ?></th>
                                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'Phone Extension') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($subordinates as $subordinate) : ?>
                                        <tr>
                                            <td><?= $this->Html->link($subordinate->get('name'), [
                                                'controller' => 'Users',
                                                'action' => 'view',
                                                $subordinate->get('id')
                                            ]) ?></td>
                                            <td><?= $subordinate->get('department') ?></td>
                                            <td><?= $subordinate->get('team') ?></td>
                                            <td><?= $subordinate->get('position') ?></td>
                                            <td><?= $subordinate->get('phone_office') ?></td>
                                            <td><?= $subordinate->get('phone_extension') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif ?>
                </div>
            </div>
        </div>
    </div>
</section>
