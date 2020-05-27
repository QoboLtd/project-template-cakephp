<?php
namespace App\Module;

use Qobo\Utils\Module\Module;

final class SettingsModule extends Module
{
    protected $config = array (
  'table' => 
  array (
    'icon' => 'wrench',
    'searchable' => false,
    'display_field' => '',
  ),
);

    protected $migration = array (
);

    protected $lists = array (
);

    protected $fields = array (
);

    protected $menus = array (
  'admin_menu' => 
  array (
    0 => 
    array (
      'label' => 'Settings',
      'desc' => 'Manage settings',
      'url' => '#',
      'icon' => 'wrench',
      'order' => 100,
      'children' => 
      array (
        0 => 
        array (
          'label' => 'System Settings',
          'desc' => 'Manage system settings',
          'url' => '/settings/app',
          'order' => 65,
        ),
      ),
    ),
  ),
);

    protected $views = array (
  'edit' => 
  array (
  ),
  'index' => 
  array (
  ),
  'add' => 
  array (
  ),
  'view' => 
  array (
  ),
);
}
