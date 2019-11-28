<?php
use Cake\Core\Configure;

$size = $size ?? 'large';
?>
<img src="<?= $this->Url->image(Configure::read('Theme.logo.' . $size)) ?>" alt="<?= __('Site Logo') ?>" height="50" />
