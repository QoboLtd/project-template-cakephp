<?php
//
// Composer information
//

use App\SystemInfo\Composer;

$packages = Composer::getInstalledPackages();
$count = count($packages);

// Words to match in composer libraries and the icon to use
// for display count
$matches = [
    'cakephp' => 'birthday-cake',
    'qobo' => 'copyright'
];
$matchWords = array_keys($matches);
$matchCounts = Composer::getMatchCounts($packages, $matchWords);

?>
<div class="row">
    <div class="col-md-3">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __d('Qobo/ProjectTemplateCakephp', 'Composer Summary') ?></h3>
            </div>
            <div class="box-body">

                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-book"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= __d('Qobo/ProjectTemplateCakephp', 'Composer libraries') ?></span>
                        <span class="info-box-number"><?php echo number_format($count); ?></span>
                    </div>
                </div>
                <?php foreach ($matchCounts as $word => $count) : ?>
                    <div class="info-box">
                        <span class="info-box-icon bg-blue"><i class="fa fa-<?= $matches[$word] ?>"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text"><?= __d('Qobo/ProjectTemplateCakephp', 'Match {0}', $word); ?></span>
                            <span class="info-box-number"><?php echo number_format($count); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-md-9">
        <?php if (0 < $count) : ?>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __d('Qobo/ProjectTemplateCakephp', 'Installed Dependencies') ?></h3>
            </div>
            <div class="box-body">
                <table class="table table-hover table-condensed table-vertical-align">
                    <thead>
                        <tr>
                            <th width="20%"><?= __d('Qobo/ProjectTemplateCakephp', 'Name') ?></th>
                            <th width="10%"><?= __d('Qobo/ProjectTemplateCakephp', 'Version') ?></th>
                            <th width="10%"><?= __d('Qobo/ProjectTemplateCakephp', 'License') ?></th>
                            <th width="60%"><?= __d('Qobo/ProjectTemplateCakephp', 'Description') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($packages as $package) : ?>
                            <?php
                                $url = '';
                                if (!empty($package['homepage'])) {
                                    $url = $package['homepage'];
                                }
                                if (empty($url) && !empty($package['source']['url'])) {
                                    $url = $package['source']['url'];
                                }
                            ?>
                            <tr>
                                <td><?= empty($url) ? $package['name'] : $this->Html->link($package['name'], $url, ['target' => '_blank']); ?></td>
                                <td><?= $package['version'] ?></td>
                                <td><?= implode(', ', $package['license']); ?></td>
                                <td><?= empty($package['description']) ? '&nbsp;' : $package['description'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
