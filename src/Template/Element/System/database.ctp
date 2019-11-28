<?php
//
// Database information
//

use App\SystemInfo\Database;

$driver = Database::getDriver();
$allTables = Database::getTables();
$skipTables = Database::getTables('phinxlog');
$tableStats = Database::getTablesStats($allTables);
$totalSize = 0;
foreach ($tableStats as $table => $stats) {
    if (is_numeric($stats['size'])) {
        $totalSize += $stats['size'];
    }
}

$totalSize = ($totalSize > 0) ? $this->Number->toReadableSize($totalSize) : 'N/A';

?>
<div class="row">
    <div class="col-md-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('Database Summary') ?></h3>
            </div>
            <div class="box-body">
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-database"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= __('Database engine') ?></span>
                        <span class="info-box-number"><?= $driver ?></span>
                    </div>
                </div>
                <div class="info-box">
                    <span class="info-box-icon bg-blue"><i class="fa fa-database"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= __('Database size') ?></span>
                        <span class="info-box-number"><?= $totalSize; ?></span>
                    </div>
                </div>
                <div class="info-box bg-blue">
                    <span class="info-box-icon"><i class="fa fa-database"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text"><?= __('Total tables') ?></span>
                        <span class="info-box-number"><?php echo number_format(count($allTables)); ?></span>
                        <div class="progress">
                            <div class="progress-bar" style="width: <?php echo $this->SystemInfo->getProgressValue(count($skipTables), count($allTables)); ?>"></div>
                        </div>
                        <span class="progress-description"><?= __('{0} system tables', count($skipTables)) ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('Table Records') ?></h3>
            </div>
            <div class="box-body">
                <?php foreach ($tableStats as $table => $stats) : ?>
                    <?php
                        if (in_array($table, $skipTables)) {
                            continue;
                        }
                    ?>
                    <div class="info-box bg-aqua">
                        <span class="info-box-icon"><i class="fa fa-table"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text"><?php echo $table; ?></span>
                            <span class="info-box-number"><?= __('{0} records', number_format($stats['total'])) ?></span>
                            <div class="progress">
                                <div class="progress-bar" style="width: <?php echo $this->SystemInfo->getProgressValue($stats['deleted'], $stats['total']); ?>"></div>
                            </div>
                            <span class="progress-description">
                                <?php __('{0} deleted records ({1})', number_format($stats['deleted']), $this->SystemInfo->getProgressValue($stats['deleted'], $stats['total'])) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"><?= __('Table Sizes') ?></h3>
            </div>
            <div class="box-body">
                <?php foreach ($tableStats as $table => $stats) : ?>
                    <?php
                        if (in_array($table, $skipTables)) {
                            continue;
                        }
                        if (is_numeric($stats['size'])) {
                            $stats['size'] = $this->Number->toReadableSize($stats['size']);
                        }
                    ?>
                    <div class="info-box">
                        <span class="info-box-icon bg-aqua"><i class="fa fa-table"></i></span>
                        <div class="info-box-content">
                        <span class="info-box-text"><?php echo $table; ?></span>
                            <span class="info-box-number"><?= $stats['size'] ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
