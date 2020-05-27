<?php
namespace App\Module;

use Qobo\Utils\Module\Module;

final class ThingsModule extends Module
{
    protected $config = array (
  'table' => 
  array (
    'icon' => 'user',
    'searchable' => true,
    'display_field' => 'name',
    'typeahead_fields' => 
    array (
    ),
    'basic_search_fields' => 
    array (
    ),
    'lookup_fields' => 
    array (
    ),
    'allow_reminders' => 
    array (
      0 => 'Users',
    ),
    'translatable' => true,
    'permissions_parent_modules' => 
    array (
    ),
    'type' => 'module',
  ),
  'panels' => 
  array (
    'Dynamic panel - checkbox checked' => '(%%vip%%==1)',
    'Dynamic panel - checkbox unchecked' => '(%%vip%%==0)',
    'Dynamic panel - male' => '(%%gender%%==\'m\')',
    'Dynamic panel - female' => '(%%gender%%==\'f\')',
  ),
  'virtualFields' => 
  array (
  ),
  'notifications' => 
  array (
    'enable' => true,
    'ignored_fields' => 
    array (
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
);

    protected $migration = array (
  'id' => 
  array (
    'name' => 'id',
    'type' => 'uuid',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'name' => 
  array (
    'name' => 'name',
    'type' => 'string',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'description' => 
  array (
    'name' => 'description',
    'type' => 'text',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'country' => 
  array (
    'name' => 'country',
    'type' => 'country(Common.countries)',
    'required' => true,
    'non-searchable' => false,
    'unique' => false,
  ),
  'currency' => 
  array (
    'name' => 'currency',
    'type' => 'currency(currencies)',
    'required' => true,
    'non-searchable' => false,
    'unique' => false,
  ),
  'gender' => 
  array (
    'name' => 'gender',
    'type' => 'list(Common.genders)',
    'required' => true,
    'non-searchable' => false,
    'unique' => false,
  ),
  'assigned_to' => 
  array (
    'name' => 'assigned_to',
    'type' => 'related(Users)',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'primary_thing' => 
  array (
    'name' => 'primary_thing',
    'type' => 'related(Things)',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'salary' => 
  array (
    'name' => 'salary',
    'type' => 'money(currencies)',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'vip' => 
  array (
    'name' => 'vip',
    'type' => 'boolean',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'area' => 
  array (
    'name' => 'area',
    'type' => 'metric(units_area)',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'date_of_birth' => 
  array (
    'name' => 'date_of_birth',
    'type' => 'date',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'work_start' => 
  array (
    'name' => 'work_start',
    'type' => 'time',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'website' => 
  array (
    'name' => 'website',
    'type' => 'url',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'bio' => 
  array (
    'name' => 'bio',
    'type' => 'blob',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'level' => 
  array (
    'name' => 'level',
    'type' => 'integer',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'appointment' => 
  array (
    'name' => 'appointment',
    'type' => 'reminder',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'phone' => 
  array (
    'name' => 'phone',
    'type' => 'phone',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'email' => 
  array (
    'name' => 'email',
    'type' => 'email',
    'required' => true,
    'non-searchable' => false,
    'unique' => true,
  ),
  'rate' => 
  array (
    'name' => 'rate',
    'type' => 'decimal',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'title' => 
  array (
    'name' => 'title',
    'type' => 'list(Common.titles)',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'language' => 
  array (
    'name' => 'language',
    'type' => 'list(Common.languages)',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'photos' => 
  array (
    'name' => 'photos',
    'type' => 'files',
    'required' => false,
    'non-searchable' => true,
    'unique' => false,
  ),
  'file' => 
  array (
    'name' => 'file',
    'type' => 'files',
    'required' => false,
    'non-searchable' => true,
    'unique' => false,
  ),
  'test_list' => 
  array (
    'name' => 'test_list',
    'type' => 'sublist(Things.test_list)',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'testmetric' => 
  array (
    'name' => 'testmetric',
    'type' => 'metric(units_area)',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'testmoney' => 
  array (
    'name' => 'testmoney',
    'type' => 'money(currencies)',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'created' => 
  array (
    'name' => 'created',
    'type' => 'datetime',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'modified' => 
  array (
    'name' => 'modified',
    'type' => 'datetime',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'created_by' => 
  array (
    'name' => 'created_by',
    'type' => 'related(Users)',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'modified_by' => 
  array (
    'name' => 'modified_by',
    'type' => 'related(Users)',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'non_searchable' => 
  array (
    'name' => 'non_searchable',
    'type' => 'string',
    'required' => false,
    'non-searchable' => true,
    'unique' => false,
  ),
  'sample_date' => 
  array (
    'name' => 'sample_date',
    'type' => 'datetime',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
);

    protected $lists = array (
  'test_list' => 
  array (
    'first' => 
    array (
      'label' => 'first',
      'inactive' => false,
      'children' => 
      array (
        'first.first_children' => 
        array (
          'label' => 'first children',
          'inactive' => false,
        ),
        'first.second_children' => 
        array (
          'label' => 'second children',
          'inactive' => false,
        ),
      ),
    ),
    'second' => 
    array (
      'label' => 'second',
      'inactive' => false,
    ),
    'third' => 
    array (
      'label' => 'third',
      'inactive' => true,
    ),
  ),
);

    protected $fields = array (
  'name' => 
  array (
    'label' => 'label name',
    'personal' => true,
    'placeholder' => 'The name - without help tooltip',
  ),
  'description' => 
  array (
    'label' => 'label description',
    'translatable' => true,
    'help' => 'LONG TEST Help - Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras cursus congue mi vitae semper. Proin vitae eros quis ipsum ornare sagittis efficitur ac odio. Vivamus iaculis, turpis in tempor hendrerit, neque mi dapibus sem, sed vulputate arcu nisi sed nibh. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nulla tempor condimentum quam. Quisque iaculis dui ac ipsum tempus semper. Vivamus interdum auctor dolor, sit amet pharetra est porta ac. Donec sit amet elit quam.',
  ),
  'appointment' => 
  array (
    'help' => 'Help Appointment',
  ),
  'area' => 
  array (
    'help' => 'Help Area',
  ),
  'assigned_to' => 
  array (
    'help' => 'Help Assigned To',
  ),
  'bio' => 
  array (
    'help' => 'Help Bio',
  ),
  'country' => 
  array (
    'help' => 'Help Country',
  ),
  'currency' => 
  array (
    'help' => 'Help Currency',
  ),
  'date_of_birth' => 
  array (
    'help' => 'Help Date Of Birth',
  ),
  'email' => 
  array (
    'help' => 'Help Email',
  ),
  'file' => 
  array (
    'help' => 'Help File',
  ),
  'gender' => 
  array (
    'help' => 'Help Gender',
  ),
  'language' => 
  array (
    'help' => 'Help Language',
  ),
  'level' => 
  array (
    'help' => 'Help Level',
  ),
  'non_searchable' => 
  array (
    'help' => 'Help Non Searchable',
  ),
  'phone' => 
  array (
    'help' => 'Help Phone',
  ),
  'photos' => 
  array (
    'help' => 'Help Photos',
  ),
  'primary_thing' => 
  array (
    'help' => 'Help Primary Thing',
  ),
  'rate' => 
  array (
    'help' => 'Help Rate',
  ),
  'salary' => 
  array (
    'help' => 'Help Salary',
  ),
  'sample_date' => 
  array (
    'help' => 'Help Sample Date',
  ),
  'test_list' => 
  array (
    'help' => 'Help Test List',
  ),
  'testmetric' => 
  array (
    'help' => 'Help Testmetric',
  ),
  'testmoney' => 
  array (
    'help' => 'Help Testmoney',
  ),
  'title' => 
  array (
    'help' => 'Help Title',
  ),
  'vip' => 
  array (
    'help' => 'Help Vip',
  ),
  'website' => 
  array (
    'help' => 'Help Website',
  ),
  'work_start' => 
  array (
    'help' => 'Help Work Start',
  ),
);

    protected $menus = array (
  'main_menu' => 
  array (
    0 => 
    array (
      'label' => 'Things',
      'url' => '/things/',
      'order' => 40,
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
      2 => 'gender',
      3 => 'assigned_to',
    ),
    1 => 
    array (
      0 => 'Details',
      1 => 'description',
      2 => 'country',
      3 => 'primary_thing',
    ),
    2 => 
    array (
      0 => 'Details',
      1 => 'salary',
      2 => 'vip',
      3 => 'area',
      4 => 'date_of_birth',
    ),
    3 => 
    array (
      0 => 'Details',
      1 => 'work_start',
      2 => 'website',
      3 => 'bio',
      4 => 'level',
    ),
    4 => 
    array (
      0 => 'Details',
      1 => 'appointment',
      2 => 'phone',
      3 => 'email',
      4 => 'rate',
    ),
    5 => 
    array (
      0 => 'Details',
      1 => 'language',
      2 => 'title',
      3 => 'photos',
      4 => 'file',
    ),
    6 => 
    array (
      0 => 'Details',
      1 => 'test_list',
      2 => 'testmetric',
      3 => 'testmoney',
    ),
    7 => 
    array (
      0 => 'Details',
      1 => 'sample_date',
      2 => 'currency',
      3 => '',
      4 => '',
    ),
    8 => 
    array (
      0 => 'Dynamic panel - male',
      1 => '',
    ),
    9 => 
    array (
      0 => 'Dynamic panel - female',
      1 => '',
    ),
    10 => 
    array (
      0 => 'Dynamic panel - checkbox checked',
      1 => '',
    ),
    11 => 
    array (
      0 => 'Dynamic panel - checkbox unchecked',
      1 => '',
    ),
  ),
  'index' => 
  array (
    0 => 
    array (
      0 => 'id',
    ),
    1 => 
    array (
      0 => 'name',
    ),
    2 => 
    array (
      0 => 'gender',
    ),
    3 => 
    array (
      0 => 'assigned_to',
    ),
    4 => 
    array (
      0 => 'created',
    ),
    5 => 
    array (
      0 => 'modified',
    ),
  ),
  'add' => 
  array (
    0 => 
    array (
      0 => 'Details',
      1 => 'name',
      2 => 'gender',
      3 => 'assigned_to',
    ),
    1 => 
    array (
      0 => 'Details',
      1 => 'description',
      2 => 'country',
      3 => 'primary_thing',
    ),
    2 => 
    array (
      0 => 'Details',
      1 => 'salary',
      2 => 'vip',
      3 => 'area',
      4 => 'date_of_birth',
    ),
    3 => 
    array (
      0 => 'Details',
      1 => 'work_start',
      2 => 'website',
      3 => 'bio',
      4 => 'level',
    ),
    4 => 
    array (
      0 => 'Details',
      1 => 'appointment',
      2 => 'phone',
      3 => 'email',
      4 => 'rate',
    ),
    5 => 
    array (
      0 => 'Details',
      1 => 'language',
      2 => 'title',
      3 => 'photos',
      4 => 'file',
    ),
    6 => 
    array (
      0 => 'Details',
      1 => 'test_list',
      2 => 'testmetric',
      3 => 'testmoney',
    ),
    7 => 
    array (
      0 => 'Details',
      1 => 'sample_date',
      2 => 'currency',
      3 => '',
      4 => '',
    ),
    8 => 
    array (
      0 => 'Dynamic panel - male',
      1 => '',
    ),
    9 => 
    array (
      0 => 'Dynamic panel - female',
      1 => '',
    ),
    10 => 
    array (
      0 => 'Dynamic panel - checkbox checked',
      1 => '',
    ),
    11 => 
    array (
      0 => 'Dynamic panel - checkbox unchecked',
      1 => '',
    ),
  ),
  'view' => 
  array (
    0 => 
    array (
      0 => 'Details',
      1 => 'name',
      2 => 'gender',
      3 => 'assigned_to',
    ),
    1 => 
    array (
      0 => 'Details',
      1 => 'description',
      2 => 'country',
      3 => 'primary_thing',
    ),
    2 => 
    array (
      0 => 'Details',
      1 => 'salary',
      2 => 'vip',
      3 => 'area',
      4 => 'date_of_birth',
    ),
    3 => 
    array (
      0 => 'Details',
      1 => 'work_start',
      2 => 'website',
      3 => 'bio',
      4 => 'level',
    ),
    4 => 
    array (
      0 => 'Details',
      1 => 'appointment',
      2 => 'phone',
      3 => 'email',
      4 => 'rate',
    ),
    5 => 
    array (
      0 => 'Details',
      1 => 'language',
      2 => 'title',
      3 => 'photos',
      4 => 'file',
    ),
    6 => 
    array (
      0 => 'Details',
      1 => 'test_list',
      2 => 'testmetric',
      3 => 'testmoney',
    ),
    7 => 
    array (
      0 => 'Details',
      1 => 'created_by',
      2 => 'created',
      3 => 'modified_by',
      4 => 'modified',
    ),
    8 => 
    array (
      0 => 'Details',
      1 => 'sample_date',
      2 => 'currency',
      3 => '',
      4 => '',
    ),
    9 => 
    array (
      0 => 'Dynamic panel - male',
      1 => '',
    ),
    10 => 
    array (
      0 => 'Dynamic panel - female',
      1 => '',
    ),
    11 => 
    array (
      0 => 'Dynamic panel - checkbox checked',
      1 => '',
    ),
    12 => 
    array (
      0 => 'Dynamic panel - checkbox unchecked',
      1 => '',
    ),
  ),
);
}
