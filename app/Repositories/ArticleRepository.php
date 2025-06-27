<?php

namespace App\Repositories;

use App\Models\Article;

class ArticleRepository extends BaseRepository
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
}
