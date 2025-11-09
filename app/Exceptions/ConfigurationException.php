<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

final class ConfigurationException extends Exception
{
    public function __construct(string $message = 'Configuration error', int $code = 500, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
