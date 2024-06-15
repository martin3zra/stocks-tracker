<?php

use \Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$config = [
    'driver' => $_ENV['DATABASE_DRIVER'],
    'host' => $_ENV['DATABASE_HOST'],
    'database' => $_ENV['DATABASE_NAME'],
    'username' => $_ENV['DATABASE_USERNAME'],
    'password' => $_ENV['DATABASE_PASSWORD'],
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
    'unix_socket' => '',
];

$capsule->addConnection($config);

// Make it global, so we can use it on the migration symfony command
$capsule->setAsGlobal();

$capsule->bootEloquent();
