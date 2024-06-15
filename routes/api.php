<?php

declare(strict_types=1);

use App\Actions\Authentication;
use App\Actions\StockFetcher;
use App\Http\Middleware\AuthValidation;
use App\Http\Middleware\AuthVerifier;
use App\Http\Middleware\UserValidation;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

$app->group('', function() use ($app) {

    $app->get('/api', new \App\Actions\Welcome);

    $app->post('/api/users', new \App\Actions\UserRegister)->add(new UserValidation);
    $app->post('/api/auth', Authentication::class)->add(new AuthValidation);
    $app->get('/api/stock',  StockFetcher::class)->add(AuthVerifier::class);
    $app->get('/api/history', new \App\Actions\History)->add(AuthVerifier::class);
})
    ->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) {
        $response = $handler->handle($request);
        return $response->withHeader('Content-type', 'application/json');
    });;
