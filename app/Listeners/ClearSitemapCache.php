<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ArticleCreated;
use App\Events\ArticleDeleted;
use App\Events\ArticleUpdated;
use App\Events\PageCreated;
use App\Events\PageDeleted;
use App\Events\PageUpdated;
use App\Events\ProductCreated;
use App\Events\ProductDeleted;
use App\Events\ProductUpdated;
use App\Services\Contracts\SitemapServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Listener to clear sitemap cache when content changes
 *
 * Handles cache invalidation for sitemap when pages, products, or articles
 * are created, updated, or deleted.
 */
final class ClearSitemapCache implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private readonly SitemapServiceInterface $sitemapService
    ) {}

    /**
     * Handle content change events (pages, products, articles)
     */
    public function handle(PageCreated|PageUpdated|PageDeleted|ProductCreated|ProductUpdated|ProductDeleted|ArticleCreated|ArticleUpdated|ArticleDeleted $event): void
    {
        $this->clearCache();
    }

    /**
     * Clear sitemap cache
     */
    private function clearCache(): void
    {
        $this->sitemapService->clearCache();
    }
}
