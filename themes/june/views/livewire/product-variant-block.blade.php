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

    // Debug: Log product data on server side
    if (config('app.debug')) {
        logger()->debug('ProductVariantBlock: Product data', [
            'product_name' => $product['name'] ?? 'N/A',
            'variants_count' => count($variants),
            'has_variants' => !empty($variants),
        ]);
    }
@endphp

<section class="product-variant-block py-5" wire:key="product-variant-{{ $this->getId() }}" x-data="{
    selectedVariantIndex: 0,
    selectedColorIndex: 0,
    product: {{ Js::from($product) }},
    currentVariant: null,
    currentColor: null,

    init() {
        console.log('[ProductVariantBlock] Initializing with product:', this.product);
        console.log('[ProductVariantBlock] Variants count:', this.product?.variants?.length ?? 0);

        // Initialize computed values
        this.updateCurrentVariant();

        // Watch for changes to selectedVariantIndex
        this.$watch('selectedVariantIndex', () => {
            console.log('[ProductVariantBlock] Variant index changed to:', this.selectedVariantIndex);
            this.updateCurrentVariant();
        });

        // Watch for changes to selectedColorIndex
        this.$watch('selectedColorIndex', () => {
            console.log('[ProductVariantBlock] Color index changed to:', this.selectedColorIndex);
            this.updateCurrentColor();
        });
    },

    updateCurrentVariant() {
        const variants = this.product?.variants;
        if (!variants || !Array.isArray(variants) || variants.length === 0) {
            console.warn('[ProductVariantBlock] No variants available');
            this.currentVariant = null;
            this.currentColor = null;
            return;
        }

        this.currentVariant = variants[this.selectedVariantIndex] ?? variants[0] ?? null;
        console.log('[ProductVariantBlock] Current variant set to:', this.currentVariant?.name, this.currentVariant);

        // Also update current color when variant changes
        this.updateCurrentColor();
    },

    updateCurrentColor() {
        const colors = this.currentVariant?.colors;
        if (!colors || !Array.isArray(colors) || colors.length === 0) {
            console.log('[ProductVariantBlock] No colors available for current variant');
            this.currentColor = null;
            return;
        }

        this.currentColor = colors[this.selectedColorIndex] ?? colors[0] ?? null;
        console.log('[ProductVariantBlock] Current color set to:', this.currentColor?.name, this.currentColor);
    },

    switchVariant(index) {
        console.log('[ProductVariantBlock] switchVariant called with index:', index);
        this.selectedVariantIndex = index;
        // Reset to first color when switching variants
        this.selectedColorIndex = 0;
    },

    selectColor(index) {
        console.log('[ProductVariantBlock] selectColor called with index:', index);
        this.selectedColorIndex = index;
    },

    // Helper to get formatted price
    getFormattedPrice() {
        const price = this.currentVariant?.price;
        if (price === null || price === undefined || price === '') {
            return null;
        }
        return typeof price === 'number' ? price.toLocaleString() : price;
    }
}"
    x-init="init()">
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
                        <template x-if="currentColor && currentColor.images && currentColor.images.length > 0">
                            <div class="main-image-container mb-4">
                                <img :src="currentColor.images[0]" :alt="currentColor.name || 'Product Image'"
                                    class="img-fluid rounded shadow-sm w-100 product-main-image"
                                    style="max-height: 500px; object-fit: contain;"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                            </div>
                        </template>

                        <!-- Color Selection -->
                        <template x-if="currentVariant && currentVariant.colors && currentVariant.colors.length > 0">
                            <div class="color-selection mb-4">
                                <h6 class="mb-3 fw-semibold">Select Color</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <template x-for="(color, colorIndex) in currentVariant.colors"
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
                                    <strong>Selected: </strong><span
                                        x-text="currentColor?.name || 'Unnamed Color'"></span>
                                </p>
                            </div>
                        </template>

                        <!-- Additional Images -->
                        <template x-if="currentColor && currentColor.images && currentColor.images.length > 1">
                            <div class="additional-images mt-4">
                                <h6 class="mb-3 fw-semibold">More Images</h6>
                                <div class="row g-2">
                                    <template x-for="(image, imageIndex) in currentColor.images.slice(1)"
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
                        </template>
                    </div>

                    <!-- Right Column: Price & Specs -->
                    <div class="col-lg-5">
                        <!-- Variant Name & Price -->
                        <div class="variant-info mb-4">
                            <h2 class="h3 fw-bold mb-2" x-text="currentVariant?.name || 'Variant'"></h2>
                            <template x-if="currentVariant && getFormattedPrice() !== null">
                                <div class="price-section mb-3">
                                    <span class="h4 fw-bold text-primary">
                                        $<span x-text="getFormattedPrice()"></span>
                                    </span>
                                </div>
                            </template>
                        </div>

                        <!-- Specifications -->
                        <template x-if="currentVariant && currentVariant.specs && currentVariant.specs.length > 0">
                            <div class="specifications-section mb-4">
                                <h5 class="fw-bold mb-3">Specifications</h5>
                                <ul class="list-group">
                                    <template x-for="(spec, specIndex) in currentVariant.specs" :key="specIndex">
                                        <li class="list-group-item" x-text="spec"></li>
                                    </template>
                                </ul>
                            </div>
                        </template>
                    </div>
                </div>
            @endif
        @endif
    </div>
</section>
