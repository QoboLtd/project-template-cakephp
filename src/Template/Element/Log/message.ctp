<?php
$defaultLog = [
    'hostname' => '',
    'ip' => '',
    'uri' => '',
    'refer' => '',
    'message' => '',
    'context' => '',
];
$log = array_merge($defaultLog, $log->toArray());
?>

<div class="box-body">
    <div class="row">
        <div class="col-xs-4 col-md-2 text-right"><strong><?= __('Hostname'); ?></strong></div>
        <div class="col-xs-8 col-md-4"><?= h($log['hostname']); ?></div>
        <div class="col-xs-4 col-md-2 text-right"><strong><?= __('IP'); ?></strong></div>
        <div class="col-xs-8 col-md-4"><?= h($log['ip']); ?></div>
    </div>
    <div class="row">
        <div class="col-xs-4 col-md-2 text-right"><strong><?= __('Uri'); ?></strong></div>
        <div class="col-xs-8 col-md-4"><?= h($log['uri']); ?></div>
    </div>
    <div class="row">
        <div class="col-md-2 text-right"><strong><?= __('Referrer'); ?></strong></div>
        <div class="col-md-10"><?= h($log['refer']); ?></div>
    </div>
    <div class="row">
        <div class="col-md-2 text-right"><strong><?= __('Summary'); ?></strong></div>
        <div class="col-md-10"><?= trim(h($log['summary'])); ?></div>
    </div>
    <div class="row" style="margin-top:20px;">
        <div class="col-md-2 text-right"><strong><?= __('Message'); ?></strong></div>
        <div class="col-md-10"><pre><small><?= trim(h($log['message'])); ?></small></pre></div>
    </div>
    <?php if (!empty($log['context']['scope'])) : ?>
    <div class="row">
        <div class="col-md-2 text-right"><strong><?= __('Context'); ?></strong></div>
        <div class="col-md-10"><pre><small><?= h($log['context']); ?></small></pre></div>
    </div>
    <?php endif; ?>
</div> <!-- .box-body -->

