<?php
//
// CakePHP information
//

use App\SystemInfo\Cake;

$cakeVersion = Cake::getVersion();
$cakeVersionUrl = Cake::getVersionUrl();

$plugins = Cake::getLoadedPlugins();

?>
<div class="row">
    <div class="col-md-3">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __d('Qobo/ProjectTemplateCakephp', 'Summary') ?></h3>
            </div>
            <div class="box-body">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-birthday-cake"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= __d('Qobo/ProjectTemplateCakephp', 'CakePHP version') ?></span>
                        <span class="info-box-number"><?= $this->Html->link($cakeVersion, $cakeVersionUrl, ['target' => '_blank']) ?></span>
                    </div>
                </div>
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-plug"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= __d('Qobo/ProjectTemplateCakephp', 'CakePHP plugins') ?></span>
                        <span class="info-box-number"><?= number_format(count($plugins)); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __d('Qobo/ProjectTemplateCakephp', 'Loaded Plugins') ?></h3>
            </div>
            <div class="box-body">
                <ul>
                <?php foreach ($plugins as $plugin) : ?>
                    <li><?= $plugin ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <?= $this->element('System/about-cakephp'); ?>
    </div>
</div>
