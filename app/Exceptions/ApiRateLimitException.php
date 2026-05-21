<?php

namespace App\Exceptions;

use RuntimeException;

class ApiRateLimitException extends RuntimeException
{
    public function __construct(
        public readonly int $retryAfterMs = 6000,
        string $message = "API Rate limit exceeded."
    )
    {
        return parent::__construct($message);
    }
}

