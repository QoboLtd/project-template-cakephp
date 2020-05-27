<?php
namespace App\Module;

use Qobo\Utils\Module\Module;

final class UsersModule extends Module
{
    protected $config = array (
  'table' => 
  array (
    'icon' => 'user',
    'lookup_fields' => 
    array (
      0 => 'email',
      1 => 'username',
    ),
    'display_field' => 'name',
    'typeahead_fields' => 
    array (
      0 => 'first_name',
      1 => 'last_name',
      2 => 'username',
      3 => 'email',
    ),
    'searchable' => true,
    'basic_search_fields' => 
    array (
    ),
    'allow_reminders' => 
    array (
    ),
    'translatable' => false,
    'permissions_parent_modules' => 
    array (
    ),
  ),
  'virtualFields' => 
  array (
    'name' => 
    array (
      0 => 'first_name',
      1 => 'last_name',
    ),
  ),
  'associations' => 
  array (
    'hide_associations' => 
    array (
    ),
  ),
  'associationLabels' => 
  array (
  ),
  'notifications' => 
  array (
    'enable' => false,
    'ignored_fields' => 
    array (
    ),
  ),
  'manyToMany' => 
  array (
    'modules' => 
    array (
    ),
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
      'label' => 'Access Control',
      'desc' => 'Manage access control',
      'url' => '#',
      'icon' => 'lock',
      'order' => 10,
      'children' => 
      array (
        0 => 
        array (
          'label' => 'Users',
          'desc' => 'Manage system users',
          'url' => '/users/',
          'order' => 10,
        ),
      ),
    ),
  ),
);

    protected $views = array (
);
}
