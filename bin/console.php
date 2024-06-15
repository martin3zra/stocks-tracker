<?php

use App\Console\MigrationCommand;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

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

