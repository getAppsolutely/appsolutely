<?php

declare(strict_types=1);

namespace App\Livewire;

use Illuminate\Contracts\Container\Container;

final class ProductVariantBlock extends BaseBlock
{
    /**
     * Currently selected variant index.
     */
    public int $selectedVariantIndex = 0;

    /**
     * Currently selected color index for the active variant.
     */
    public ?int $selectedColorIndex = null;

    /**
     * Initialize the component.
     */
    protected function initializeComponent(Container $container): void
    {
        $product = $this->getData('product', []);

        // Set default selected variant (first one)
        if (! empty($product['variants'])) {
            $this->selectedVariantIndex = 0;
            $this->initializeSelectedColor();
        }
    }

    /**
     * Initialize the selected color for the current variant.
     */
    private function initializeSelectedColor(): void
    {
        $product = $this->getData('product', []);
        $variant = $product['variants'][$this->selectedVariantIndex] ?? null;

        if ($variant && ! empty($variant['colors'])) {
            $this->selectedColorIndex = 0;
        }
    }

    /**
     * Switch to a different variant.
     */
    public function switchVariant(int $index): void
    {
        $product = $this->getData('product', []);
        if (isset($product['variants'][$index])) {
            $this->selectedVariantIndex = $index;
            $this->initializeSelectedColor();
        }
    }

    /**
     * Select a color for the current variant.
     */
    public function selectColor(int $index): void
    {
        $product = $this->getData('product', []);
        $variant = $product['variants'][$this->selectedVariantIndex] ?? null;

        if ($variant && isset($variant['colors'][$index])) {
            $this->selectedColorIndex = $index;
        }
    }
}
