<?php

namespace App\Exceptions;

use Exception;

class InsufficientCreditsException extends Exception
{
    public function __construct($required, $available)
    {
        parent::__construct("You need {$required} credits. You currently have {$available} credits.");
    }
} 