<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Repositories\ProductSkuRepository;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Support\Collection;

final readonly class ProductService implements ProductServiceInterface
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
