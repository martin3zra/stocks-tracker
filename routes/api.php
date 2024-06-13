<?php

use App\Http\Middleware\AuthValidation;
use App\Http\Middleware\UserValidation;

$app->get('/api', new \App\Actions\Welcome);

$app->post('/api/users', new \App\Actions\UserRegister)->add(new UserValidation);
$app->post('/api/auth', new \App\Actions\Authentication)->add(new AuthValidation);
