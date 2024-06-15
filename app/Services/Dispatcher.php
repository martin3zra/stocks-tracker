<?php

declare(strict_types=1);

namespace App\Services;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Message\AMQPMessage;

class Dispatcher
{
    private string $routingKey = "email";

    public function __construct(private AMQPChannel $channel)
    {

    }

    public function dispatch(array $attributes): void
    {

        $this->channel->queue_declare($this->routingKey, false, true, false, false);
        $message = new AMQPMessage(
            json_encode($attributes),
            ['delivery_mode' => 2]
        );

        $this->channel->basic_publish($message, '', $this->routingKey);
        $this->channel->close();
    }
}
