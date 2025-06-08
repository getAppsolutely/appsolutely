<?php

namespace App\Repositories;

use App\Models\ProductAttribute;

class ProductAttributeRepository extends BaseRepository
{
    public function model(): string
    {
        return ProductAttribute::class;
    }
}
