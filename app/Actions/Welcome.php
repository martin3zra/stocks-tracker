<?php

namespace App\Actions;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class Welcome
{
    public function __invoke(Request $request, Response $response): Response
    {
        $response->getBody()->write(json_encode(['message' => 'Welcome to Stock tracker API']));

        return $response;
    }
}
