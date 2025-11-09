<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\NotFoundException;
use App\Models\Page;
use App\Repositories\PageRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Service for resolving nested URLs
 *
 * Handles finding nested content based on parent pages and block configurations.
 * Separated from GeneralPageService for better single responsibility.
 */
final readonly class NestedUrlResolverService
{
    public function __construct(
        private PageRepository $pageRepository
    ) {}

    /**
     * Resolve a nested URL by finding the parent page and nested content
     *
     * @param  string  $fullSlug  The complete URL slug
     * @return array|null Array with content, parentPage, and childSlug, or null if not found
     */
    public function resolveNestedUrl(string $fullSlug): ?array
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
                    return [
                        'content'    => $nestedContent['content'],
                        'parentPage' => $parentPage,
                        'childSlug'  => $childSlug,
                        'repository' => $nestedContent['repository'] ?? null,
                    ];
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
     * @return array|null Array with content and repository info
     */
    public function findNestedContent(Page $parentPage, string $childSlug): ?array
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

            $schemaValues    = json_decode($blockValue->schema_values ?? '{}', true) ?? [];
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

    /**
     * Get possible content from blocks without explicit repository configuration
     */
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
     * @return array|null Array with content and repository info
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
        } catch (NotFoundException $e) {
            // Log the error but don't break the flow
            \log_warning("Failed to find nested content using repository {$repositoryClass}: resource not found", [
                'repository' => $repositoryClass,
                'slug'       => $childSlug,
                'error'      => $e->getMessage(),
            ]);
        } catch (\Exception $e) {
            // Log the error but don't break the flow
            \log_warning("Failed to find nested content using repository {$repositoryClass}: unexpected error", [
                'repository' => $repositoryClass,
                'slug'       => $childSlug,
                'error'      => $e->getMessage(),
            ]);
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
                } catch (NotFoundException|\InvalidArgumentException $e) {
                    // Expected exceptions when method doesn't exist or content not found
                    continue;
                } catch (\Exception $e) {
                    // Log unexpected errors but continue trying other methods
                    \log_warning('Unexpected error while finding content by slug', [
                        'method' => $method,
                        'slug'   => $slug,
                        'error'  => $e->getMessage(),
                    ]);

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
}
