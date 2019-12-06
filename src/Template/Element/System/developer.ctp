<?php
//
// Developer information
//

use App\SystemInfo\Project;
use App\SystemInfo\Git;

$currentVersion = Project::getDisplayVersion();
$buildVersions = Project::getBuildVersions();

$localChangesCommand = Git::getCommand('localChanges');
$localChanges = Git::getLocalChanges();

$localChangesOutput = "<b>$ " . $localChangesCommand . "</b>\n\n";
$localChangesOutput .= !empty($localChanges) ? implode("\n", $localChanges) : __d("Qobo/ProjectTemplateCakephp", "All good, no local modifications found.");

$localFeatures = file_exists(CONFIG . 'features_local.php');
?>
<div class="row">
    <div class="col-md-3">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __d('Qobo/ProjectTemplateCakephp', 'Build Summary') ?></h3>
            </div>
            <div class="box-body">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-tag"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= __d('Qobo/ProjectTemplateCakephp', 'Current version') ?></span>
                        <span class="info-box-number"><?= $currentVersion; ?></span>
                    </div>
                </div>
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-tag"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= __d('Qobo/ProjectTemplateCakephp', 'Current build') ?></span>
                        <span class="info-box-number"><?= $buildVersions['current']; ?></span>
                    </div>
                </div>
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-tag"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= __d('Qobo/ProjectTemplateCakephp', 'Deployed build') ?></span>
                        <span class="info-box-number"><?= $buildVersions['deployed']; ?></span>
                    </div>
                </div>
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-tag"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= __d('Qobo/ProjectTemplateCakephp', 'Previous build') ?></span>
                        <span class="info-box-number"><?= $buildVersions['previous']; ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __d('Qobo/ProjectTemplateCakephp', 'Local Changes') ?></h3>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-box">
                        <?php if (!$localFeatures) : ?>
                            <span class="info-box-icon bg-green"><i class="fa fa-thumbs-up"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text"><?= __d('Qobo/ProjectTemplateCakephp', 'Standard Features') ?></span>
                                <span class="info-box-number"></span>
                            </div>
                        <?php else : ?>
                            <span class="info-box-icon bg-red"><i class="fa fa-exclamation-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text"><?= __d('Qobo/ProjectTemplateCakephp', 'Local Features') ?></span>
                                <span class="info-box-number"></span>
                            </div>
                        <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="info-box">
                        <?php if (empty($localChanges)) : ?>
                            <span class="info-box-icon bg-green"><i class="fa fa-thumbs-up"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text"><?= __d('Qobo/ProjectTemplateCakephp', 'Changed files') ?></span>
                                <span class="info-box-number">0</span>
                            </div>
                        <?php else : ?>
                            <span class="info-box-icon bg-red"><i class="fa fa-exclamation-circle"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text"><?= __d('Qobo/ProjectTemplateCakephp', 'Changed files') ?></span>
                                <span class="info-box-number"><?php echo number_format(count($localChanges)); ?></span>
                            </div>
                        <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <pre><?php echo $localChangesOutput; ?></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
