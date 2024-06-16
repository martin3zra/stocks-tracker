<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Services\Dispatcher;
use App\Services\StockClientContract;
use App\Traits\HtmlGenerator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class StockFetcher
{

    use HtmlGenerator;

    public function __construct(
        private Dispatcher $dispatcher,
        private StockClientContract $stockClient,
    )
    {

    }

    public function __invoke(Request $request, Response $response, $args): Response
    {
        if (!array_key_exists('q', $request->getQueryParams())) {
            $response->getBody()->write(json_encode(['message' => 'The q query item is required']));
            return $response->withStatus(422);
        }

        $statusCode = 200;
        try {

            $stockData = $this->requestStockNotifyUserAndLog($request);

            $response->getBody()->write(json_encode($stockData));
        } catch (\Exception $e) {
            $statusCode = $e->getCode();
            $response->getBody()->write(json_encode(['message' => $e->getMessage()]));
        }

        return $response->withStatus($statusCode);
    }

    /**
     * @throws \Exception
     **/
    private function requestStockNotifyUserAndLog(Request $request): array
    {
        $stockCode = $request->getQueryParams()['q'];
        $filename = $this->stockClient->getStockInformation($stockCode);

        $csvData = array_map('str_getcsv', file($filename));

        // As we download a csv file and the file contains two rows, one for the header
        // and the other one for the values, so here we just made sure that the value
        // of the open key is N/D, that mean that the stock symbol does not exists
        if ($csvData[1][3] == "N/D") {
            throw new \Exception("$stockCode", 404);
        }

        $user = $request->getAttribute('user');
        $this->queueNotification($user, ['filename' => $filename, 'code' => $stockCode, 'data' => $csvData]);

        $stockData = $this->transformValues($csvData[1]);

        $user->logQuerySearch($stockData);

        return $stockData;
    }

    private function transformValues(array $values): array
    {
        return [
            'name' => $values[8],
            'symbol' => $values[0],
            'open' => $values[3],
            'high' => $values[4],
            'low' => $values[5],
            'close' => $values[6],
        ];
    }

    private function queueNotification(User $user, array $attributes = [])
    {
        $payload = [
            'to' => [
                'email' => $user->email,
                'name' => $user->name,
            ],
            'attachetmentPath' => $attributes['filename'],
            'html' => $this->generateHtmlContent($user->name, $attributes['code'], $attributes['data']),
        ];

        $this->dispatcher->dispatch($payload);
    }
}
