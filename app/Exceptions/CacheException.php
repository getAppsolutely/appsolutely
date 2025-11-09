<?php

declare(strict_types=1);

namespace App\Exceptions;

final class CacheException extends BusinessException
{
    public function __construct(string $message = 'Cache operation failed', array $errors = [])
    {
        parent::__construct($message, 1005, $errors);
    }
}
