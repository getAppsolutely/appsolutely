<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception for cache operation errors
 */
final class CacheException extends BaseSystemException
{
    public function __construct(
        string $message = 'Cache operation failed',
        ?string $userMessage = null,
        ?\Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct(
            $message,
            'Cache',
            500,
            $userMessage ?? 'A caching error occurred. Please try again.',
            $previous,
            $context
        );
    }
}
