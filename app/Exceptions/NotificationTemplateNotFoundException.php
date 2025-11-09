<?php

declare(strict_types=1);

namespace App\Exceptions;

final class NotificationTemplateNotFoundException extends NotFoundException
{
    public function __construct(string $templateSlug, ?\Throwable $previous = null)
    {
        parent::__construct("Notification template not found: {$templateSlug}", 404, $previous);
    }
}
