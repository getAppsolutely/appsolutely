<?php

namespace App\Repositories;

use App\Models\ProductSku;

class ProductSkuRepository extends BaseRepository
{
    public function model(): string
    {
        return ProductSku::class;
    }

    public function getActiveSkusByProduct(int $productId)
    {
        return $this->model->where('product_id', $productId)
            ->status()
            ->orderBy('sort')
            ->get();
    }

    public function getSkusBySkuKey(string $attributeKey, string $productId)
    {
        return $this->model->where('product_id', $productId)
            ->where(function ($query) use ($attributeKey) {

                $query->whereJsonContains('attributes->key', $attributeKey);

            })->first();
    }
}
