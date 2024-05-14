<?php

namespace App\Project\Exceptions;

use Exception;
use Throwable;

class InvalidRowException extends Exception
{
    public function __construct(string $message = "Row is invalid.", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
