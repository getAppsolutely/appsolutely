<?php

namespace App\Repositories;

use App\Models\ProductAttributeGroup;

class ProductAttributeGroupRepository extends BaseRepository
{
    public function model()
    {
        return ProductAttributeGroup::class;
    }
}
