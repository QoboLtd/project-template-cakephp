<?php
namespace App\Module;

use Qobo\Utils\Module\Module;

final class FileStorageModule extends Module
{
    protected $config = array (
  'table' => 
  array (
    'icon' => 'file',
    'display_field' => 'filename',
    'searchable' => true,
    'lookup_fields' => 
    array (
    ),
    'typeahead_fields' => 
    array (
    ),
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
  'notifications' => 
  array (
    'enable' => true,
    'ignored_fields' => 
    array (
    ),
  ),
  'virtualFields' => 
  array (
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
);

    protected $views = array (
  'index' => 
  array (
    0 => 
    array (
      0 => 'filename',
    ),
    1 => 
    array (
      0 => 'model',
    ),
    2 => 
    array (
      0 => 'model_field',
    ),
  ),
);
}
