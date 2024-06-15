<?php

declare(strict_types=1);

namespace App\Traits;

trait HtmlGenerator
{
    public function generateHtmlContent(string $name,$code, array $values): string
    {
        $html = '<p>Hey '. $name. ' the data we were able to find base on your query criteria using the stock code: <b>'.$code.'</b> is available in the attached CSV file!</p>';
        $table = '<table style="border: 1px solid black;">';
        foreach ($values as $key => $row) {
            $table .= "<tr>";
            foreach ($row as $index => $value) {
                $table .= "<td>$value</td>";
            }
            $table .= "</tr>";
        }

        $table .= '</table>';

        return "$html $table";
    }
}
