<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Page;
use App\Repositories\PageRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
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
     * Build the sitemap XML content
     */
    private function buildSitemap(): string
    {
        $pages            = $this->getPublishedPages();
        $supportedLocales = LaravelLocalization::getSupportedLocales();
        $defaultLocale    = LaravelLocalization::getDefaultLocale();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . PHP_EOL;
        $xml .= '        xmlns:xhtml="http://www.w3.org/1999/xhtml">' . PHP_EOL;

        // Add home page (empty slug or '/')
        $homeSlug = '';
        $xml .= $this->buildUrlEntry($homeSlug, now(), 'daily', 1.0, $supportedLocales, $defaultLocale);

        // Add all published pages (skip if already added as home)
        foreach ($pages as $page) {
            $slug = $this->getPageSlug($page);

            // Skip if this is the home page (already added above)
            if ($slug === '' || $slug === '/') {
                continue;
            }

            $lastmod    = $this->getLastModDate($page);
            $priority   = $this->calculatePriority($page);
            $changefreq = $this->getChangeFreq($page);

            $xml .= $this->buildUrlEntry($slug, $lastmod, $changefreq, $priority, $supportedLocales, $defaultLocale, $page);
        }

        $xml .= '</urlset>';

        return $xml;
    }

    /**
     * Get all published pages
     */
    private function getPublishedPages(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->pageRepository->getPublishedPagesForSitemap(now());
    }

    /**
     * Build a URL entry with optional hreflang support
     */
    private function buildUrlEntry(
        string $slug,
        Carbon $lastmod,
        string $changefreq,
        float $priority,
        array $supportedLocales,
        string $defaultLocale,
        ?Page $page = null
    ): string {
        $baseUrl = rtrim(app_url(), '/');
        $slug    = ltrim($slug, '/');
        // For home page (empty slug), don't add trailing slash
        $loc = $slug === '' ? $baseUrl : $baseUrl . '/' . $slug;

        $xml = '  <url>' . PHP_EOL;
        $xml .= '    <loc>' . htmlspecialchars($loc, ENT_XML1, 'UTF-8') . '</loc>' . PHP_EOL;
        $xml .= '    <lastmod>' . $lastmod->toAtomString() . '</lastmod>' . PHP_EOL;
        $xml .= '    <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
        $xml .= '    <priority>' . number_format($priority, 1, '.', '') . '</priority>' . PHP_EOL;

        // Add hreflang alternatives if multi-language (more than 1 locale)
        if (count($supportedLocales) > 1) {
            $xml .= $this->buildHreflangEntries($slug, $supportedLocales, $defaultLocale, $baseUrl);
        }

        $xml .= '  </url>' . PHP_EOL;

        return $xml;
    }

    /**
     * Build hreflang alternative links for multi-language support
     */
    private function buildHreflangEntries(
        string $slug,
        array $supportedLocales,
        string $defaultLocale,
        string $baseUrl
    ): string {
        $xml = '';

        // Add x-default pointing to default locale
        $defaultSlug = ($slug === '') ? '' : '/' . $slug;
        $defaultUrl  = $baseUrl . $defaultSlug;
        $xml .= '    <xhtml:link rel="alternate" hreflang="x-default" href="' . htmlspecialchars($defaultUrl, ENT_XML1, 'UTF-8') . '"/>' . PHP_EOL;

        // Add each locale variant
        foreach ($supportedLocales as $localeKey => $locale) {
            // For home page, just use locale prefix; for others, add slug after locale
            if ($slug === '') {
                $localeUrl = $baseUrl . '/' . $localeKey;
            } else {
                $localeUrl = $baseUrl . '/' . $localeKey . '/' . $slug;
            }
            $xml .= '    <xhtml:link rel="alternate" hreflang="' . htmlspecialchars($localeKey, ENT_XML1, 'UTF-8') . '" href="' . htmlspecialchars($localeUrl, ENT_XML1, 'UTF-8') . '"/>' . PHP_EOL;
        }

        return $xml;
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
