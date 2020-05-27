<?php
namespace App\Module;

use Qobo\Utils\Module\Module;

final class ScheduledJobsModule extends Module
{
    protected $config = array (
  'table' => 
  array (
    'icon' => 'clock-o',
    'searchable' => true,
    'display_field' => 'name',
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
  'active' => 
  array (
    'renderAs' => 'booleanYesNo',
  ),
  'created' => 
  array (
    'renderAs' => 'datetime',
  ),
  'modified' => 
  array (
    'renderAs' => 'datetime',
  ),
  'start_date' => 
  array (
    'renderAs' => 'datetime',
  ),
  'end_date' => 
  array (
    'renderAs' => 'datetime',
  ),
  'last_run_date' => 
  array (
    'renderAs' => 'datetime',
  ),
);

    protected $menus = array (
  'admin_menu' => 
  array (
    0 => 
    array (
      'label' => 'Settings',
      'desc' => 'Manage settings',
      'url' => '#',
      'icon' => 'cog',
      'order' => 20,
      'children' => 
      array (
        0 => 
        array (
          'label' => 'Scheduled Jobs',
          'desc' => 'Automated Scripts execution',
          'url' => '/scheduled-jobs/',
          'order' => 100,
        ),
      ),
    ),
  ),
);

    protected $views = array (
  'edit' => 
  array (
    0 => 
    array (
      0 => 'Details',
      1 => 'name',
      2 => 'active',
    ),
    1 => 
    array (
      0 => 'Details',
      1 => 'job',
      2 => 'priority',
    ),
    2 => 
    array (
      0 => 'Details',
      1 => 'options',
    ),
    3 => 
    array (
      0 => 'Recurrence',
      1 => 'start_date',
      2 => 'end_date',
    ),
    4 => 
    array (
      0 => 'Recurrence',
      1 => 'recurrence',
    ),
  ),
  'index' => 
  array (
    0 => 
    array (
      0 => 'name',
    ),
    1 => 
    array (
      0 => 'active',
    ),
    2 => 
    array (
      0 => 'priority',
    ),
    3 => 
    array (
      0 => 'start_date',
    ),
    4 => 
    array (
      0 => 'end_date',
    ),
    5 => 
    array (
      0 => 'last_run_date',
    ),
  ),
  'add' => 
  array (
    0 => 
    array (
      0 => 'Details',
      1 => 'name',
      2 => 'active',
    ),
    1 => 
    array (
      0 => 'Details',
      1 => 'job',
      2 => 'priority',
    ),
    2 => 
    array (
      0 => 'Details',
      1 => 'options',
    ),
    3 => 
    array (
      0 => 'Recurrence',
      1 => 'start_date',
      2 => 'end_date',
    ),
    4 => 
    array (
      0 => 'Recurrence',
      1 => 'recurrence',
    ),
  ),
  'view' => 
  array (
    0 => 
    array (
      0 => 'Details',
      1 => 'name',
      2 => 'active',
    ),
    1 => 
    array (
      0 => 'Command',
      1 => 'job',
      2 => 'options',
    ),
    2 => 
    array (
      0 => 'Recurrence',
      1 => 'recurrence',
      2 => 'last_run_date',
    ),
    3 => 
    array (
      0 => 'Recurrence',
      1 => 'start_date',
      2 => 'end_date',
    ),
  ),
);
}
