<?php

declare(strict_types=1);

namespace App\Actions;

use GuzzleHttp\Client;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class StockFetcher
{
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $params = $request->getQueryParams();
        if (!array_key_exists('q', $params)) {
            $response->getBody()->write(json_encode(['message' => 'The q query item is required']));
            return $response->withStatus(422);
        }

        $stockData = $this->requestCsvFileUsingStockCode($params['q']);

        $user = $request->getAttribute('user');

        $user->queryHistories()->create($stockData);

        $response->getBody()->write(json_encode($stockData));

        return $response->withStatus(200);
    }

    private function requestCsvFileUsingStockCode(string $stockCode): array
    {

        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://stooq.com',
            'timeout'  => 10,
        ]);

        $time = microtime();
        $fileUniquePrefix = md5("$time");
        $filename = "$fileUniquePrefix-$stockCode.csv";

        //Request the stock and save the result stream as file
        $client->request('GET', "/q/l/?s=$stockCode&f=sd2t2ohlcvn&h&e=csv", ['sink' => $filename]);

        return $this->readAndMapCsvFileContents($filename);
    }

    private function readAndMapCsvFileContents(string $filename): array
    {
        //Read and map CSV from the downloaed file
        $csv = array_map('str_getcsv', file($filename))[1];

        //This removes downloaded the file
        if(file_exists($filename)) {
            unlink($filename);
        }

        return [
            'name' => $csv[8],
            'symbol' => $csv[0],
            'open' => $csv[3],
            'high' => $csv[4],
            'low' => $csv[5],
            'close' => $csv[6],
        ];
    }
}
