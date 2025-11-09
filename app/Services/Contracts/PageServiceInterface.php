<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Page;
use Illuminate\Database\Eloquent\Model;

interface PageServiceInterface
{
    /**
     * Find a published page by slug
     */
    public function findPublishedPage(string $slug): ?Page;

    /**
     * Find a published page by ID
     */
    public function findPublishedPageById(int $id): ?Page;

    /**
     * Find a page by reference
     */
    public function findByReference(string $reference): Model;

    /**
     * Reset page settings
     */
    public function resetSetting(string $reference): Model;

    /**
     * Save page settings
     */
    public function saveSetting(string $reference, array $data): Model;

    /**
     * Sync page block settings
     */
    public function syncSettings(array $data, int $pageId): array;

    /**
     * Get block value ID for a block
     */
    public function getBlockValueId(int $blockId): int;

    /**
     * Generate default page setting structure
     */
    public function generateDefaultPageSetting(): array;
}
