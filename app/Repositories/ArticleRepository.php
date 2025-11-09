<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Article;
use Carbon\Carbon;

final class ArticleRepository extends BaseRepository
{
    public function model(): string
    {
        return Article::class;
    }

    public function getPublishedArticles(array $filters = []): \Illuminate\Database\Eloquent\Builder
    {
        $query = $this->model->newQuery()
            ->where('status', 1) // Published articles only
            ->where('published_at', '<=', now());

        // Apply category filter
        if (! empty($filters['category_filter'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('slug', $filters['category_filter']);
            });
        }

        // Apply tag filter (if tags are implemented)
        if (! empty($filters['tag_filter'])) {
            // TODO: Implement tag filtering when tags are added to Article model
            // $query->whereHas('tags', function ($q) use ($filters) {
            //     $q->where('slug', $filters['tag_filter']);
            // });
        }

        // Apply ordering
        $orderBy        = $filters['order_by'] ?? 'published_at';
        $orderDirection = $filters['order_direction'] ?? 'desc';
        $query->orderBy($orderBy, $orderDirection);

        return $query;
    }

    public function findActiveBySlug(string $slug, ?Carbon $datetime): ?Article
    {
        return $this->model->newQuery()
            ->status()
            ->published($datetime)
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Get all published articles for sitemap generation
     */
    public function getPublishedArticlesForSitemap(Carbon $datetime): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->newQuery()
            ->status()
            ->published($datetime)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->with(['categories' => function ($query) {
                $query->status()->published(now())->whereNotNull('slug')->where('slug', '!=', '');
            }])
            ->orderBy('published_at', 'desc')
            ->get();
    }
}
