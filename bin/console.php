<?php

use App\Console\MigrationCommand;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;

define('RUNNING_IN_CONSOLE', 1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../database/database.php';

/** @var ContainerInterface $container */
$container = (new ContainerBuilder())->build();

try {

    /** @var Application $application */
    $application = $container->get(Application::class);

    $application->add($container->get(MigrationCommand::class));

    exit($application->run());
} catch (Throwable $exception) {
    echo $exception->getMessage();
    exit(1);
}

