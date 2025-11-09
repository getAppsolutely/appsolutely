<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Page;
use App\Services\Contracts\SitemapServiceInterface;

/**
 * Observer for Page model events
 *
 * Handles side effects when pages are created, updated, or deleted.
 * Separates concerns from the model itself.
 */
final class PageObserver
{
    public function __construct(
        private readonly SitemapServiceInterface $sitemapService
    ) {}

    /**
     * Handle the Page "saved" event (fires for both create and update).
     */
    public function saved(Page $page): void
    {
        $this->clearSitemapCache();
    }

    /**
     * Handle the Page "deleted" event.
     */
    public function deleted(Page $page): void
    {
        $this->clearSitemapCache();
    }

    /**
     * Clear sitemap cache after page changes
     */
    private function clearSitemapCache(): void
    {
        $this->sitemapService->clearCache();
    }
}
