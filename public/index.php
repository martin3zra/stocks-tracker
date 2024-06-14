<?php

use App\Actions\UserNotifier;
use App\Services\StockClient;
use DI\Container;
use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;

require __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/../.env');

$ENV = $_ENV['ENV'] ?? 'dev';

$containerBuilder = new ContainerBuilder();
$container = $containerBuilder->build();

$container->set('mailer', function (ContainerInterface $containter) {

    $transport = Transport::fromDsn($_ENV['MAILER_DSN']);

    return new Mailer($transport);
});

$container->set('stockClient', function(ContainerInterface $container) {
    return new StockClient();
});

$container->set('notifier', function(ContainerInterface $container) {
    return new UserNotifier($container->get('mailer'));
});

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
