<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class TransactionException extends Exception
{
    public function __construct(string $message = 'Database transaction failed', int $code = 500, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
