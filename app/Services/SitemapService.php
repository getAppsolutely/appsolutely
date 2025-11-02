<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Page;
use App\Repositories\PageRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/**
 * Service to generate XML sitemap for SEO
 *
 * Follows architecture patterns:
 * - Repository pattern for data access
 * - Caching for performance
 * - Multi-language support via LaravelLocalization
 * - Respects published/expired dates
 */
final readonly class SitemapService
{
    private const CACHE_TTL = 86400; // 24 hours

    private const CACHE_KEY = 'sitemap:xml';

    public function __construct(
        private PageRepository $pageRepository
    ) {}

    /**
     * Generate XML sitemap with caching
     */
    public function generateXml(): string
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return $this->buildSitemap();
        });
    }

    /**
     * Clear sitemap cache (call after page updates)
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Build the sitemap XML content using Blade template
     */
    private function buildSitemap(): string
    {
        $pages            = $this->getPublishedPages();
        $defaultLocale    = LaravelLocalization::getDefaultLocale();
        $supportedLocales = supported_locales();
        $baseUrl          = rtrim(app_url(), '/');

        $urls = [];

        // Add home page
        $urls[] = $this->prepareUrlEntry('', now(), 'daily', 1.0, $supportedLocales, $defaultLocale, $baseUrl);

        // Add all published pages (skip if already added as home)
        foreach ($pages as $page) {
            $slug = $this->getPageSlug($page);

            // Skip if this is the home page (already added above)
            if ($slug === '' || $slug === '/') {
                continue;
            }

            $lastMod    = $this->getLastModDate($page);
            $priority   = $this->calculatePriority($page);
            $changeFreq = $this->getChangeFreq($page);

            $urls[] = $this->prepareUrlEntry($slug, $lastMod, $changeFreq, $priority, $supportedLocales, $defaultLocale, $baseUrl);
        }

        return View::make('sitemap', ['urls' => $urls])->render();
    }

    /**
     * Get all published pages
     */
    private function getPublishedPages(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->pageRepository->getPublishedPagesForSitemap(now());
    }

    /**
     * Prepare URL entry data for Blade template
     */
    private function prepareUrlEntry(
        string $slug,
        Carbon $lastMod,
        string $changeFreq,
        float $priority,
        array $supportedLocales,
        string $defaultLocale,
        string $baseUrl
    ): array {
        $slug = ltrim($slug, '/');
        // For home page (empty slug), don't add trailing slash
        $loc = $slug === '' ? $baseUrl : $baseUrl . '/' . $slug;

        $entry = [
            'loc'        => htmlspecialchars($loc, ENT_XML1, 'UTF-8'),
            'lastmod'    => $lastMod->toAtomString(),
            'changefreq' => $changeFreq,
            'priority'   => number_format($priority, 1, '.', ''),
            'hreflang'   => [],
        ];

        // Add hreflang alternatives if multi-language (more than 1 locale)
        if (count($supportedLocales) > 1) {
            $entry['hreflang'] = $this->prepareHreflangEntries($slug, $supportedLocales, $defaultLocale, $baseUrl);
        }

        return $entry;
    }

    /**
     * Prepare hreflang alternative links data for Blade template
     */
    private function prepareHreflangEntries(
        string $slug,
        array $supportedLocales,
        string $defaultLocale,
        string $baseUrl
    ): array {
        $hreflang = [];

        // Add x-default pointing to default locale
        $defaultSlug = ($slug === '') ? '' : '/' . $slug;
        $defaultUrl  = $baseUrl . $defaultSlug;
        $hreflang[]  = [
            'lang' => 'x-default',
            'href' => htmlspecialchars($defaultUrl, ENT_XML1, 'UTF-8'),
        ];

        // Add each locale variant
        foreach ($supportedLocales as $localeKey => $locale) {
            // For home page, just use locale prefix; for others, add slug after locale
            if ($slug === '') {
                $localeUrl = $baseUrl . '/' . $localeKey;
            } else {
                $localeUrl = $baseUrl . '/' . $localeKey . '/' . $slug;
            }
            $hreflang[] = [
                'lang' => htmlspecialchars($localeKey, ENT_XML1, 'UTF-8'),
                'href' => htmlspecialchars($localeUrl, ENT_XML1, 'UTF-8'),
            ];
        }

        return $hreflang;
    }

    /**
     * Get page slug for sitemap
     */
    private function getPageSlug(Page $page): string
    {
        return trim($page->slug ?? '', '/');
    }

    /**
     * Get last modification date (updated_at or published_at)
     */
    private function getLastModDate(Page $page): Carbon
    {
        return $page->updated_at?->greaterThan($page->published_at ?? now())
            ? $page->updated_at
            : ($page->published_at ?? now());
    }

    /**
     * Calculate priority based on page hierarchy
     */
    private function calculatePriority(Page $page): float
    {
        // Home page or root pages get highest priority
        if (empty($page->parent_id) && ($page->slug === '' || $page->slug === '/')) {
            return 1.0;
        }

        // Root level pages
        if (empty($page->parent_id)) {
            return 0.9;
        }

        // Nested pages get lower priority based on depth
        return 0.7;
    }

    /**
     * Determine change frequency based on page update patterns
     */
    private function getChangeFreq(Page $page): string
    {
        $daysSinceUpdate = now()->diffInDays($page->updated_at ?? $page->published_at ?? now());

        if ($daysSinceUpdate < 1) {
            return 'daily';
        }

        if ($daysSinceUpdate < 7) {
            return 'weekly';
        }

        if ($daysSinceUpdate < 30) {
            return 'monthly';
        }

        return 'yearly';
    }
}
