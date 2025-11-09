<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Article;
use App\Services\Contracts\SitemapServiceInterface;

/**
 * Observer for Article model events
 *
 * Handles side effects when articles are created, updated, or deleted.
 * Separates concerns from the model itself.
 */
final class ArticleObserver
{
    public function __construct(
        private readonly SitemapServiceInterface $sitemapService
    ) {}

    /**
     * Handle the Article "saved" event (fires for both create and update).
     */
    public function saved(Article $article): void
    {
        $this->clearSitemapCache();
    }

    /**
     * Handle the Article "deleted" event.
     */
    public function deleted(Article $article): void
    {
        $this->clearSitemapCache();
    }

    /**
     * Clear sitemap cache after article changes
     */
    private function clearSitemapCache(): void
    {
        $this->sitemapService->clearCache();
    }
}
