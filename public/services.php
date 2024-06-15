<?php

declare(strict_types=1);

use App\Actions\UserNotification;
use App\Services\Dispatcher;
use App\Services\StockClient;
use App\Services\StockClientContract;
use App\Services\TokenizerContract;
use App\Services\TokenService;
use DI\ContainerBuilder;
use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;

return function(ContainerBuilder $builder) {
    $mailer = function() {
        $transport = Transport::fromDsn($_ENV['MAILER_DSN']);
        return new Mailer($transport);
    };

    $queueChannel = function() {
        $connection = new AMQPStreamConnection(
            $_ENV['RMQ_HOST'],
            $_ENV['RMQ_PORT'],
            $_ENV['RMQ_USERNAME'],
            $_ENV['RMQ_PASSWORD'],
        );
        return $connection->channel();
    };

    $dispatcher = function () use ($queueChannel){
        return new Dispatcher($queueChannel());
    };

    $builder->addDefinitions([
        MailerInterface::class => $mailer,

        Dispatcher::class => $dispatcher,

        UserNotification::class => function() use ($mailer) {
            return new UserNotification($mailer());
        },

        StockClientContract::class => function() {
            return new StockClient();
        },

        AMQPChannel::class => $queueChannel,

        TokenizerContract::class => function () {
            return new TokenService();
        }
    ]);
};
