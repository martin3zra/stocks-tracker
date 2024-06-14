<?php

use \Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$config = [
    'driver' => $_ENV['DATABASE_DRIVER'],
    'host' => $_ENV['DATABASE_HOST'],
    'port' => $_ENV['DATABASE_PORT'],
    'database' => $_ENV['DATABASE_NAME'],
    'username' => $_ENV['DATABASE_USERNAME'],
    'password' => $_ENV['DATABASE_PASSWORD'],
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
    'unix_socket' => '',
];

if (RUNNING_IN_CONSOLE == 1) {
    $config['unix_socket'] = '';
    $config['host'] = $_ENV['CONSOLE_DATABASE_HOST'];
} else {
    // When running the API remove port from the connection
    // Bc is been running inside the container
    unset($config['port']);
}

$capsule->addConnection($config);

$capsule->setAsGlobal();

$capsule->bootEloquent();
