<?php

namespace App\Services;

use stdClass;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenService implements TokenizerContract
{

    public function createToken(string $email): string
    {
        $payload = [
            'iss' => 'stocks',
            'iat' => time(),
            'exp' => strtotime('+1 hour'),
            'email' => $email,
        ];

        $token = JWT::encode($payload, $_ENV['APP_KEY'], 'HS256');

        return $token;
    }

     /**
     *  @throws Firebase\JWT\SignatureInvalidException
     *  @throws Firebase\JWT\ExpiredException
     */
    public function verifyToken(string $token): stdClass
    {
        $tokenPlainText = $token;
        if (str_starts_with($tokenPlainText, 'Bearer')) {
            $tokenPlainText = explode(" ", $tokenPlainText)[1];
        }

        return JWT::decode($tokenPlainText, new Key($_ENV['APP_KEY'], 'HS256'));
    }
}
