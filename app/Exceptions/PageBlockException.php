<?php

declare(strict_types=1);

namespace App\Exceptions;

final class PageBlockException extends BusinessException
{
    public function __construct(string $message = 'Page block error', int $code = 1001, array $errors = [])
    {
        parent::__construct($message, $code, $errors);
    }
}
