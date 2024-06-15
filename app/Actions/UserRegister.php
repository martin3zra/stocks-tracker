<?php

namespace App\Actions;

use App\Models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class UserRegister
{
    public function __invoke(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $user = User::where('email', $data['email'])->first();
        if ($user != null) {
            $response->getBody()->write(json_encode(['message' => 'An account with the email already exists']));
            return $response->withStatus(409);
        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
        ]);

        return $response->withStatus(201);
    }
}
