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

        $this->pageBlockSettingRepository->resetSetting($page->id);
        $this->pageBlockSettingRepository->syncSetting($blockData, $page->id);

        $page->update(['content' => $data]);

        return $page;
    }
}
