<?php
//
// About project section
//

use App\SystemInfo\Project;

$projectName = Project::getName();
$projectVersion = Project::getDisplayVersion();
$projectLogo = Project::getLogo('large');

?>
<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title"><?= __('About') ?></h3>
    </div>
    <div class="box-body">
        <p><img src="<?= $this->Url->image($projectLogo) ?>" alt="Site Logo" height="50" /></p>
        <p><?= __('Welcome to <strong>{0}</strong>. You are using version <strong>{1}</strong>.', $projectName, $projectVersion) ?></p>
    </div>
</div>
