<?php

declare(strict_types=1);

namespace App\Exceptions;

final class TranslationException extends BusinessException
{
    public function __construct(string $message = 'Translation operation failed', array $errors = [])
    {
        parent::__construct($message, 1004, $errors);
    }
}
