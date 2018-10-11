<?php
/**
 * AdminLTE plugin configuration
 *
 * Note: in case you want to use custom CSS skin,
 * you should set `skin` as `custom`
 * and defined `skinUrl` as `skins/skin-custom`
 * so it would be loaded locally.
 */

// create logo HTML img tags
$logo = '<img src="/img/logo.png" alt="Site Logo" height="50" />';
$logoMini = '<img src="/img/logo-mini.png" alt="Site Logo"height="50" />';

return [
    'Theme' => [
        'folder' => ROOT,
        'title' => getenv('PROJECT_NAME'),
        'logo' => [
            'mini' => $logoMini,
            'large' => $logo,
        ],
        'login' => [
            'show_remember' => true,
            'show_register' => false,
            'show_social' => false,
        ],
        'skin' => 'blue',
        'backgroundImages' => 'qobo',
        'prependAvatars' => true,
    ],
];
