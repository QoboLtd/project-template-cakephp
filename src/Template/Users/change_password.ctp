<?php
use Cake\Core\Configure;

if (! $validatePassword) :
    $this->layout = 'AdminLTE/login';
?>
<?= $this->Form->create('User') ?>
<fieldset>
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">
                <span class="fa fa-lock"></span>
            </span>
            <?= $this->Form->control('Users.password', [
                'type' => 'password',
                'required' => true,
                'label' => false,
                'placeholder' => __d('Qobo/ProjectTemplateCakephp', 'Password')
            ]); ?>
        </div>
    </div>
    <div class="form-group">
        <div class="input-group">
            <span class="input-group-addon">
                <span class="fa fa-lock"></span>
            </span>
            <?= $this->Form->control('Users.password_confirm', [
                'type' => 'password',
                'required' => true,
                'label' => false,
                'placeholder' => __d('Qobo/ProjectTemplateCakephp', 'Password Confirm')
            ]); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-8 col-xs-offset-2 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
            <?= $this->Form->button(
                '<span class="glyphicon glyphicon-log-in" aria-hidden="true"></span> ' . __d('Users', 'Submit'),
                ['class' => 'btn btn-primary btn-block']
            ); ?>
        </div>
    </div>
</fieldset>
<?= $this->Form->end() ?>
<?php endif; ?>
<?php
if ($validatePassword) : ?>
<section class="content-header">
    <h1>Change Password</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <?= $this->Form->create(); ?>
                <div class="box-body">
                    <?= $this->Form->control('Users.current_password', [
                        'type' => 'password',
                        'required' => true,
                        'placeholder' => __d('Qobo/ProjectTemplateCakephp', 'Current Password')
                    ]); ?>
                    <?= $this->Form->control('Users.password', [
                        'type' => 'password',
                        'required' => true,
                        'placeholder' => __d('Qobo/ProjectTemplateCakephp', 'Password')
                    ]); ?>
                    <?= $this->Form->control('Users.password_confirm', [
                        'type' => 'password',
                        'required' => true,
                        'placeholder' => __d('Qobo/ProjectTemplateCakephp', 'Password Confirm')
                    ]); ?>
                    <?= $this->Form->button(__d('Qobo/ProjectTemplateCakephp', 'Submit'), ['class' => 'btn btn-primary']) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>
