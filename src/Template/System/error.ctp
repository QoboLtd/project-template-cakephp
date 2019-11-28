<?php

use Cake\Routing\Router;

if (empty($user)) {
    $this->layout = 'error';
}

$currentError = $this->request->getSession()->read('currentError');

$code = '000';
$message = 'Unknown Error';

$url = '';

if (!empty($currentError)) {
    $currentError = json_decode($currentError, true);

    $url = $currentError['url'];
    $code = $currentError['code'];
    $message = $currentError['message'];
}
?>
<div class="container">
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="box box-danger box-solid" style="margin-top:80px;">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    <?= __d('cake', 'Error') ?> <?= h($code) ?>: <?= h($message) ?>
                </h3>
            </div>
            <div class="box-body">
                <?= __('There was a problem processing your request.  Please notify your system administrator.'); ?>
            </div>
            <div class="box-footer">
                <b>URL: </b><?= h(Router::url($url, true)) ?>
            </div>
        </div>
    </div>
</div>
</div>
