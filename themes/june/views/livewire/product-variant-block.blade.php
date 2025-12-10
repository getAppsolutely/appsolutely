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

    // Pre-compute first variant and color for SSR fallback (shown before Alpine loads)
    $firstVariant = $variants[0] ?? null;
    $firstColor = $firstVariant['colors'][0] ?? null;
    $firstImage = $firstColor['images'][0] ?? null;
@endphp

<section class="product-variant-block py-5" wire:key="product-variant-{{ $this->getId() }}">
    <div class="container">
        @if (empty($product))
            <div class="alert alert-warning">No product data configured</div>
        @elseif (empty($variants))
            <!-- Product Header -->
            <div class="product-header mb-4 text-center">
                <h1 class="h2 fw-bold mb-2">{{ $product['name'] ?? 'Product' }}</h1>
            </div>
            <div class="alert alert-info">No variants available</div>
        @else
            {{-- Alpine.js Enhanced Component --}}
            <div x-data="{
                selectedVariantIndex: 0,
                selectedColorIndex: 0,
                product: null,
                currentVariant: null,
                currentColor: null,
                initialized: false,

                init() {
                    try {
                        // Parse product data from script element (more reliable than inline JSON)
                        const dataEl = this.$el.querySelector('script[data-product]');
                        if (dataEl && dataEl.textContent) {
                            this.product = JSON.parse(dataEl.textContent.trim());
                        }

                        if (this.product?.variants?.length) {
                            this.currentVariant = this.product.variants[0];
                            if (this.currentVariant?.colors?.length) {
                                this.currentColor = this.currentVariant.colors[0];
                            }
                        }

                        this.initialized = true;
                    } catch (e) {
                        console.error('[ProductVariantBlock] Init error:', e);
                        // Force initialized even on error to show SSR content
                        this.initialized = true;
                    }
                },

                switchVariant(index) {
                    if (!this.product?.variants?.[index]) return;
                    this.selectedVariantIndex = index;
                    this.selectedColorIndex = 0;
                    this.currentVariant = this.product.variants[index];
                    this.currentColor = this.currentVariant?.colors?.[0] || null;
                },

                selectColor(index) {
                    if (!this.currentVariant?.colors?.[index]) return;
                    this.selectedColorIndex = index;
                    this.currentColor = this.currentVariant.colors[index];
                },

                getFormattedPrice() {
                    const price = this.currentVariant?.price;
                    if (price === null || price === undefined || price === '') return null;
                    return typeof price === 'number' ? price.toLocaleString() : price;
                }
            }">
                {{-- Hidden data element for reliable JSON parsing --}}
                <script type="application/json" data-product>@json($product)</script>

                <!-- Product Header (Static - always visible) -->
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
                                <span class="me-3"><strong>Body Type:</strong> {{ $product['common']['body_type'] }}</span>
                            @endif
                            @if (!empty($product['common']['platform']))
                                <span class="me-3"><strong>Platform:</strong> {{ $product['common']['platform'] }}</span>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Variant Tabs (Static names, Alpine handles active state) -->
                <div class="variant-tabs mb-4">
                    <ul class="nav nav-tabs justify-content-center" role="tablist">
                        @foreach ($variants as $index => $variant)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}"
                                    :class="{ 'active': selectedVariantIndex === {{ $index }} }"
                                    type="button"
                                    @click="switchVariant({{ $index }})"
                                    role="tab">
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
                        {{-- SSR Fallback: Show first image immediately (hidden once Alpine loads) --}}
                        @if ($firstImage)
                            <div class="main-image-container mb-4" x-show="!initialized">
                                <img src="{{ $firstImage }}"
                                    alt="{{ $firstColor['name'] ?? 'Product Image' }}"
                                    class="img-fluid rounded shadow-sm w-100"
                                    style="max-height: 500px; object-fit: contain;">
                            </div>
                        @endif

                        {{-- Alpine Dynamic Image --}}
                        <div class="main-image-container mb-4" x-show="initialized && currentColor?.images?.length" x-cloak>
                            <img :src="currentColor?.images?.[0] || ''"
                                :alt="currentColor?.name || 'Product Image'"
                                class="img-fluid rounded shadow-sm w-100 product-main-image"
                                style="max-height: 500px; object-fit: contain;">
                        </div>

                        {{-- SSR Fallback: Color Selection --}}
                        @if (!empty($firstVariant['colors']))
                            <div class="color-selection mb-4" x-show="!initialized">
                                <h6 class="mb-3 fw-semibold">Select Color</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($firstVariant['colors'] as $colorIndex => $color)
                                        <button type="button" class="color-option p-0 border-0"
                                            style="width: 50px; height: 50px; border-radius: 50%; background: {{ $color['code'] ?? '#ccc' }}; outline: 3px solid {{ $colorIndex === 0 ? '#007bff' : 'transparent' }}; outline-offset: -3px; position: relative; cursor: pointer;"
                                            title="{{ $color['name'] ?? 'Color ' . ($colorIndex + 1) }}">
                                            @if ($colorIndex === 0)
                                                <i class="fas fa-check text-white position-absolute top-50 start-50 translate-middle"></i>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                                <p class="mt-2 mb-0 text-muted">
                                    <strong>Selected: </strong>{{ $firstColor['name'] ?? 'Unnamed Color' }}
                                </p>
                            </div>
                        @endif

                        {{-- Alpine Dynamic Color Selection --}}
                        <div class="color-selection mb-4" x-show="initialized && currentVariant?.colors?.length" x-cloak>
                            <h6 class="mb-3 fw-semibold">Select Color</h6>
                            <div class="d-flex flex-wrap gap-2">
                                <template x-for="(color, colorIndex) in currentVariant?.colors || []" :key="colorIndex">
                                    <button type="button" class="color-option p-0 border-0"
                                        :class="{ 'active': selectedColorIndex === colorIndex }"
                                        @click="selectColor(colorIndex)"
                                        :style="`width: 50px; height: 50px; border-radius: 50%; background: ${color.code || '#ccc'}; outline: 3px solid ${selectedColorIndex === colorIndex ? '#007bff' : 'transparent'}; outline-offset: -3px; position: relative; cursor: pointer;`"
                                        :title="color.name || 'Color ' + (colorIndex + 1)">
                                        <i x-show="selectedColorIndex === colorIndex"
                                            class="fas fa-check text-white position-absolute top-50 start-50 translate-middle"></i>
                                    </button>
                                </template>
                            </div>
                            <p class="mt-2 mb-0 text-muted" x-show="currentColor">
                                <strong>Selected: </strong><span x-text="currentColor?.name || 'Unnamed Color'"></span>
                            </p>
                        </div>

                        {{-- Alpine Dynamic Additional Images --}}
                        <div class="additional-images mt-4" x-show="initialized && currentColor?.images?.length > 1" x-cloak>
                            <h6 class="mb-3 fw-semibold">More Images</h6>
                            <div class="row g-2">
                                <template x-for="(image, imageIndex) in (currentColor?.images || []).slice(1)" :key="imageIndex">
                                    <div class="col-4 col-md-3">
                                        <img :src="image" :alt="'Product Image ' + (imageIndex + 2)"
                                            class="img-fluid rounded shadow-sm w-100 product-thumbnail"
                                            style="height: 100px; object-fit: cover; cursor: pointer;"
                                            @click="$el.closest('.col-lg-7').querySelector('.product-main-image').src = image">
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Price & Specs -->
                    <div class="col-lg-5">
                        {{-- SSR Fallback: Variant Info --}}
                        @if ($firstVariant)
                            <div class="variant-info mb-4" x-show="!initialized">
                                <h2 class="h3 fw-bold mb-2">{{ $firstVariant['name'] ?? 'Variant' }}</h2>
                                @if (!empty($firstVariant['price']))
                                    <div class="price-section mb-3">
                                        <span class="h4 fw-bold text-primary">
                                            ${{ is_numeric($firstVariant['price']) ? number_format($firstVariant['price']) : $firstVariant['price'] }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Alpine Dynamic Variant Info --}}
                        <div class="variant-info mb-4" x-show="initialized" x-cloak>
                            <h2 class="h3 fw-bold mb-2" x-text="currentVariant?.name || 'Variant'"></h2>
                            <div class="price-section mb-3" x-show="getFormattedPrice() !== null">
                                <span class="h4 fw-bold text-primary">
                                    $<span x-text="getFormattedPrice()"></span>
                                </span>
                            </div>
                        </div>

                        {{-- SSR Fallback: Specifications --}}
                        @if (!empty($firstVariant['specs']))
                            <div class="specifications-section mb-4" x-show="!initialized">
                                <h5 class="fw-bold mb-3">Specifications</h5>
                                <ul class="list-group">
                                    @foreach ($firstVariant['specs'] as $spec)
                                        <li class="list-group-item">{{ $spec }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Alpine Dynamic Specifications --}}
                        <div class="specifications-section mb-4" x-show="initialized && currentVariant?.specs?.length" x-cloak>
                            <h5 class="fw-bold mb-3">Specifications</h5>
                            <ul class="list-group">
                                <template x-for="(spec, specIndex) in currentVariant?.specs || []" :key="specIndex">
                                    <li class="list-group-item" x-text="spec"></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>
