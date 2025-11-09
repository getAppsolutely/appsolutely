<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Generic resource not found exception
 *
 * Use specific exceptions (FormNotFoundException, etc.) when possible.
 * This is a fallback for general "not found" scenarios.
 */
final class NotFoundException extends BaseNotFoundException
{
    public function __construct(string $identifier, ?string $userMessage = null, ?\Throwable $previous = null, array $context = [])
    {
        parent::__construct($identifier, 'Resource', $userMessage, $previous, $context);
    }
}
