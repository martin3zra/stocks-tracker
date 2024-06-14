<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class UserNotifier
{
    public function __construct(private MailerInterface $mailer)
    {

    }

    public function notify(User $user, $filename, $stockCode, array $data): void
    {
        $htmlTable = $this->generateHtmlTable($data);

        $email = (new Email())
            ->from('hello@stock-tracker.test')
            ->to($user->email)
            ->subject('Stock Query Result!')
            ->attachFromPath($filename)
            ->html('
                <p>Hey '. $user->name. ' the data we were able to find base on your query criteria using the stock code: <b>'.$stockCode.'</b> is available in the attached CSV file!</p>
            ' . $htmlTable);

        $this->mailer->send($email);
    }

    private function generateHtmlTable(array $values): string
    {
        $table = '<table style="border: 1px solid black;">';
        foreach ($values as $key => $row) {
            $table .= "<tr>";
            foreach ($row as $index => $value) {
                $table .= "<td>$value</td>";
            }
            $table .= "</tr>";
        }

        $table .= '</table>';

        return $table;
    }
}
