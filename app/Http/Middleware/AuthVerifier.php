<?php

namespace App\Http\Middleware;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;

class AuthVerifier
{
    public function __invoke(Request $request, RequestHandler $handler)
    {
        $response = new Response();

        if (!$request->hasHeader('Authorization')) {

            $response->getBody()->write(json_encode(['message' => 'Missing Authorization header']));

            return $response
                ->withStatus(401)
                ->withHeader('Content-type', 'application/json');
        }

        $token = explode(" ", $request->getHeaderLine('Authorization'))[1];

        try {
            $decoded = JWT::decode($token, new Key($_ENV['APP_KEY'], 'HS256'));

            $user = User::where('email', $decoded->email)->firstOrFail();
            $request = $request->withAttribute('user', $user);

            $response = $handler->handle($request);
            return $response->withHeader('Content-type', 'application/json');
        } catch(ExpiredException|SignatureInvalidException $e) {
            $response->getBody()->write(json_encode(['message' => $e->getMessage()]));
        } catch(ModelNotFoundException $e) {
            $response->getBody()->write(json_encode(['message' => 'Invalid Authorization Token']));
        }

        return $response->withStatus(401);
    }
}
