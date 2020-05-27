<?php
namespace App\Module;

use Qobo\Utils\Module\Module;

final class ScheduledJobLogsModule extends Module
{
    protected $config = array (
  'table' => 
  array (
    'icon' => 'clock-o',
    'searchable' => false,
    'display_field' => 'context',
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
  'sheduled_job_id' => 
  array (
    'name' => 'scheduled_job_id',
    'type' => 'related(ScheduledJobs)',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'context' => 
  array (
    'name' => 'context',
    'type' => 'string',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'extra' => 
  array (
    'name' => 'extra',
    'type' => 'text',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'status' => 
  array (
    'name' => 'status',
    'type' => 'boolean',
    'required' => false,
    'non-searchable' => false,
    'unique' => false,
  ),
  'datetime' => 
  array (
    'name' => 'datetime',
    'type' => 'datetime',
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
);

    protected $lists = array (
);

    protected $fields = array (
  'datetime' => 
  array (
    'renderAs' => 'datetime',
  ),
  'created' => 
  array (
    'renderAs' => 'datetime',
  ),
);

    protected $menus = array (
);

    protected $views = array (
  'index' => 
  array (
    0 => 
    array (
      0 => 'context',
    ),
    1 => 
    array (
      0 => 'datetime',
    ),
    2 => 
    array (
      0 => 'status',
    ),
    3 => 
    array (
      0 => 'created',
    ),
  ),
  'view' => 
  array (
    0 => 
    array (
      0 => 'Details',
      1 => 'context',
      2 => 'status',
    ),
    1 => 
    array (
      0 => 'Details',
      1 => 'datetime',
      2 => 'created',
    ),
    2 => 
    array (
      0 => 'Response',
      1 => 'extra',
    ),
  ),
);
}
