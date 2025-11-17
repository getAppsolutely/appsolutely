@php
    $product = $displayOptions['product'] ?? [];
    $variants = $product['variants'] ?? [];
    $currentVariant = $variants[$selectedVariantIndex] ?? null;
    $currentColor = null;

    if ($currentVariant && $selectedColorIndex !== null && isset($currentVariant['colors'][$selectedColorIndex])) {
        $currentColor = $currentVariant['colors'][$selectedColorIndex];
    }
@endphp

<section class="product-variant-block py-5" wire:key="product-variant-{{ $this->getId() }}">
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
                            <span class="me-3"><strong>Body Type:</strong> {{ $product['common']['body_type'] }}</span>
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
                                <button class="nav-link {{ $selectedVariantIndex === $index ? 'active' : '' }}"
                                    type="button" wire:click="switchVariant({{ $index }})" role="tab"
                                    aria-selected="{{ $selectedVariantIndex === $index ? 'true' : 'false' }}">
                                    {{ $variant['name'] ?? 'Variant ' . ($index + 1) }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                @if ($currentVariant)
                    <div class="row g-4">
                        <!-- Left Column: Images -->
                        <div class="col-lg-7">
                            <!-- Main Image -->
                            @if ($currentColor && !empty($currentColor['images']))
                                <div class="main-image-container mb-4">
                                    <img src="{{ asset_url($currentColor['images'][0]) }}"
                                        alt="{{ $currentColor['name'] ?? 'Product Image' }}"
                                        class="img-fluid rounded shadow-sm w-100 product-main-image"
                                        style="max-height: 500px; object-fit: contain;">
                                </div>
                            @endif

                            <!-- Color Selection -->
                            @if (!empty($currentVariant['colors']))
                                <div class="color-selection mb-4">
                                    <h6 class="mb-3 fw-semibold">Select Color</h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach ($currentVariant['colors'] as $colorIndex => $color)
                                            <button type="button"
                                                class="color-option btn {{ $selectedColorIndex === $colorIndex ? 'active' : '' }}"
                                                wire:click="selectColor({{ $colorIndex }})"
                                                style="
                                                    width: 50px;
                                                    height: 50px;
                                                    border-radius: 50%;
                                                    background-color: {{ $color['code'] ?? '#ccc' }};
                                                    border: 3px solid {{ $selectedColorIndex === $colorIndex ? '#007bff' : 'transparent' }};
                                                    position: relative;
                                                "
                                                title="{{ $color['name'] ?? 'Color ' . ($colorIndex + 1) }}"
                                                data-bs-toggle="tooltip">
                                                @if ($selectedColorIndex === $colorIndex)
                                                    <i
                                                        class="fas fa-check text-white position-absolute top-50 start-50 translate-middle"></i>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                    @if ($currentColor)
                                        <p class="mt-2 mb-0 text-muted">
                                            <strong>Selected: </strong>{{ $currentColor['name'] ?? 'Unnamed Color' }}
                                        </p>
                                    @endif
                                </div>
                            @endif

                            <!-- Additional Images -->
                            @if ($currentColor && !empty($currentColor['images']) && count($currentColor['images']) > 1)
                                <div class="additional-images mt-4">
                                    <h6 class="mb-3 fw-semibold">More Images</h6>
                                    <div class="row g-2">
                                        @foreach (array_slice($currentColor['images'], 1) as $imageIndex => $image)
                                            <div class="col-4 col-md-3">
                                                <img src="{{ asset_url($image) }}"
                                                    alt="Product Image {{ $imageIndex + 2 }}"
                                                    class="img-fluid rounded shadow-sm w-100 product-thumbnail"
                                                    style="height: 100px; object-fit: cover; cursor: pointer;">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Right Column: Price & Specs -->
                        <div class="col-lg-5">
                            <!-- Variant Name & Price -->
                            <div class="variant-info mb-4">
                                <h2 class="h3 fw-bold mb-2">{{ $currentVariant['name'] ?? 'Variant' }}</h2>
                                @if (!empty($currentVariant['price']))
                                    <div class="price-section mb-3">
                                        <span class="h4 fw-bold text-primary">
                                            ${{ is_int($currentVariant['price']) ? number_format($currentVariant['price'], 0) : $currentVariant['price'] }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Specifications -->
                            @if (!empty($currentVariant['specs']))
                                <div class="specifications-section mb-4">
                                    <h5 class="fw-bold mb-3">Specifications</h5>
                                    <ul class="list-group">
                                        @foreach ($currentVariant['specs'] as $spec)
                                            <li class="list-group-item">{{ $spec }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        @endif
    </div>
</section>
