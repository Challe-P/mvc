<?php

namespace App\Project\Exceptions;

use Exception;
use Throwable;

class PositionFilledException extends Exception
{
    public function __construct(string $message = "Position on row is filled.", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
