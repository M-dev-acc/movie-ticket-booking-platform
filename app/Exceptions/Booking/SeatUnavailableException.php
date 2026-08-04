<?php

namespace App\Exceptions\Booking;

use RuntimeException;

class SeatUnavailableException extends RuntimeException
{
    public function __construct(string $message = "The selected seats are not available.")
    {
        return parent::__construct($message);
    }
}
