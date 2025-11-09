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

    /**
     * Get product types with translations
     * Moved from Product model to service for better separation of concerns
     */
    public function getProductTypes(): array
    {
        return [
            Product::TYPE_PHYSICAL_PRODUCT                   => __t('Physical Product'),
            Product::TYPE_AUTO_DELIVERABLE_VIRTUAL_PRODUCT   => __t('Auto Deliverable Virtual Product'),
            Product::TYPE_MANUAL_DELIVERABLE_VIRTUAL_PRODUCT => __t('Manual Deliverable Virtual Product'),
        ];
    }

    /**
     * Get shipment methods for manual virtual products
     * Moved from Product model to service
     */
    public function getShipmentMethodForManualVirtualProduct(): array
    {
        return Product::SHIPMENT_METHOD_MANUAL_DELIVERABLE_VIRTUAL_PRODUCT;
    }

    /**
     * Get shipment methods for auto virtual products
     * Moved from Product model to service
     */
    public function getShipmentMethodForAutoVirtualProduct(): array
    {
        return Product::SHIPMENT_METHOD_AUTO_DELIVERABLE_VIRTUAL_PRODUCT;
    }

    /**
     * Get shipment methods for physical products
     * Moved from Product model to service
     */
    public function getShipmentMethodForPhysicalProduct(): array
    {
        return Product::SHIPMENT_METHOD_PHYSICAL_PRODUCT;
    }
}
