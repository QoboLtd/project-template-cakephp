<?php
//
// Server information
//

use App\SystemInfo\Server;

$server = Server::getInfo();

?>
<div class="row">
    <div class="col-md-3">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __d('Qobo/ProjectTemplateCakephp', 'Summary') ?></h3>
            </div>
            <div class="box-body">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-linux"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= __d('Qobo/ProjectTemplateCakephp', 'Operating System') ?></span>
                        <span class="info-box-number"><?php echo PHP_OS; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __d('Qobo/ProjectTemplateCakephp', 'Hardware') ?></h3>
            </div>
            <div class="box-body">
                <dl class="dl-horizontal">
                <?php
                    foreach ($server as $name => $value) {
                        print '<dt>' . $name . ':</dt><dd>' . $value . '</dd>';
                    }
                ?>
                </dl>
            </div>
        </div>
    </div>
</div>
