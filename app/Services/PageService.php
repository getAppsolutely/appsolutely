<?php

declare(strict_types=1);

namespace App\Services;

use App\Constants\BasicConstant;
use App\Models\Page;
use App\Repositories\PageBlockSettingRepository;
use App\Repositories\PageRepository;
use App\Services\Contracts\PageBlockSettingServiceInterface;
use App\Services\Contracts\PageServiceInterface;
use App\Services\Contracts\PageStructureServiceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * Service for core page operations
 *
 * This service handles basic page operations:
 * - Finding pages by slug, ID, or reference
 * - Managing page settings (save/reset)
 *
 * Block settings sync is delegated to PageBlockSettingService.
 * Page structure generation is delegated to PageStructureService.
 */
final readonly class PageService implements PageServiceInterface
{
    public function __construct(
        protected PageRepository $pageRepository,
        protected PageBlockSettingRepository $pageBlockSettingRepository,
        protected PageBlockSettingServiceInterface $blockSettingService,
        protected PageStructureServiceInterface $structureService
    ) {}

    public function findPublishedPage(string $slug): ?Page
    {
        return $this->pageRepository->findPageBySlug($slug, now());
    }

    public function findPublishedPageById(int $id): ?Page
    {
        return $this->pageRepository->findPageById($id, now());
    }

    public function findByReference(string $reference): Model
    {
        return $this->pageRepository->with(['blocks'])->reference($reference)->firstOrFail();
    }

    public function resetSetting(string $reference): Model
    {
        $page = $this->findByReference($reference);
        $this->pageRepository->updateSetting($page->id, []);
        $this->pageBlockSettingRepository->resetSetting($page->id);

        return $this->pageRepository->find($page->id);
    }

    public function saveSetting(string $reference, array $data): Model
    {
        $page = $this->findByReference($reference);

        // Extract block data from GrapesJS structure
        $blockData = Arr::get($data, BasicConstant::PAGE_GRAPESJS_KEY);
        if (empty($blockData) || ! is_array($blockData)) {
            $blockData = [];
        }

        // Reset existing settings and sync new ones
        $this->pageBlockSettingRepository->resetSetting($page->id);
        $this->blockSettingService->syncSettings($blockData, $page->id);

        // Update page settings
        $this->pageRepository->updateSetting($page->id, $data);

        return $this->pageRepository->find($page->id);
    }

    /**
     * Sync page block settings (delegates to PageBlockSettingService)
     */
    public function syncSettings(array $data, int $pageId): array
    {
        return $this->blockSettingService->syncSettings($data, $pageId);
    }

    /**
     * Get block value ID (delegates to PageBlockSettingService)
     */
    public function getBlockValueId(int $blockId): int
    {
        return $this->blockSettingService->getBlockValueId($blockId);
    }

    /**
     * Generate default page setting structure (delegates to PageStructureService)
     */
    public function generateDefaultPageSetting(): array
    {
        return $this->structureService->generateDefaultPageSetting();
    }
}
