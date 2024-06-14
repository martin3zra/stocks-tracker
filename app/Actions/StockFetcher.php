<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class StockFetcher
{

    public function __construct(public ContainerInterface $container)
    {

    }

    public function __invoke(Request $request, Response $response, $args): Response
    {
        $params = $request->getQueryParams();
        if (!array_key_exists('q', $params)) {
            $response->getBody()->write(json_encode(['message' => 'The q query item is required']));
            return $response->withStatus(422);
        }

        $user = $request->getAttribute('user');

        $stockData = $this->requestStockAndNotifyUser($user, $params['q']);

        $user->logQuerySearch($stockData);

        $response->getBody()->write(json_encode($stockData));

        return $response->withStatus(200);
    }

    private function requestStockAndNotifyUser(User $user, string $stockCode): array
    {

        $filename = $this->container
            ->get('stockClient')
            ->getStockInformation($stockCode);

        $csvHandler = new CsvHandler($filename);
        $csvData = $csvHandler->map();

        $user->notify($this->container->get('notifier'), $filename, $stockCode, $csvData);

        $csvHandler->deleteFile();

        return $this->transformValues($csvData[1]);
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
}
