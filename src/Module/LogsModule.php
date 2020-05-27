<?php
namespace App\Module;

use Qobo\Utils\Module\Module;

final class LogsModule extends Module
{
    protected $config = array (
  'table' => 
  array (
    'icon' => 'file-code-o',
    'display_field' => 'type',
    'searchable' => true,
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
      0 => 'hostname',
    ),
    1 => 
    array (
      0 => 'type',
    ),
    2 => 
    array (
      0 => 'ip',
    ),
    3 => 
    array (
      0 => 'message',
    ),
    4 => 
    array (
      0 => 'uri',
    ),
    5 => 
    array (
      0 => 'created',
    ),
  ),
);
}
