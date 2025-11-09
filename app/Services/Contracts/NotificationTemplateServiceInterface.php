<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\NotificationTemplate;

interface NotificationTemplateServiceInterface
{
    /**
     * Get available variables for a category
     */
    public function getAvailableVariables(string $category): array;

    /**
     * Create a new template
     */
    public function createTemplate(array $data): NotificationTemplate;

    /**
     * Update existing template
     */
    public function updateTemplate(NotificationTemplate $template, array $data): NotificationTemplate;

    /**
     * Render template with variables and preview
     */
    public function renderPreview(NotificationTemplate $template, array $sampleData = []): array;

    /**
     * Get sample data for template preview
     */
    public function getSampleVariables(string $category): array;

    /**
     * Validate template syntax
     */
    public function validateTemplate(string $content, array $allowedVariables): array;

    /**
     * Duplicate template
     */
    public function duplicateTemplate(NotificationTemplate $template): NotificationTemplate;
}
