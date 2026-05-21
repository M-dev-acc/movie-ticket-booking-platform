<?php
namespace App\Exceptions;

use RuntimeException;

class ApiAuthException extends RuntimeException
{
    public function __construct(string $message = "Api authentication failed.")
    {
        return parent::__construct($message);
    }
}

