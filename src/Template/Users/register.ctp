<?php
use Cake\Core\Configure;

$this->layout = 'AdminLTE/login';
?>
<?= $this->Form->create($user); ?>
<fieldset>
    <legend><?= __d('CakeDC/Users', 'Registration') ?></legend>
    <?= $this->Form->control('username', ['placeholder' => 'Username', 'required' => true, 'label' => false]); ?>
    <?= $this->Form->control('email', ['placeholder' => 'Email', 'required' => true, 'label' => false]); ?>
    <?= $this->Form->control('password', ['placeholder' => 'Password', 'required' => true, 'label' => false]); ?>
    <?= $this->Form->control('password_confirm', ['type' => 'password', 'placeholder' => 'Confirm password', 'required' => true, 'label' => false]); ?>
    <?= $this->Form->control('first_name', ['placeholder' => 'First name', 'label' => false]); ?>
    <?= $this->Form->control('last_name', ['placeholder' => 'Last name', 'label' => false]); ?>
    <?php if ((bool)Configure::read('Users.Tos.required')) : ?>
        <div class="form-group">
        <?php
            $label = $this->Form->label('tos', __d('Users', 'Accept TOS conditions?'));
            echo $this->Form->control('tos', [
                'type' => 'checkbox',
                'class' => 'square',
                'required' => true,
                'label' => false,
                'templates' => [
                    'inputContainer' => '<div class="{{required}}">' . $label . '<div class="clearfix"></div>{{content}}</div>'
                ]
            ]);
        ?>
        </div>
    <?php endif; ?>
    <?php
    if ((bool)Configure::read('Users.Registration.reCaptcha') && (bool)Configure::read('Users.reCaptcha.registration')) {
        echo $this->User->addReCaptcha();
    }
    ?>
</fieldset>
<?= $this->Form->button(__d('Qobo/ProjectTemplateCakephp', 'Register'), ['class' => 'btn btn-primary btn-block']) ?>
<?= $this->Form->end() ?>
