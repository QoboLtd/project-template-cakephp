<?php
/**
 * Copyright (c) Qobo Ltd. (https://www.qobo.biz)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Qobo Ltd. (https://www.qobo.biz)
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
?>
<section class="content-header">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h4><?= $this->Html->link(
                __d('Qobo/ProjectTemplateCakephp', 'Groups'),
                ['plugin' => 'Groups', 'controller' => 'Groups', 'action' => 'index']
            ) . ' &raquo; ' . h($group->name) ?></h4>
        </div>
    </div>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-6">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <i class="fa fa-users"></i>

                    <h3 class="box-title">Details</h3>
                </div>
                <div class="box-body">
                    <dl class="dl-horizontal">
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Name') ?></dt>
                        <dd><?= h($group->name) ?></dd>
                        <dt><?= __d('Qobo/ProjectTemplateCakephp', 'Description') ?></dt>
                        <dd><?= h($group->description) ?></dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
    <hr />
    <div class="row">
        <div class="col-xs-12">
            <div class="nav-tabs-custom">
                <ul id="relatedTabs" class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#users" aria-controls="users" role="tab" data-toggle="tab">
                            <?= __d('Qobo/ProjectTemplateCakephp', 'Users'); ?>
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="users">
                        <?php if (!empty($group->users)) : ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-condensed table-vertical-align">
                                <thead>
                                    <tr>
                                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'Username') ?></th>
                                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'First Name') ?></th>
                                        <th><?= __d('Qobo/ProjectTemplateCakephp', 'Last Name') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($group->users as $users) : ?>
                                    <tr>
                                        <td><?= $this->Html->link($users->username, [
                                            'plugin' => false,
                                            'controller' => 'Users',
                                            'action' => 'view',
                                            $users->id
                                        ]) ?>
                                        </td>
                                        <td><?= h($users->first_name) ?></td>
                                        <td><?= h($users->last_name) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
