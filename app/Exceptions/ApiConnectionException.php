<?php

namespace App\Exceptions;

use RuntimeException;

class ApiConnectionException extends RuntimeException
{
    public function __construct(string $message = "API Connection failed.")
    {
        return parent::__construct($message);
    }
}

