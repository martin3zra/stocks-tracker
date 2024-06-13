<?php

use \Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$config = [
    'driver' => 'mysql',
    'host' => getenv('DATABASE_HOST'),
    'port' => getenv('DATABASE_PORT'),
    'database' => 'stocks',
    'username' => 'root',
    'password' => 'secret',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
    'unix_socket' => '',
];

if (RUNNING_IN_CONSOLE == 1) {
    $config['unix_socket'] = '';
    $config['host'] = getenv('DATABASE_HOST');
} else {
    // When running the API remove port from the connection
    // Bc is been running inside the container
    unset($config['port']);
}

$capsule->addConnection($config);

$capsule->setAsGlobal();

$capsule->bootEloquent();
