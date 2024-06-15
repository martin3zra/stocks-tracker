<?php

use App\Actions\UserNotification;
use App\Services\Listener;
use DI\ContainerBuilder;
use PhpAmqpLib\Channel\AMQPChannel;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

$builder = new ContainerBuilder();

$services = require_once __DIR__ . '/../public/services.php';
$services($builder);
$container = $builder->build();

$listener = new Listener(
    $container->get(AMQPChannel::class),
    $container->get(UserNotification::class),
    new ConsoleOutput(),
);
$listener->listen();
