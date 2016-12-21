<?php
$displayName = $user['first_name'] . ' ' . $user['last_name'];
if (empty(trim($displayName))) {
    $displayName = $user['username'];
}
?>
<div class="user-panel">
    <div class="pull-left image">
        <?php echo $this->Html->image('user-image-160x160.png', array('class' => 'img-circle', 'alt' => 'User Image')); ?>
    </div>
    <div class="pull-left info">
        <p><?= $displayName; ?></p>
        <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
    </div>
</div>