<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Repositories\ProductSkuRepository;
use Illuminate\Support\Collection;

final class ProductService
{
    public function __construct(
        protected ProductRepository $productRepository,
        protected ProductSkuRepository $productSkuRepository
    ) {}

    public function getActiveProducts(): Collection
    {
        return $this->productRepository->getActiveProducts();
    }

    public function getActiveSkus(Product $product): Collection
    {
        return $this->productSkuRepository->getActiveSkusByProduct($product->id);
    }
}
