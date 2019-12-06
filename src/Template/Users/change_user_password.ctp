<?php
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

$fullname = '';
if (!empty($this->passedArgs[0])) {
    $entity = TableRegistry::get('Users')->get($this->passedArgs[0]);
    $fullname = $entity->name;
}
?>
<section class="content-header">
    <h1>Change User Password</h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= __d('Qobo/ProjectTemplateCakephp', 'Please enter the new password for user {0}', '<i>' . $entity->username . '</i>') ?> <?= empty($fullname) ? '' : "($fullname)" ?></h3>
                </div>
                <?= $this->Form->create('User') ?>
                <div class="box-body">
                    <?= $this->Form->control('password'); ?>
                    <?= $this->Form->control('password_confirm', ['type' => 'password', 'required' => true]); ?>
                    <?= $this->Form->button(__d('Qobo/ProjectTemplateCakephp', 'Submit'), ['class' => 'btn btn-primary']) ?>
                </div>
                <?= $this->Form->end() ?>
            </div>
        </div>
    </div>
</section>
