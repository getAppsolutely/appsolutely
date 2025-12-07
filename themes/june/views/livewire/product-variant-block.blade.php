@php
    $product = $displayOptions['product'] ?? [];
    $variants = $product['variants'] ?? [];

    // Pre-process all image URLs through asset_url() helper
    foreach ($variants as &$variant) {
        if (!empty($variant['colors'])) {
            foreach ($variant['colors'] as &$color) {
                if (!empty($color['images'])) {
                    $color['images'] = array_map(fn($img) => asset_url($img), $color['images']);
                }
            }
        }
    }
    unset($variant, $color);

    $product['variants'] = $variants;
@endphp

<section class="product-variant-block py-5" wire:key="product-variant-{{ $this->getId() }}" x-data="{
    selectedVariantIndex: 0,
    selectedColorIndex: 0,
    product: {{ json_encode($product) }},
    get currentVariant() {
        return this.product.variants?.[this.selectedVariantIndex] || null;
    },
    get currentColor() {
        return this.currentVariant?.colors?.[this.selectedColorIndex] || null;
    },
    switchVariant(index) {
        this.selectedVariantIndex = index;
        // Reset to first color when switching variants
        if (this.currentVariant?.colors?.length) {
            this.selectedColorIndex = 0;
        }
    },
    selectColor(index) {
        this.selectedColorIndex = index;
    }
}">
    <div class="container">
        @if (empty($product))
            <div class="alert alert-warning">No product data configured</div>
        @else
            <!-- Product Header -->
            <div class="product-header mb-4 text-center">
                <h1 class="h2 fw-bold mb-2">{{ $product['name'] ?? 'Product' }}</h1>
                @if (!empty($product['common']))
                    <div class="product-meta text-muted">
                        @if (!empty($product['common']['brand']))
                            <span class="me-3"><strong>Brand:</strong> {{ $product['common']['brand'] }}</span>
                        @endif
                        @if (!empty($product['common']['year']))
                            <span class="me-3"><strong>Year:</strong> {{ $product['common']['year'] }}</span>
                        @endif
                        @if (!empty($product['common']['body_type']))
                            <span class="me-3"><strong>Body Type:</strong>
                                {{ $product['common']['body_type'] }}</span>
                        @endif
                        @if (!empty($product['common']['platform']))
                            <span class="me-3"><strong>Platform:</strong> {{ $product['common']['platform'] }}</span>
                        @endif
                    </div>
                @endif
            </div>

            @if (empty($variants))
                <div class="alert alert-info">No variants available</div>
            @else
                <!-- Variant Tabs -->
                <div class="variant-tabs mb-4">
                    <ul class="nav nav-tabs justify-content-center" role="tablist">
                        @foreach ($variants as $index => $variant)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link"
                                    :class="{ 'active': selectedVariantIndex === {{ $index }} }" type="button"
                                    @click="switchVariant({{ $index }})" role="tab"
                                    :aria-selected="selectedVariantIndex === {{ $index }} ? 'true' : 'false'">
                                    {{ $variant['name'] ?? 'Variant ' . ($index + 1) }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Variant Content -->
                <div class="row g-4">
                    <!-- Left Column: Images -->
                    <div class="col-lg-7">
                        <!-- Main Image -->
                        <div class="main-image-container mb-4" x-show="currentColor && currentColor.images?.length">
                            <img :src="currentColor?.images?.[0] || ''" :alt="currentColor?.name || 'Product Image'"
                                class="img-fluid rounded shadow-sm w-100 product-main-image"
                                style="max-height: 500px; object-fit: contain;"
                                x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        </div>

                        <!-- Color Selection -->
                        <div class="color-selection mb-4" x-show="currentVariant?.colors?.length">
                            <h6 class="mb-3 fw-semibold">Select Color</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <template x-for="(color, colorIndex) in currentVariant?.colors || []"
                                    :key="colorIndex">
                                    <button type="button" class="color-option p-0 border-0"
                                        :class="{ 'active': selectedColorIndex === colorIndex }"
                                        @click="selectColor(colorIndex)"
                                        :style="`
                                                                                    width: 50px;
                                                                                    height: 50px;
                                                                                    border-radius: 50%;
                                                                                    background: ${color.code || '#ccc'};
                                                                                    outline: 3px solid ${selectedColorIndex === colorIndex ? '#007bff' : 'transparent'};
                                                                                    outline-offset: -3px;
                                                                                    position: relative;
                                                                                    cursor: pointer;
                                                                                `"
                                        :title="color.name || 'Color ' + (colorIndex + 1)" data-bs-toggle="tooltip">
                                        <i x-show="selectedColorIndex === colorIndex"
                                            class="fas fa-check text-white position-absolute top-50 start-50 translate-middle"></i>
                                    </button>
                                </template>
                            </div>
                            <p class="mt-2 mb-0 text-muted" x-show="currentColor">
                                <strong>Selected: </strong><span x-text="currentColor?.name || 'Unnamed Color'"></span>
                            </p>
                        </div>

                        <!-- Additional Images -->
                        <div class="additional-images mt-4" x-show="currentColor?.images?.length > 1">
                            <h6 class="mb-3 fw-semibold">More Images</h6>
                            <div class="row g-2">
                                <template x-for="(image, imageIndex) in currentColor?.images?.slice(1) || []"
                                    :key="imageIndex">
                                    <div class="col-4 col-md-3">
                                        <img :src="image" :alt="'Product Image ' + (imageIndex + 2)"
                                            class="img-fluid rounded shadow-sm w-100 product-thumbnail"
                                            style="height: 100px; object-fit: cover; cursor: pointer;"
                                            @click="$el.closest('.col-lg-7').querySelector('.product-main-image').src = $el.src">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Price & Specs -->
                    <div class="col-lg-5">
                        <!-- Variant Name & Price -->
                        <div class="variant-info mb-4">
                            <h2 class="h3 fw-bold mb-2" x-text="currentVariant?.name || 'Variant'"></h2>
                            <div class="price-section mb-3" x-show="currentVariant?.price">
                                <span class="h4 fw-bold text-primary">
                                    $<span
                                        x-text="typeof currentVariant?.price === 'number' ? currentVariant.price.toLocaleString() : currentVariant?.price"></span>
                                </span>
                            </div>
                        </div>

                        <!-- Specifications -->
                        <div class="specifications-section mb-4" x-show="currentVariant?.specs?.length">
                            <h5 class="fw-bold mb-3">Specifications</h5>
                            <ul class="list-group">
                                <template x-for="(spec, specIndex) in currentVariant?.specs || []"
                                    :key="specIndex">
                                    <li class="list-group-item" x-text="spec"></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    </div>
</section>
