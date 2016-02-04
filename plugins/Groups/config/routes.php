<?php
use Cake\Routing\Router;

Router::plugin(
    'Groups',
    ['path' => '/groups'],
    function ($routes) {
        $routes->fallbacks('DashedRoute');
    }
);
