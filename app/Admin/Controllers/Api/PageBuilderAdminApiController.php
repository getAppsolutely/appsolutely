<?php

declare(strict_types=1);

namespace App\Admin\Controllers\Api;

use App\Models\Page;
use App\Services\PageBlockSchemaService;
use App\Services\PageBlockService;
use App\Services\PageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PageBuilderAdminApiController extends AdminBaseApiController
{
    public function __construct(protected PageService $pageService,
        protected PageBlockService $pageBlockService,
        protected PageBlockSchemaService $pageBlockSchemaService
    ) {}

    /**
     * Get page data for the builder
     */
    public function getPageData(Request $request, string $reference): JsonResponse
    {
        $page = $this->pageService->findByReference($reference);

        return $this->success([
            'page' => $page,
        ]);
    }

    /**
     * Save page builder data
     */
    public function savePageData(Request $request, string $reference): JsonResponse
    {
        $data = $request->get('data');
        if (empty($data)) {
            return $this->error('Page data cannot be empty.');
        }

        $page = $this->pageService->saveSetting($reference, $data);

        return $this->success($data, 'Page saved successfully.');
    }

    /**
     * Reset page builder data
     */
    public function resetPageData(Request $request, string $reference): JsonResponse
    {
        $page = $this->pageService->resetSetting($reference);

        return $this->success(['page' => $page], __t('Page setting has been reset.'));
    }

    /**
     * Get available blocks registry
     * Only returns blocks that have existing Livewire components
     */
    public function getBlockRegistry()
    {
        $data = $this->pageBlockService->getCategorisedBlocks();

        // Filter out blocks that don't have existing Livewire components
        $data = $data->map(function ($group) {
            $filteredBlocks = $group->blocks->filter(function ($block) {
                return class_exists($block->class);
            })->values();

            $group->setRelation('blocks', $filteredBlocks);

            return $group;
        })->filter(function ($group) {
            // Remove groups that have no valid blocks
            return $group->blocks->isNotEmpty();
        })->values();

        return $this->success($data->toArray());
    }

    /**
     * Get schema fields for a block
     */
    public function getSchemaFields(Request $request): JsonResponse
    {
        $blockId    = $request->get('q');
        $formConfig = $this->pageBlockService->getSchemaFields($blockId);

        if (empty($formConfig)) {
            return $this->error('Page block not found.');
        }

        return $this->success($formConfig);
    }
}
