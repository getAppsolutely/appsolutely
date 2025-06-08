<?php

namespace App\Services;

use App\Models\Page;
use App\Repositories\PageRepository;

class PageService
{
    public function __construct(
        private PageRepository $pageRepository
    ) {}

    public function getPublishedPage(string $slug): ?Page
    {
        return $this->pageRepository->findPublishedBySlug($slug);
    }

    public function findByReference(string $reference)
    {
        return $this->pageRepository->findByReference($reference);
    }
}
