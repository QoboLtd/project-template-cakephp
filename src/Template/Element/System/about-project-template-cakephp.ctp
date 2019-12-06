<?php
//
// About project-template-cakephp
//
?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">project-template-cakephp</h3>
    </div>
    <div class="box-body">
        <p>
        <?php
            echo $this->Html->link(
                $this->Html->image('branding/qobo/logo.png', [
                            'alt' => 'Qobo',
                            'class' => 'img img-responsive',
                ]),
                'https://www.qobo.biz',
                [
                    'target' => '_blank',
                    'escape' => false
                ]
            );
        ?>
        </p>
        <p>
            <?= (string)__(
                'This project is built with <strong>{0}</strong>.',
                 $this->Html->link('project-template-cakephp', 'https://github.com/QoboLtd/project-template-cakephp/', ['target' => '_blank']),
            ) ?>
            <?= (string)__(
                'This template is developed by {0} and aims to assist web developers in rapidly creating new web applications, powered by {1} framework.
                It is also used in the award-winning {2} platform.',
                $this->Html->link('Qobo', 'https://www.qobo.biz', ['target' => '_blank']),
                $this->Html->link('CakePHP', 'https://cakephp.org', ['target' => '_blank']),
                $this->Html->link('Qobrix', 'https://qobrix.com', ['target' => '_blank'])
            ) ?>
        </p>
        <p>
            <?= __d('Qobo/ProjectTemplateCakephp', 'Here are some useful links for more information:') ?>
            <ul>
                <li><?= $this->Html->link('project-template-cakephp on GitHub', 'https://github.com/QoboLtd/project-template-cakephp/', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link(__d('Qobo/ProjectTemplateCakephp', 'Qobo Website'), 'https://www.qobo.biz', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link(__d('Qobo/ProjectTemplateCakephp', 'Qobrix Website'), 'https://qobrix.com', ['target' => '_blank']) ?></li>
            </ul>
        </p>
    </div>
</div>
