<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository extends BaseRepository
{
    public function model(): string
    {
        return Product::class;
    }

    public function getActiveProducts()
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
}
