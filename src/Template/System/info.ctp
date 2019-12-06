<?php
use Cake\Core\Configure;

echo $this->Html->scriptBlock('var api_token="' . Configure::read('API.token') . '";', ['block' => 'scriptBottom']);
?>

<section class="content-header">
    <h1><?= __d('Qobo/ProjectTemplateCakephp', 'System Information'); ?></h1>
</section>
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs system-info">
                    <?php
                        $first = true;
                        foreach ($tabs as $tab => $settings) {
                           echo $first ? '<li class="active">' : '<li>';
                           echo '<a href="#' . $tab . '" data-tab="' . $tab . '" data-toggle="tab" aria-expanded="true">';
                           echo '<i class="fa ' . $settings['icon'] . '"></i>';
                           echo ' ';
                           echo $settings['label'];
                           echo '</a>';
                           echo '</li>';
                           $first = false;
                        }
                    ?>
                </ul>
                <div class="tab-content">
                    <div id="spinner-system-info" style="height:150px;padding-top:60px;display:none;">
                        <p class="lead text-center"><i class="fa fa-refresh fa-spin fa-fw"></i> Processing..</p>
                    </div>
                    <?php
                        $first = true;
                        foreach ($tabs as $tab => $settings) {
                            echo $first ? '<div class="tab-pane active" id="' . $tab . '">' : '<div class="tab-pane" id="' . $tab . '">';
                            echo '</div>';
                            $first = false;
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
