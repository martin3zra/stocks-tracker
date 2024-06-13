<?php

declare(strict_types=1);

use App\Http\Middleware\AuthValidation;
use App\Http\Middleware\AuthVerifier;
use App\Http\Middleware\UserValidation;

$app->get('/api', new \App\Actions\Welcome);

$app->post('/api/users', new \App\Actions\UserRegister)->add(new UserValidation);
$app->post('/api/auth', new \App\Actions\Authentication)->add(new AuthValidation);
$app->get('/api/stock', new \App\Actions\StockFetcher)->add(new AuthVerifier);
$app->get('/api/history', new \App\Actions\History)->add(new AuthVerifier);
