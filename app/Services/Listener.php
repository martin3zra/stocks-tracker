<?php

namespace App\Services;

use App\Actions\UserNotification;
use PhpAmqpLib\Channel\AMQPChannel;
use Symfony\Component\Console\Output\OutputInterface;

class Listener
{
    public function __construct(
        private AMQPChannel $channel,
        private UserNotification $notification,
        private OutputInterface $output
    )
    {

    }

    public function listen(): void
    {
        $this->output->writeln("Started to listen");
        $this->channel->queue_declare('email', false, true, false, false);
        $this->channel->basic_qos(null, 1, null);

        $this->channel->basic_consume(
            'email',
            '',
            false,
            false,
            false,
            false,
            function ($message) {
                $this->output->writeln("Consuming message");
                $attribues = json_decode($message->body, true);
                $this->notification->send($attribues['to'], $attribues['attachetmentPath'], $attribues['html'],);

                $channel = $message->delivery_info['channel'];
                $channel->basic_ack($message->delivery_info['delivery_tag']);
            }
        );

        while (count($this->channel->callbacks)) {
            $this->channel->wait();
        }
        $this->output->writeln("Done consuming messages!");
        $this->channel->close();
    }
}
