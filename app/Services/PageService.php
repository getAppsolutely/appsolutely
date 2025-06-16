<?php

namespace App\Services;

use App\Models\Model;
use App\Models\Page;
use App\Repositories\PageBlockSettingRepository;
use App\Repositories\PageRepository;

class PageService
{
    public function __construct(
        protected PageRepository $pageRepository,
        protected PageBlockSettingRepository $pageBlockSettingRepository
    ) {}

    public function getPublishedPage(string $slug): ?Page
    {
        return $this->pageRepository->findPublishedBySlug($slug);
    }

    public function findByReference(string $reference): Model
    {
        return $this->pageRepository->with(['blocks'])->reference($reference)->firstOrFail();
    }

    public function resetPageContent(string $reference): Model
    {
        $page = $this->findByReference($reference);
        $page->update(['content' => '']);

        return $page;
    }

    public function savePageData($reference, $data): Model
    {
        $page = $this->findByReference($reference);

        $blocksKey = 'pages.0.frames.0.component.components';
        $blockData = \Arr::get($data, $blocksKey);
        if (empty($blockData) || ! is_array($blockData)) {
            $blockData = [];
        }

        $originalIds = $page->blocks()->pluck('id')->toArray();
        $updatedIds  = array_column($blockData, 'id');

        $toAddIds = array_diff($updatedIds, $originalIds);
        $toAdd    = array_filter($blockData, function ($block) use ($toAddIds) {
            return in_array($block['id'], $toAddIds);
        });

        $toRemoveIds = array_diff($originalIds, $updatedIds);

        $this->pageBlockSettingRepository->createInBatch($toAdd, $page->id);
        $this->pageBlockSettingRepository->disableInBatch($toRemoveIds);

        $page->update(['content' => $data]);

        return $page;
    }
}
