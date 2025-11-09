<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Product;
use App\Services\Contracts\SitemapServiceInterface;

/**
 * Observer for Product model events
 *
 * Handles side effects when products are created, updated, or deleted.
 * Separates concerns from the model itself.
 */
final class ProductObserver
{
    public function __construct(
        private readonly SitemapServiceInterface $sitemapService
    ) {}

    /**
     * Handle the Product "saved" event (fires for both create and update).
     */
    public function saved(Product $product): void
    {
        $this->clearSitemapCache();
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->clearSitemapCache();
    }

    /**
     * Clear sitemap cache after product changes
     */
    private function clearSitemapCache(): void
    {
        $this->sitemapService->clearCache();
    }
}
