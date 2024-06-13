<?php

declare(strict_types=1);

namespace App\Actions;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class History
{
    public function __invoke(Request $request, Response $response, $args): Response
    {
        $user = $request->getAttribute('user');

        $stockData = $user->queryHistories;
        $response->getBody()->write(json_encode($stockData));

        return $response->withStatus(200);
    }
}
