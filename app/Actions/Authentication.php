<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Services\TokenizerContract;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Authentication
{
    public function __construct(private TokenizerContract $tokenizer)
    {

    }

    public function __invoke(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        try {
            $user = User::where('email', $data['email'])->firstOrFail();

            if (!password_verify($data['password'], $user['password'])) {
                $response->getBody()->write(json_encode(['message' => 'Invalid credentials']));
                return $response->withStatus(401);
            }

            $response->getBody()->write(json_encode([
                'token' => $this->tokenizer->createToken($user->email),
            ]));

            return $response->withStatus(200);

        } catch(ModelNotFoundException $e) {

            $response->getBody()->write(json_encode(['message' => 'Invalid credentials']));
            return $response->withStatus(401);
        }
    }

}
