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
        $possibleSlugs = $this->getPossibleSlugs($slug);

        return $this->pageRepository->findPublishedBySlug($possibleSlugs, now());
    }

    public function getPossibleSlugs(string $slug): array
    {
        $slug    = trim($slug);
        $trimmed = trim($slug, '/');

        return array_unique([
            $slug,
            '/' . ltrim($slug, '/'),
            rtrim($slug, '/') . '/',
            '/' . $trimmed . '/',
        ]);
    }

    public function findByReference(string $reference): Model
    {
        return $this->pageRepository->with(['blocks'])->reference($reference)->firstOrFail();
    }

    public function resetSetting(string $reference): Model
    {
        $page = $this->findByReference($reference);
        $page->update(['setting' => []]);
        $this->pageBlockSettingRepository->resetSetting($page->id);

        return $page;
    }

    public function saveSetting($reference, $data): Model
    {
        $page = $this->findByReference($reference);

        $blocksKey = 'pages.0.frames.0.component.components';
        $blockData = \Arr::get($data, $blocksKey);
        if (empty($blockData) || ! is_array($blockData)) {
            $blockData = [];
        }

        $this->pageBlockSettingRepository->resetSetting($page->id);
        $this->pageBlockSettingRepository->syncSetting($blockData, $page->id);

        $page->update(['setting' => $data]);

        return $page;
    }
}
