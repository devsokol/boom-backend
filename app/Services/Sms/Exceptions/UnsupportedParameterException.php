<?php

namespace App\Services\Sms\Exceptions;

use Exception;

class UnsupportedParameterException extends Exception
{
    /**
     * @var int
     */
    protected $code = 406;

    public function __construct(string $message)
    {
        parent::__construct($message, $this->code, $previous = null);
    }

    public function getStatusCode(): int
    {
        return $this->code;
    }
}
