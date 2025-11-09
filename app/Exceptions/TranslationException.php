<?php

declare(strict_types=1);

namespace App\Exceptions;

/**
 * Exception for translation operation errors
 */
final class TranslationException extends BaseBusinessException
{
    public function __construct(
        string $message = 'Translation operation failed',
        int $code = 1004,
        array $errors = [],
        ?string $userMessage = null,
        ?\Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct(
            $message,
            $code,
            $errors,
            $userMessage ?? 'A translation error occurred. Please try again.',
            $previous,
            $context
        );
    }
}
