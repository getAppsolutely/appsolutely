<?php

namespace App\Repositories;

use App\Models\ProductAttributeGroup;

class ProductAttributeGroupRepository extends BaseRepository
{
    public function model(): string
    {
        return ProductAttributeGroup::class;
    }
}
