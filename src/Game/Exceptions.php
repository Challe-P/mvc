<?php

namespace Challe_P\Game\Exceptions;

use Exception;
use Throwable;

class EmptyDeckException extends Exception
{
    public function __construct(string $message = "Kortleken är slut.", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
