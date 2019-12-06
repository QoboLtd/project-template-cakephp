<?php
//
// About Qobo
//
?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Qobo</h3>
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
                '<strong>{0}</strong> is a software development company, specializing in business applications.',
                $this->Html->link('Qobo', 'https://www.qobo.biz', ['target' => '_blank'])
            ) ?>
        </p>
        <p>
            <?= __d('Qobo/ProjectTemplateCakephp', 'Here are some useful links for more information:') ?>
            <ul>
                <li><?= $this->Html->link(__d('Qobo/ProjectTemplateCakephp', 'Qobo Website'), 'https://www.qobo.biz', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link(__d('Qobo/ProjectTemplateCakephp', 'Qobo Blog'), 'https://qobo.biz/blog/', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link(__d('Qobo/ProjectTemplateCakephp', 'Qobo Careers'), 'https://qobo.biz/careers/', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link(__d('Qobo/ProjectTemplateCakephp', 'Qobo on GitHub'), 'https://github.com/QoboLtd', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link(__d('Qobo/ProjectTemplateCakephp', 'Qobo on Facebook'), 'https://www.facebook.com/Qobo.biz/', ['target' => '_blank']) ?></li>
                <li><?= $this->Html->link(__d('Qobo/ProjectTemplateCakephp', 'Qobo on LinkedIn'), 'https://www.linkedin.com/company/3241664/', ['target' => '_blank']) ?></li>
            </ul>
            <?= __d('Qobo/ProjectTemplateCakephp', 'Feel free to contact Qobo via the {0} page.', $this->Html->link(__('Contact Us'), 'https://qobo.biz/contact/', ['target' => '_blank']) ) ?>
        </p>
    </div>
</div>
