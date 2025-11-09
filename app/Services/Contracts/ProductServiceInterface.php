<?php

declare(strict_types=1);

namespace App\Services\Contracts;

use App\Models\Product;
use Illuminate\Support\Collection;

interface ProductServiceInterface
{
    /**
     * Get all active products
     */
    public function getActiveProducts(): Collection;

    /**
     * Get active SKUs for a product
     */
    public function getActiveSkus(Product $product): Collection;

    /**
     * Get product types with translations
     */
    public function getProductTypes(): array;

    /**
     * Get shipment methods for manual virtual products
     */
    public function getShipmentMethodForManualVirtualProduct(): array;

    /**
     * Get shipment methods for auto virtual products
     */
    public function getShipmentMethodForAutoVirtualProduct(): array;

    /**
     * Get shipment methods for physical products
     */
    public function getShipmentMethodForPhysicalProduct(): array;
}
