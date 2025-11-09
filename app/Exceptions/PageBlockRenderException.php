<?php

declare(strict_types=1);

namespace App\Exceptions;

final class PageBlockRenderException extends PageBlockException
{
    public function __construct(string $message, array $errors = [])
    {
        parent::__construct("Page block render error: {$message}", 1002, $errors);
    }
}
