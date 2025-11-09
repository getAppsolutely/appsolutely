<?php

declare(strict_types=1);

namespace App\Exceptions;

final class FormNotFoundException extends NotFoundException
{
    public function __construct(string $identifier, ?\Throwable $previous = null)
    {
        parent::__construct("Form not found: {$identifier}", 404, $previous);
    }
}
