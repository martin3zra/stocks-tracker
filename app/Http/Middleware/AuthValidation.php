<?php

namespace App\Http\Middleware;
use JsonSchema\Validator as Validator;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as Response;

class AuthValidation {
    public function __invoke(Request $request, RequestHandler $handler)
    {

        $schema = <<<'JSON'
        {
            "type": "object",
            "properties": {
                "email": {"type": "string"},
                "password": {"type": "string"}
            },
            "required": ["email", "password"]
        }
        JSON;

        $schemaObject = json_decode($schema);

        $validator = new Validator();
        $data = json_decode(json_encode($request->getParsedBody()));
        $validator->validate($data, $schemaObject);

        if ($validator->isValid()) {
            $response = $handler->handle($request);
            return $response->withHeader('Content-type', 'application/json');;
        }

        $response = new Response();
        $response->getBody()->write(json_encode($validator->getErrors()));
        return $response
            ->withStatus(422)
            ->withHeader('Content-type', 'application/json');
    }
}
