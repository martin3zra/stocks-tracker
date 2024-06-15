<?php

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Symfony\Component\Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

$ENV = $_ENV['ENV'] ?? 'dev';

$builder = new ContainerBuilder();

$services = require __DIR__ . '/../public/services.php';
$services($builder);

$container = $builder->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->addRoutingMiddleware();

// Parse json, form data and xml
$app->addBodyParsingMiddleware();

$displayErrorDetails = $ENV == 'dev';
$errorMiddleware = $app->addErrorMiddleware($displayErrorDetails, true, true);

// Error Handler
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');

require './bootstrap.php';

$app->run();
