<?php
use Cake\Event\Event;
use Cake\I18n\Time;
use CsvMigrations\FieldHandlers\FieldHandlerFactory;

$factory = new FieldHandlerFactory($this);

$tableName = $this->name;
if ($this->plugin) {
    $tableName = $this->plugin . '.' . $this->name;
}

$oldUser = null;
$oldDate = null;
$dateColors = [
    'red',
    'green'
];
$iconColors = [
    'light-blue',
    'navy',
    'blue',
    'aqua'
];
?>
<section class="content-header">
    <div class="row">
        <div class="col-xs-12 col-md-6">
            <h4>
            <?= __('Changelog')?> &raquo; <?= $this->Html->link(
            $entity->{$displayField},
            ['plugin' => $this->plugin, 'controller' => $this->name, 'action' => 'view', $entity->id],
            ['escape' => false]
            ); ?>
            </h4>
        </div>
        <div class="col-xs-12 col-md-6">
            <div class="pull-right">
            </div>
        </div>
    </div>
</section>
<section class="content">
<div class="row">
    <div class="col-xs-12">
        <ul class="timeline">
<?php foreach ($changelog as $record) : ?>
    <?php
    $meta = json_decode($record->meta);
    if (empty($meta)) {
        $meta = new StdClass();
    }
    $date = $record->timestamp->i18nFormat('d MMM. YYY');

    $url = '#';
    $username = __('Unknown');
    if (isset($meta->user)) {
        $user = $usersTable->findById($meta->user)->first();
        $username = empty($user) ? $meta->user : $user->name;
        $url = $this->Url->build([
            'plugin' => 'CakeDC/Users',
            'controller' => 'Users',
            'action' => 'view',
            $meta->user
        ]);
    }
    ?>
    <?php if ($username !== $oldUser || $date !== $oldDate) : ?>
        <li class="time-label"><span class="bg-<?= current($dateColors) ?>"><?= $date ?></span></li>
        <?php
        reset($iconColors);
        next($dateColors);
        if (!current($dateColors)) {
            reset($dateColors);
        }
        ?>
    <?php endif; ?>
        <li>
            <i class="fa fa-book bg-<?= current($iconColors) ?>"></i>
            <?php
            next($iconColors);
            if (!current($iconColors)) {
                reset($iconColors);
            } ?>
            <div class="timeline-item">
                <span class="time"><i class="fa fa-clock-o"></i>
                    <?= $record->timestamp->timeAgoInWords([
                        'format' => 'MMM d, YYY | HH:mm:ss',
                        'end' => '1 month'
                    ]) ?>
                </span>
                <h3 class="timeline-header">
                    <a href="<?= $url ?>"><?= $username; ?></a> made the following changes:
                </h3>
                <div class="timeline-body">
                    <table class="table table-hover table-condensed table-vertical-align">
                        <thead>
                            <tr>
                                <th class="col-xs-2">Field</th>
                                <th class="col-xs-5">Old Value</th>
                                <th class="col-xs-5">New Value</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php
    $changed = json_decode($record->changed);
    $original = json_decode($record->original);
    ?>
    <?php foreach ($changed as $k => $v) : ?>
    <?php
    $old = '';
    if ($original !== null && isset($original->{$k})) {
        if ($original->{$k} !== $v) {
            $old = $original->{$k};
        }
    }
    ?>
    <tr>
        <td><?= $factory->renderName($tableName, $k) ?></td>
        <td><?= $factory->renderValue($tableName, $k, $old) ?></td>
        <?php
        if (is_object($v)) {
            if (!empty($v->date) && !empty($v->timezone)) {
                $v = new Time($v->date, $v->timezone);
            } else {
                $v = __('Unknown value');
            }
        }
        ?>
        <td><?= $factory->renderValue($tableName, $k, $v) ?></td>
    </tr>
    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </li>
<?php
$oldUser = $username;
$oldDate = $date;
?>
<?php endforeach; ?>
        </ul>
    </div>
</div>
