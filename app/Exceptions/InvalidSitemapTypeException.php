<?php

declare(strict_types=1);

namespace App\Exceptions;

final class InvalidSitemapTypeException extends BusinessException
{
    public function __construct(string $type, array $allowedTypes = [])
    {
        $allowedTypesStr  = ! empty($allowedTypes) ? implode(', ', $allowedTypes) : 'page, article, product';
        $message          = "Invalid sitemap type: {$type}. Allowed types: {$allowedTypesStr}";

        parent::__construct($message, 1003, ['type' => $type, 'allowed_types' => $allowedTypes]);
    }
}
