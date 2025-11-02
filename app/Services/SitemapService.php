<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Article;
use App\Models\Page;
use App\Models\Product;
use App\Repositories\ArticleRepository;
use App\Repositories\PageRepository;
use App\Repositories\ProductRepository;
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

    private const CACHE_KEY_PAGE = 'sitemap:page:xml';

    private const CACHE_KEY_ARTICLE = 'sitemap:article:xml';

    private const CACHE_KEY_PRODUCT = 'sitemap:product:xml';

    public function __construct(
        private PageRepository $pageRepository,
        private ArticleRepository $articleRepository,
        private ProductRepository $productRepository
    ) {}

    /**
     * Generate XML sitemap index with caching
     * This is the main sitemap.xml that references sub-sitemaps
     */
    public function generateXml(): string
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return $this->buildSitemapIndex();
        });
    }

    /**
     * Generate page sitemap XML with caching
     */
    public function generatePageXml(): string
    {
        return Cache::remember(self::CACHE_KEY_PAGE, self::CACHE_TTL, function () {
            return $this->buildPageSitemap();
        });
    }

    /**
     * Generate article sitemap XML with caching
     */
    public function generateArticleXml(): string
    {
        return Cache::remember(self::CACHE_KEY_ARTICLE, self::CACHE_TTL, function () {
            return $this->buildArticleSitemap();
        });
    }

    /**
     * Generate product sitemap XML with caching
     */
    public function generateProductXml(): string
    {
        return Cache::remember(self::CACHE_KEY_PRODUCT, self::CACHE_TTL, function () {
            return $this->buildProductSitemap();
        });
    }

    /**
     * Generate sitemap XML by type (page, article, or product) with caching
     */
    public function generateTypeXml(string $type): string
    {
        return match (strtolower($type)) {
            'page'    => $this->generatePageXml(),
            'article' => $this->generateArticleXml(),
            'product' => $this->generateProductXml(),
            default   => throw new \InvalidArgumentException("Invalid sitemap type: {$type}. Allowed types: page, article, product"),
        };
    }

    /**
     * Clear all sitemap cache (call after updates)
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::CACHE_KEY_PAGE);
        Cache::forget(self::CACHE_KEY_ARTICLE);
        Cache::forget(self::CACHE_KEY_PRODUCT);
    }

    /**
     * Build the sitemap index XML that references sub-sitemaps
     * Only includes sitemaps that have content
     */
    private function buildSitemapIndex(): string
    {
        $baseUrl  = rtrim(app_url(), '/');
        $sitemaps = [];

        // Only add page sitemap if there are published pages (home page is always included)
        $pages = $this->getPublishedPages();
        if ($pages->isNotEmpty()) {
            $sitemaps[] = [
                'loc'     => $baseUrl . '/sitemap-page.xml',
                'lastmod' => now()->toAtomString(),
            ];
        }

        // Only add article sitemap if there are published articles
        $articles = $this->getPublishedArticles();
        if ($articles->isNotEmpty()) {
            $sitemaps[] = [
                'loc'     => $baseUrl . '/sitemap-article.xml',
                'lastmod' => now()->toAtomString(),
            ];
        }

        // Only add product sitemap if there are published products
        $products = $this->getPublishedProducts();
        if ($products->isNotEmpty()) {
            $sitemaps[] = [
                'loc'     => $baseUrl . '/sitemap-product.xml',
                'lastmod' => now()->toAtomString(),
            ];
        }

        return View::make('sitemap-index', ['sitemaps' => $sitemaps])->render();
    }

    /**
     * Build the page sitemap XML content using Blade template
     */
    private function buildPageSitemap(): string
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
     * Build the article sitemap XML content using Blade template
     */
    private function buildArticleSitemap(): string
    {
        $articles          = $this->getPublishedArticles();
        $defaultLocale     = LaravelLocalization::getDefaultLocale();
        $supportedLocales  = supported_locales();
        $baseUrl           = rtrim(app_url(), '/');

        $urls = [];

        foreach ($articles as $article) {
            $slug = $this->getArticleSlug($article);

            $lastMod    = $this->getArticleLastModDate($article);
            $priority   = 0.8; // Articles get medium-high priority
            $changeFreq = $this->getArticleChangeFreq($article);

            $urls[] = $this->prepareUrlEntry('articles/' . $slug, $lastMod, $changeFreq, $priority, $supportedLocales, $defaultLocale, $baseUrl);
        }

        return View::make('sitemap', ['urls' => $urls])->render();
    }

    /**
     * Build the product sitemap XML content using Blade template
     */
    private function buildProductSitemap(): string
    {
        $products          = $this->getPublishedProducts();
        $defaultLocale     = LaravelLocalization::getDefaultLocale();
        $supportedLocales  = supported_locales();
        $baseUrl           = rtrim(app_url(), '/');

        $urls = [];

        foreach ($products as $product) {
            $slug = $this->getProductSlug($product);

            $lastMod    = $this->getProductLastModDate($product);
            $priority   = 0.8; // Products get medium-high priority
            $changeFreq = $this->getProductChangeFreq($product);

            $urls[] = $this->prepareUrlEntry('products/' . $slug, $lastMod, $changeFreq, $priority, $supportedLocales, $defaultLocale, $baseUrl);
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
     * Get all published articles
     */
    private function getPublishedArticles(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->articleRepository->getPublishedArticlesForSitemap(now());
    }

    /**
     * Get all published products
     */
    private function getPublishedProducts(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->productRepository->getPublishedProductsForSitemap(now());
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

    /**
     * Get article slug for sitemap
     */
    private function getArticleSlug(Article $article): string
    {
        return trim($article->slug ?? '', '/');
    }

    /**
     * Get article last modification date
     */
    private function getArticleLastModDate(Article $article): Carbon
    {
        return $article->updated_at?->greaterThan($article->published_at ?? now())
            ? $article->updated_at
            : ($article->published_at ?? now());
    }

    /**
     * Determine change frequency for articles
     */
    private function getArticleChangeFreq(Article $article): string
    {
        $daysSinceUpdate = now()->diffInDays($article->updated_at ?? $article->published_at ?? now());

        if ($daysSinceUpdate < 7) {
            return 'weekly';
        }

        if ($daysSinceUpdate < 30) {
            return 'monthly';
        }

        return 'yearly';
    }

    /**
     * Get product slug for sitemap
     */
    private function getProductSlug(Product $product): string
    {
        return trim($product->slug ?? '', '/');
    }

    /**
     * Get product last modification date
     */
    private function getProductLastModDate(Product $product): Carbon
    {
        return $product->updated_at?->greaterThan($product->published_at ?? now())
            ? $product->updated_at
            : ($product->published_at ?? now());
    }

    /**
     * Determine change frequency for products
     */
    private function getProductChangeFreq(Product $product): string
    {
        $daysSinceUpdate = now()->diffInDays($product->updated_at ?? $product->published_at ?? now());

        if ($daysSinceUpdate < 7) {
            return 'weekly';
        }

        if ($daysSinceUpdate < 30) {
            return 'monthly';
        }

        return 'yearly';
    }
}
