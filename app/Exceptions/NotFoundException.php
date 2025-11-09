<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class NotFoundException extends Exception
{
    public function __construct(string $message = 'Resource not found', int $code = 404, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
