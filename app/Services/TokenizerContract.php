<?php

declare(strict_types=1);

namespace App\Services;

use stdClass;

interface TokenizerContract
{
    public function createToken(string $email): string;

    /**
     *  @throws Firebase\JWT\SignatureInvalidException
     *  @throws Firebase\JWT\ExpiredException
     */
    public function verifyToken(string $token): stdClass;
}
