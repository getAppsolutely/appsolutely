<?php

namespace App\Repositories;

use App\Models\ProductAttributeValue;

final class ProductAttributeValueRepository extends BaseRepository
{
    public function model(): string
    {
        return ProductAttributeValue::class;
    }
}
