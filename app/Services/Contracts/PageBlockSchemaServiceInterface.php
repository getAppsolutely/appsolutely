<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\PageBlock;

interface PageBlockSchemaServiceInterface
{
    /**
     * Get schema for a block
     */
    public function getBlockSchema(PageBlock $block): array;

    /**
     * Validate schema values against block schema
     */
    public function validateSchemaValues(array $schema, array $values): array;

    /**
     * Get default values from schema
     */
    public function getDefaultValues(array $schema): array;

    /**
     * Merge schema values with defaults
     */
    public function mergeWithDefaults(array $schema, array $values): array;

    /**
     * Generate form configuration for admin interface
     */
    public function generateFormConfig(array $schema): array;
}
