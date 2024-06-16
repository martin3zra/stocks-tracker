<?php

declare(strict_types=1);

namespace App\Actions;

use App\Services\Dispatcher;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class UserNotification
{
    public function __construct(private MailerInterface $mailer)
    {

    }

    public function send(array $to, string $attachetmentPath, $html): void
    {
        $attachetmentPathExists = file_exists(dirname(__FILE__, 3) . "/public/$attachetmentPath");

        $email = (new Email())
            ->from('hello@stock-tracker.test')
            ->to(new Address($to['email'], $to['name']))
            ->subject('Stock Query Result!')
            ->html($html);

        if ($attachetmentPathExists) {
            $email->attachFromPath(
                dirname(__FILE__, 3) . "/public/$attachetmentPath"
            );
        }

        $this->mailer->send($email);

        if($attachetmentPathExists) {
            unlink(dirname(__FILE__, 3) . "/public/$attachetmentPath");
        }
    }
}
