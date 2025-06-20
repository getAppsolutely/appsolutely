<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\PageBlockGroupRepository;
use App\Repositories\PageBlockRepository;
use App\Repositories\PageBlockSettingRepository;

final class PageBlockService
{
    public function __construct(
        protected PageBlockGroupRepository $groupRepository,
        protected PageBlockRepository $blockRepository,
        protected PageBlockSettingRepository $settingRepository,
        protected PageBlockSchemaService $schemaService
    ) {}

    public function getCategorisedBlocks()
    {
        return $this->groupRepository->getCategorisedBlocks();
    }

    public function getPublishedBlockSettings(int $pageId): \Illuminate\Database\Eloquent\Collection
    {
        return $this->settingRepository->getActivePublishedSettings($pageId);
    }

    public function updateBlockSettingPublishStatus(int $settingId, ?string $publishedAt = null, ?string $expiredAt = null): bool
    {
        return $this->settingRepository->updatePublishStatus($settingId, $publishedAt, $expiredAt);
    }

    /**
     * Get schema fields for a block
     */
    public function getSchemaFields(int $blockId): array
    {
        $block = $this->blockRepository->find($blockId);

        if (! $block) {
            return [];
        }

        $schema     = $this->schemaService->getBlockSchema($block);
        $formConfig = $this->schemaService->generateFormConfig($schema);

        return $formConfig;
    }
}
