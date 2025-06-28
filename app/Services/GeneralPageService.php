<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\GeneralPage;
use App\Models\Page;
use App\Repositories\PageRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Service to handle all page resolution including regular pages and nested URLs
 *
 * Consolidates logic for:
 * 1. Regular page lookup by slug
 * 2. Nested URL resolution using dynamic block configuration
 * 3. Content discovery through block repositories
 * 4. GeneralPage creation for unified page interface
 */
final readonly class GeneralPageService
{
    private const CACHE_TTL = 3600; // 1 hour

    private const CACHE_PREFIX = 'page_resolution:';

    public function __construct(
        private PageRepository $pageRepository,
        private PageService $pageService
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

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($fullSlug) {
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
                    log_debug('Nested URL resolved', [
                        'slug'           => $fullSlug,
                        'parent_page_id' => $page->getParentPage()->id,
                        'content_type'   => $page->getContentType(),
                        'content_id'     => $page->getContent()->id,
                    ]);
                } else {
                    log_debug('Root page resolved', [
                        'slug'         => $fullSlug,
                        'page_id'      => $page->id,
                        'content_type' => $page->getContentType(),
                    ]);
                }
            } else {
                log_info('Page not found', ['slug' => $fullSlug]);
            }

            return $page;

        } catch (\Exception $e) {
            log_error('Error resolving page', [
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
        $page = $this->pageService->getPublishedPage($fullSlug);

        if ($page) {
            // Wrap regular Page in GeneralPage for unified interface
            return new GeneralPage($page);
        }

        // If no exact match, try to handle as nested URL
        return $this->resolveNestedUrl($fullSlug);
    }

    /**
     * Resolve a nested URL by finding the parent page and nested content
     *
     * @param  string  $fullSlug  The complete URL slug
     */
    public function resolveNestedUrl(string $fullSlug): ?GeneralPage
    {
        // Split the URL into segments
        $segments = array_filter(explode('/', trim($fullSlug, '/')));

        if (empty($segments)) {
            return null;
        }

        // Try to find a matching parent page and content
        // Start from the shortest parent path and work our way up
        for ($i = 0; $i < count($segments); $i++) {
            $parentSlugSegments = array_slice($segments, 0, $i + 1);
            $childSlugSegments  = array_slice($segments, $i + 1);

            // Skip if there are no child segments (we need nested content)
            if (empty($childSlugSegments)) {
                continue;
            }

            $parentSlug = '/' . implode('/', $parentSlugSegments);
            $childSlug  = implode('/', $childSlugSegments);

            // Try to find the parent page
            $parentPage = $this->pageRepository->findPageBySlug($parentSlug, now());

            if ($parentPage) {
                // Try to find nested content based on parent page blocks
                $nestedContent = $this->findNestedContent($parentPage, $childSlug);

                if ($nestedContent) {
                    return new GeneralPage(
                        $nestedContent['content'],
                        $parentPage,
                        $childSlug
                    );
                }
            }
        }

        return null;
    }

    /**
     * Find nested content based on parent page blocks and child slug
     *
     * @param  Page  $parentPage  The parent page containing blocks
     * @param  string  $childSlug  The child content slug
     * @return array|null Array with content, type, and repository info
     */
    protected function findNestedContent(Page $parentPage, string $childSlug): ?array
    {
        // Get the blocks configuration from appsolutely config
        $blocksConfig = config('appsolutely.blocks', []);
        $nestedBlocks = array_keys($blocksConfig);

        if (empty($blocksConfig)) {
            return null;
        }

        $possibleBlocks = [];
        // Check each block in the parent page
        foreach ($parentPage->blocks as $blockSetting) {
            $blockClass = $blockSetting->block->class ?? null;
            $blockValue = $blockSetting->blockValue ?? null;

            $schemaValues    = json_decode($blockValue->schema_values, true) ?? [];
            $repositoryClass = $schemaValues['repository'] ?? null;

            if (! $blockValue || ! in_array($blockClass, $nestedBlocks)) {
                continue;
            }

            if (! $repositoryClass) {
                $possibleBlocks[] = $blockClass;

                continue;
            }

            // Found a matching repository, try to find content
            $content = $this->findContentUsingRepository($repositoryClass, $childSlug);

            if ($content) {
                return $content;
            }
        }

        $result = $this->getPossibleContent($possibleBlocks, $childSlug);

        return $result ?? null;
    }

    protected function getPossibleContent(array $blocks, string $childSlug): ?array
    {
        $blocksConfig = config('appsolutely.blocks', []);
        $block        = \Arr::first($blocks);
        $repositories = $blocksConfig[$block] ?? [];

        foreach ($repositories as $repository) {
            $content = $this->findContentUsingRepository($repository, $childSlug);
            if ($content) {
                return $content;
            }
        }

        return null;
    }

    /**
     * Find content using a specific repository class
     *
     * @param  string  $repositoryClass  The repository class name
     * @param  string  $childSlug  The content slug to find
     * @return array|null Array with content, type, and repository info
     */
    protected function findContentUsingRepository(string $repositoryClass, string $childSlug): ?array
    {
        try {
            // Instantiate the repository
            $repository = app($repositoryClass);

            // Find content using the repository
            $content = $this->findContentBySlug($repository, $childSlug);

            if ($content) {
                return [
                    'content'    => $content,
                    'repository' => $repositoryClass,
                ];
            }
        } catch (\Exception $e) {
            // Log the error but don't break the flow
            \log_warning("Failed to find nested content using repository {$repositoryClass}: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Find content by slug using repository methods
     *
     * @param  mixed  $repository  The repository instance
     * @param  string  $slug  The content slug
     * @return Model|null The found content model
     */
    protected function findContentBySlug($repository, string $slug): ?Model
    {
        $now = now();

        // Try different common method names that repositories might have
        $methods = ['findBySlug', 'getBySlug', 'findActiveBySlug'];

        foreach ($methods as $method) {
            if (method_exists($repository, $method)) {
                try {
                    $content = $repository->$method($slug, $now);
                    if ($content && $this->isContentValid($content, $now)) {
                        return $content;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

        return null;
    }

    /**
     * Check if content is valid (published and not expired)
     *
     * @param  Model  $content  The content model to validate
     * @param  Carbon  $now  Current timestamp
     * @return bool True if content is valid for display
     */
    protected function isContentValid(Model $content, Carbon $now): bool
    {
        // Check status
        if (isset($content->status) && $content->status !== 1) {
            return false;
        }

        // Check published_at
        if (isset($content->published_at) && $content->published_at > $now) {
            return false;
        }

        // Check expired_at
        if (isset($content->expired_at) && $content->expired_at <= $now) {
            return false;
        }

        return true;
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
        return $this->pageService->getPublishedPageById($id);
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
        $blocks = config('appsolutely.blocks', []);

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
        Cache::forget($cacheKey);

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
        if (method_exists(Cache::getStore(), 'flush')) {
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
