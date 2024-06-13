<?php

use App\Http\Middleware\UserValidation;

$app->get('/api', new \App\Actions\Welcome);

$app->post('/api/users', new \App\Actions\UserRegister)->add(new UserValidation);
