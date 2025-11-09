<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GeneralPage;
use App\Repositories\PageBlockGroupRepository;
use App\Repositories\PageBlockRepository;
use App\Repositories\PageBlockSettingRepository;
use App\Services\Contracts\PageBlockSchemaServiceInterface;
use App\Services\Contracts\PageBlockServiceInterface;
use Illuminate\Database\Eloquent\Collection;

final readonly class PageBlockService implements PageBlockServiceInterface
{
    public function __construct(
        protected PageBlockGroupRepository $groupRepository,
        protected PageBlockRepository $blockRepository,
        protected PageBlockSettingRepository $settingRepository,
        protected PageBlockSchemaServiceInterface $schemaService
    ) {}

    public function getCategorisedBlocks(): Collection
    {
        return $this->groupRepository->getCategorisedBlocks();
    }

    public function getPublishedBlockSettings(int $pageId): Collection
    {
        return $this->settingRepository->getActivePublishedSettings($pageId);
    }

    public function updateBlockSettingPublishStatus(int $settingId, ?string $publishedAt = null, ?string $expiredAt = null): int
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

    /**
     * Validate and render a block safely
     * Returns the rendered HTML or error message
     */
    public function renderBlockSafely($block, GeneralPage $page): string
    {
        $rendererService = app(\App\Services\BlockRendererService::class);

        return $rendererService->renderBlockSafely($block, $page);
    }

    /**
     * Get HTML for block errors (only in debug mode)
     */
    private function getBlockErrorHtml(string $message): string
    {
        if (! config('app.debug')) {
            return ''; // Return empty string in production
        }

        // In debug mode, throw exception to show error details
        throw new \App\Exceptions\PageBlockRenderException($message);
    }
}
