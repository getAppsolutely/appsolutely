<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\ProductAttribute;

final class ProductAttributeRepository extends BaseRepository
{
    public function model(): string
    {
        return ProductAttribute::class;
    }
}
