<?php

use DI\Container;
use DI\ContainerBuilder;

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// $builder = new ContainerBuilder();
// $builder->addDefinitions();
// $container = $builder->build();

// $app = AppFactory::create($container);
$app = AppFactory::create();

$app->addRoutingMiddleware();
// Parse json, form data and xml
$app->addBodyParsingMiddleware();

$app->addErrorMiddleware(true, true, true);

require './bootstrap.php';

$app->run();
