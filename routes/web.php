<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write("Welcome to Stock tracker API");
    return $response;
});
