<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ProductAttributeGroup;

final class ProductAttributeGroupRepository extends BaseRepository
{
    public function model(): string
    {
        return ProductAttributeGroup::class;
    }
}
