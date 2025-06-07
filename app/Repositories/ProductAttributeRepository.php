<?php

namespace App\Repositories;

use App\Models\ProductAttribute;

class ProductAttributeRepository extends BaseRepository
{
    public function model()
    {
        return ProductAttribute::class;
    }
}
