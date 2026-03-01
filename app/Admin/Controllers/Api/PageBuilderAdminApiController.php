<?php

declare(strict_types=1);

namespace App\Admin\Controllers\Api;

use App\Models\Page;
use App\Services\BlockRegistryService;
use App\Services\PageBlockSchemaService;
use App\Services\PageBlockService;
use App\Services\PageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PageBuilderAdminApiController extends AdminBaseApiController
{
    public function __construct(
        protected PageService $pageService,
        protected PageBlockService $pageBlockService,
        protected PageBlockSchemaService $pageBlockSchemaService,
        protected BlockRegistryService $blockRegistryService
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
     * Get available blocks registry from theme manifest.json.
     * Matches manifest component class to page_block to obtain block_id for correct saving.
     */
    public function getBlockRegistry(): JsonResponse
    {
        $data = $this->blockRegistryService->getRegistry();

        return $this->success($data);
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
