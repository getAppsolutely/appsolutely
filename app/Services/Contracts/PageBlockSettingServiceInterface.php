<?php

declare(strict_types=1);

namespace App\Services\Contracts;

interface PageBlockSettingServiceInterface
{
    /**
     * Sync page block settings from GrapesJS data
     *
     * @param  array  $data  Array of block setting data from page builder
     * @param  int  $pageId  The page ID to sync settings for
     * @return array Array of created/updated block settings
     */
    public function syncSettings(array $data, int $pageId): array;

    /**
     * Get or create block value ID for a block
     * Reuses existing block value if block is already used elsewhere
     *
     * @param  int  $blockId  The block ID
     * @return int The block value ID
     */
    public function getBlockValueId(int $blockId): int;
}
