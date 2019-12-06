<?php
//
// Project information
//

use App\SystemInfo\Project;

$projectName = Project::getName();
$projectVersion = Project::getDisplayVersion();
$projectUrl = Project::getUrl();

?>
<div class="row">
    <div class="col-md-3">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __d('Qobo/ProjectTemplateCakephp', 'Project Information') ?></h3>
            </div>
            <div class="box-body">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-info-circle"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= __d('Qobo/ProjectTemplateCakephp', 'Project name') ?></span>
                        <span class="info-box-number"><?= $projectName ?></span>
                    </div>
                </div>
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-tag"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= __d('Qobo/ProjectTemplateCakephp', 'Project version') ?></span>
                        <span class="info-box-number"><?= $projectVersion ?></span>
                    </div>
                </div>

                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-link"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= __d('Qobo/ProjectTemplateCakephp', 'Project URL') ?></span>
                        <span class="info-box-number"><?= $this->Html->link($projectUrl, $projectUrl, ['target' => '_blank']) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <?= $this->element('System/about'); ?>
    </div>
</div>
