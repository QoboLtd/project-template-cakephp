<?php
// DB settings
return [
  'Settings' => [
    'First Tab' => [
       'icon' => 'trash',
       'zero line' => [
         'first Section' => [
           'field_number_one' => [
             'alias' => 'ScheduledLog.stats.age',
             'type' => 'string',
             'help' => 'This is very helpful',
           ],
         ],
       ],
       'first line' => [
         'first Section' => [
           'i_need_more_coffee' => [
             'alias' => 'EmailTransport.default.port',
             'type' => 'integer',
           ],
         ],
       ],
       'second line' => [
         'secondsection' => [
           'my_birthday' => [
             'alias' => 'App.fullBaseUrl',
             'type' => 'string',
           ],
           'more_rock' => [
             'alias' => 'App.wwwRoot',
             'type' => 'string',
           ],
         ],
       ],
       'third line' => [
         'secondsection' => [
           'i_want_to_write_more' => [
             'alias' => 'Icons.url',
             'type' => 'text',
           ],
           'the_answer_is_42' => [
             'alias' => 'Menu.allControllers',
             'type' => 'boolean',
           ],
         ],
       ],
     ],
     'Avatar' => [
       'Column 1' => [
         'This section 1' => [
           'name1' => [
             'alias' => 'debug',
             'type' => 'string',
           ],
         ],
       ],
       'Column 2' => [
         'This section 2' => [
           'name1' => [
             'alias' => 'SystemInfo.tabs.project.label',
             'type' => 'string',
           ],
           'name2' => [
             'alias' => 'SystemInfo.tabs.project.icon',
             'type' => 'string',
           ],
         ],
       ],
     ],
     'FileStorage' => [
       'Another Column' => [
         'This section 1' => [
           'name2' => [
             'alias' => 'FileStorage.defaultImageSize',
             'type' => 'string',
           ],
         ],
       ],
     ],
   ]
 ];
