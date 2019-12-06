<?php
use Cake\Core\Configure;

$size = $size ?? 'large';
?>
<img src="<?= $this->Url->image(Configure::read('Theme.logo.' . $size)) ?>" alt="<?= __d('Qobo/ProjectTemplateCakephp', 'Site Logo') ?>" height="50" />
