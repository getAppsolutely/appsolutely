<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Models\GeneralPage;
use App\Models\Page;
use App\Repositories\PageRepository;
use App\Services\Contracts\GeneralPageServiceInterface;
use App\Services\Contracts\PageServiceInterface;
use Illuminate\Contracts\Cache\Repository as CacheRepository;

/**
 * Orchestrator service for page resolution with caching
 *
 * This service coordinates page resolution by composing:
 *
 * - PageServiceInterface: Handles regular page lookup and page-related operations
 * - NestedUrlResolverService: Resolves nested URLs and finds content through block configurations
 * - CacheRepository: Provides caching layer for performance
 *
 * Composition pattern:
 * 1. First attempts regular page lookup via PageService
 * 2. If not found, delegates to NestedUrlResolverService for nested URL resolution
 * 3. Caches results for performance optimization
 * 4. Provides unified GeneralPage interface regardless of resolution method
 *
 * This separation allows:
 * - Clear responsibility boundaries (resolution vs. caching vs. nested logic)
 * - Independent testing of resolution strategies
 * - Easy extension of resolution methods without affecting caching
 */
final readonly class GeneralPageService implements GeneralPageServiceInterface
{
    private const CACHE_TTL = 3600; // 1 hour

    private const CACHE_PREFIX = 'page_resolution:';

    public function __construct(
        private PageRepository $pageRepository,
        private PageServiceInterface $pageService,
        private CacheRepository $cache,
        private NestedUrlResolverService $nestedUrlResolver
    ) {}

    /**
     * Resolve page with caching and error handling
     * This is the main entry point from controllers
     * Caching is disabled in non-production environments for easier development
     *
     * @param  string|null  $slug  The raw slug input (already validated by route constraints)
     */
    public function resolvePageWithCaching(?string $slug): ?GeneralPage
    {
        $fullSlug = $this->normalizeSlug($slug);

        // Skip caching in non-production environments
        if (! app()->isProduction()) {
            return $this->performPageResolution($fullSlug);
        }

        $cacheKey = self::CACHE_PREFIX . md5($fullSlug);

        return $this->cache->remember($cacheKey, self::CACHE_TTL, function () use ($fullSlug) {
            return $this->performPageResolution($fullSlug);
        });
    }

    /**
     * Normalize the slug input.
     * Route constraints already handle validation, so we only need basic normalization.
     *
     * @param  string|null  $slug  The raw slug input (already validated by route)
     * @return string The normalized slug
     */
    protected function normalizeSlug(?string $slug): string
    {
        if ($slug === null) {
            return '/';
        }

        // Basic sanitization
        $slug = trim($slug);

        // Ensure slug starts with /
        if (! str_starts_with($slug, '/')) {
            $slug = '/' . $slug;
        }

        // Remove double slashes and trailing slashes (except root)
        $slug = preg_replace('#/+#', '/', $slug);

        return $slug === '/' ? '/' : rtrim($slug, '/');
    }

    /**
     * Perform the actual page resolution logic with logging.
     *
     * @param  string  $fullSlug  The full slug to resolve
     */
    protected function performPageResolution(string $fullSlug): ?GeneralPage
    {
        try {
            // Use the core resolution logic
            $page = $this->resolvePage($fullSlug);

            if ($page instanceof GeneralPage) {
                if ($page->isNested()) {
                    local_debug('Nested URL resolved', [
                        'slug'           => $fullSlug,
                        'parent_page_id' => $page->getParentPage()->id,
                        'content_type'   => $page->getContentType(),
                        'content_id'     => $page->getContent()->id,
                    ]);
                } else {
                    local_debug('Root page resolved', [
                        'slug'         => $fullSlug,
                        'page_id'      => $page->id,
                        'content_type' => $page->getContentType(),
                    ]);
                }
            } else {
                local_debug('Resource not found', ['slug' => $fullSlug]);
            }

            return $page;

        } catch (NotFoundException $e) {
            log_error('Failed to resolve page: resource not found', [
                'slug'  => $fullSlug,
                'error' => $e->getMessage(),
            ]);

            return null;
        } catch (\Exception $e) {
            log_error('Failed to resolve page: unexpected error', [
                'slug'  => $fullSlug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }

    /**
     * Resolve any page by slug - handles both regular pages and nested URLs
     *
     * @param  string  $fullSlug  The complete slug to resolve
     */
    public function resolvePage(string $fullSlug): ?GeneralPage
    {
        // First, try to find a page with the exact slug
        $page = $this->pageService->findPublishedPage($fullSlug);

        if ($page) {
            // Wrap regular Page in GeneralPage for unified interface
            return new GeneralPage($page);
        }

        // If no exact match, try to handle as nested URL
        return $this->resolveNestedUrl($fullSlug);
    }

    /**
     * Resolve nested URL (delegates to NestedUrlResolverService)
     *
     * @param  string  $fullSlug  The complete URL slug
     * @return GeneralPage|null The resolved nested page or null if not found
     */
    public function resolveNestedUrl(string $fullSlug): ?GeneralPage
    {
        $nestedResult = $this->nestedUrlResolver->resolveNestedUrl($fullSlug);

        if ($nestedResult) {
            return new GeneralPage(
                $nestedResult['content'],
                $nestedResult['parentPage'],
                $nestedResult['childSlug']
            );
        }

        return null;
    }

    /**
     * Get page hierarchy for breadcrumbs
     *
     * @param  GeneralPage  $page  The page to get hierarchy for
     * @return array Array of pages from root to current
     */
    public function getPageHierarchy(GeneralPage $page): array
    {
        $hierarchy = [];

        if ($page->isNested()) {
            // For nested pages, include parent page hierarchy
            $parentPage = $page->getParentPage();
            if ($parentPage->parent_id) {
                $hierarchy = $this->buildHierarchy($parentPage);
            } else {
                $hierarchy[] = $parentPage;
            }
        } else {
            // For root pages, build from parent relationships
            $hierarchy = $this->buildHierarchy($page->getContent());
        }

        return $hierarchy;
    }

    /**
     * Build page hierarchy from parent relationships
     *
     * @param  Page  $page  The page to start from
     * @return array Array of pages in hierarchy order
     */
    protected function buildHierarchy(Page $page): array
    {
        $hierarchy = [];
        $current   = $page;

        while ($current) {
            array_unshift($hierarchy, $current);
            $current = $current->parent_id ? $this->getPublishedPageById($current->parent_id) : null;
        }

        return $hierarchy;
    }

    /**
     * Get page by ID with published status check
     *
     * @param  int  $id  The page ID
     */
    public function getPublishedPageById(int $id): ?Page
    {
        return $this->pageService->findPublishedPageById($id);
    }

    /**
     * Add or update a nested content mapping in blocks config
     *
     * @param  string  $blockClass  The block component class
     * @param  array  $repositories  Array of repository classes
     */
    public function addBlockMapping(string $blockClass, array $repositories): void
    {
        $currentConfig              = config('appsolutely.blocks', []);
        $currentConfig[$blockClass] = $repositories;

        // Note: This would typically require updating the config file or using a cache
        // For runtime changes, you might want to implement a different approach
        config(['appsolutely.blocks' => $currentConfig]);
    }

    /**
     * Get all block mappings
     *
     * @return array The current block to repository mappings
     */
    public function getBlockMappings(): array
    {
        return config('appsolutely.blocks', []);
    }

    /**
     * Check if a block class has repository mappings
     *
     * @param  string  $blockClass  The block component class
     * @return bool True if mappings exist
     */
    public function hasBlockMapping(string $blockClass): bool
    {
        $blocks = $this->getBlockMappings();

        return isset($blocks[$blockClass]) && ! empty($blocks[$blockClass]);
    }

    /**
     * Clear the page resolution cache for a specific slug.
     * Useful when pages are updated.
     *
     * @param  string  $slug  The slug to clear from cache
     */
    public function clearPageCache(string $slug): void
    {
        $normalizedSlug = $this->normalizeSlug($slug);
        $cacheKey       = self::CACHE_PREFIX . md5($normalizedSlug);
        $this->cache->forget($cacheKey);

        log_info('Page cache cleared', ['slug' => $normalizedSlug]);
    }

    /**
     * Clear all page resolution cache.
     * Useful for cache invalidation during deployments.
     */
    public function clearAllPageCache(): void
    {
        // For a more sophisticated implementation, you would use cache tags
        // For now, we'll clear by pattern (if supported by cache driver)
        if (method_exists($this->cache, 'flush')) {
            // This is a simple approach - in production you'd want cache tagging
            log_warning('Full cache flush requested - consider implementing cache tagging for selective clearing');
        }

        log_info('Page cache clear requested - implement cache tagging for full clear');
    }

    /**
     * Get cache statistics for monitoring
     *
     * @return array Cache statistics
     */
    public function getCacheStats(): array
    {
        return [
            'cache_prefix' => self::CACHE_PREFIX,
            'cache_ttl'    => self::CACHE_TTL,
            'cache_driver' => config('cache.default'),
        ];
    }
}
