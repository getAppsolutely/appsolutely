<?php

namespace App\Repositories;

use App\Models\ProductAttributeValue;

class ProductAttributeValueRepository extends BaseRepository
{
    public function model()
    {
        return ProductAttributeValue::class;
    }
}
