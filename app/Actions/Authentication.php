<?php

namespace App\Actions;

use App\Models\User;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Firebase\JWT\JWT;

class Authentication
{
    public function __invoke(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        try {
            $user = User::where('email', $data['email'])->firstOrFail();

            if (!password_verify($data['password'], $user['password'])) {
                $response->getBody()->write(json_encode(['message' => 'Invalid credentials']));
                return $response->withStatus(401);
            }

            $response->getBody()->write(json_encode(['token' => $this->createToken($user)]));

            return $response->withStatus(200);

        } catch(ModelNotFoundException $e) {

            $response->getBody()->write(json_encode(['message' => 'Invalid credentials']));
            return $response->withStatus(401);
        }
    }

    private function createToken(User $user): string
    {
        $payload = [
            'iss' => 'stocks',
            'iat' => time(),
            'exp' => strtotime('+1 hour'),
            'email' => $user->email,
        ];

        $token = JWT::encode($payload, $_ENV['APP_KEY'], 'HS256');

        return $token;
    }
}
