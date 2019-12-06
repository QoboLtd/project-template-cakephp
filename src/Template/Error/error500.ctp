<?php
use Cake\Core\Configure;
use Cake\Error\Debugger;
use Cake\Routing\Router;

$this->layout = 'error';

if (Configure::read('debug')) :
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error500.ctp');

    $this->start('file');
?>
<?php if (!empty($error->queryString)) : ?>
    <p class="notice">
        <strong>SQL Query: </strong>
        <?= h($error->queryString) ?>
    </p>
<?php endif; ?>
<?php if (!empty($error->params)) : ?>
        <strong>SQL Query Params: </strong>
        <?php Debugger::dump($error->params) ?>
<?php endif; ?>
<?php if ($error instanceof Error) : ?>
        <strong>Error in: </strong>
        <?= sprintf('%s, line %s', str_replace(ROOT, 'ROOT', $error->getFile()), $error->getLine()) ?>
<?php endif; ?>
<?php
    echo $this->element('auto_table_warning');

    if (extension_loaded('xdebug')) :
        xdebug_print_function_stack();
    endif;

    $this->end();
endif;
?>
<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="box box-danger box-solid">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    <?= __d('cake', 'Error') ?> <?= h($code) ?>: <?= h($message) ?>
                </h3>
            </div>
            <div class="box-body">
                <?= __d('Qobo/ProjectTemplateCakephp', 'There was a problem processing your request.  Please notify your system administrator.'); ?>
            </div>
            <div class="box-footer">
                <b>URL: </b><?= h(Router::url($url, true)) ?>
            </div>
        </div>
    </div>
</div>
