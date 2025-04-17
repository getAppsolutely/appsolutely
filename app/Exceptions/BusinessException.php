<?php

namespace App\Exceptions;

use Exception;

class BusinessException extends Exception
{
    public int $code;

    public array $errors;

    public function __construct(string $message = 'Business Error', int $code = 1000, array $errors = [])
    {
        parent::__construct($message, $code);
        $this->code   = $code;
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
