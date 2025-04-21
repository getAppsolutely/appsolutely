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
}
