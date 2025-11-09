<?php

namespace App\Repositories;

use App\Models\ProductCategory;
use App\Repositories\Traits\ActiveTreeList;

final class ProductCategoryRepository extends BaseRepository
{
    use ActiveTreeList;

    public function model(): string
    {
        return ProductCategory::class;
    }
}
