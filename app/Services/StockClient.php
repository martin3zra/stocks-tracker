<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;

class StockClient implements StockClientContract
{
    private Client $client;
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => $_ENV['STOCK_URL'],
            'timeout'  => 10,
        ]);
    }

    public function getStockInformation(string $code): string
    {
        $filename = $this->getFileName($code);

        $this->client->request('GET', "/q/l/?s=$code&f=sd2t2ohlcvn&h&e=csv", ['sink' => $filename]);

        return $filename;
    }

    public function getFileName(string $code): string
    {
        $time = microtime();
        $fileUniquePrefix = md5("$time");

        return "$fileUniquePrefix-$code.csv";
    }
}
