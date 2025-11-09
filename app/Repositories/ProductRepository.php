<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

final class ProductRepository extends BaseRepository
{
    public function model(): string
    {
        return Product::class;
    }

    public function getActiveProducts(): Collection
    {
        return $this->model->status()->orderBy('sort')->get();
    }

    /**
     * Get a list of active products for dropdown selection
     */
    public function getActiveList(): array
    {
        return $this->model->status()
            ->orderBy('title')
            ->pluck('title', 'id')
            ->toArray();
    }

    /**
     * Get all published products for sitemap generation
     */
    public function getPublishedProductsForSitemap(Carbon $datetime): Collection
    {
        return $this->model->newQuery()
            ->status()
            ->published($datetime)
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->with(['categories' => function ($query) use ($datetime) {
                $query->status()->published($datetime)->whereNotNull('slug')->where('slug', '!=', '');
            }])
            ->orderBy('published_at', 'desc')
            ->get();
    }
}
