<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class StorageException extends Exception
{
    public function __construct(string $message = 'Storage operation failed', int $code = 500, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
