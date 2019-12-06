<?php
//
// PHP setup
//

use App\SystemInfo\Php;

$setup = [
    'Server API' => Php::getSapi(),
    'PHP Server User' => Php::getUser(),
    'PHP Binary' => Php::getBinary(),
    'PHP Configuration File' => Php::getIniPath(),
    'Max Execution Time' => Php::getMaxExecutionTime() . ' seconds',
    'Memory Limit' => $this->Number->toReadableSize(Php::getMemoryLimit()),
    'Upload Max Filesize' => $this->Number->toReadableSize(Php::getUploadMaxFilesize()),
    'Post Max Size' => $this->Number->toReadableSize(Php::getPostMaxSize()),
];

// PHP extensions
$extensions = Php::getLoadedExtensions();
?>
<div class="row">
    <div class="col-md-3">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __d('Qobo/ProjectTemplateCakephp', 'PHP Summary') ?></h3>
            </div>
            <div class="box-body">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-heart"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= __d('Qobo/ProjectTemplateCakephp', 'PHP version') ?></span>
                        <span class="info-box-number"><?= Php::getVersion() ?></span>
                    </div>
                </div>
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-plug"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= __d('Qobo/ProjectTemplateCakephp', 'PHP extensions') ?></span>
                        <span class="info-box-number"><?php echo number_format(count($extensions)); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __d('Qobo/ProjectTemplateCakephp', 'PHP Configuration') ?></h3>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                <?php
                    foreach ($setup as $name => $value) {
                        print '<dt>' . $name . ':</dt><dd>' . $value . '</dd>';
                    }
                ?>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-md-5">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __d('Qobo/ProjectTemplateCakephp', 'Loaded Extensions') ?></h3>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                <?php
                    foreach ($extensions as $name => $version) {
                        print '<dt>' . $name . ':</dt><dd>' . $version . '</dd>';
                    }
                ?>
                </dl>
            </div>
        </div>
    </div>
</div>
