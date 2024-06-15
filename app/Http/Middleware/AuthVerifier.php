<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\TokenizerContract;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;

class AuthVerifier
{
    public function __construct(private TokenizerContract $tokenizer)
    {

    }

    public function __invoke(Request $request, RequestHandler $handler)
    {
        $response = new Response();

        if (!$request->hasHeader('Authorization')) {

            $response->getBody()->write(json_encode(['message' => 'Missing Authorization header']));

            return $response->withStatus(401);
        }

        try {
            $decoded = $this->tokenizer->verifyToken(
                $request->getHeaderLine('Authorization')
            );

            $user = User::where('email', $decoded->email)->firstOrFail();
            $request = $request->withAttribute('user', $user);

            return $handler->handle($request);
        } catch(ExpiredException|SignatureInvalidException $e) {
            $response->getBody()->write(json_encode(['message' => $e->getMessage()]));
        } catch(ModelNotFoundException $e) {
            $response->getBody()->write(json_encode(['message' => 'Invalid Authorization Token']));
        }

        return $response->withStatus(401);
    }
}
