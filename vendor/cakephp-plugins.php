<?php
$baseDir = dirname(dirname(__FILE__));
return [
    'plugins' => [
        'Bake' => $baseDir . '/vendor/cakephp/bake/',
        'BootstrapUI' => $baseDir . '/vendor/friendsofcake/bootstrap-ui/',
        'CakeDC' => $baseDir . '/plugins/CakeDC/',
        'CakeDC/Auth' => $baseDir . '/vendor/cakedc/auth/',
        'CakeDC/Users' => $baseDir . '/vendor/cakedc/users/',
        'DebugKit' => $baseDir . '/vendor/cakephp/debug_kit/',
        'Gentelella' => $baseDir . '/vendor/backstageel/cakephp-gentelella-theme/',
        'Migrations' => $baseDir . '/vendor/cakephp/migrations/'
    ]
];