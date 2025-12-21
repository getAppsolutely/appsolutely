<div class="features container">
    @if(isset($displayOptions['title']))
        <div class="features__header text-center mb-5">
            <h2 class="features__title">{{ $displayOptions['title'] }}</h2>
            @if(isset($displayOptions['subtitle']) && $displayOptions['subtitle'])
                <p class="features__subtitle">{{ $displayOptions['subtitle'] }}</p>
            @endif
            @if(isset($displayOptions['descriptions']) && is_array($displayOptions['descriptions']))
                <div class="features__descriptions">
                    @foreach($displayOptions['descriptions'] as $description)
                        <p class="features__description">{{ $description }}</p>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    @if(isset($displayOptions['features']) && count($displayOptions['features']) >= 3)
        <div class="features__content">
            <div class="row g-4">
                <!-- Main Feature (Left Side) -->
                <div class="col-lg-8">
                    @php $mainFeature = $displayOptions['features'][0]; @endphp
                    <div class="features__main-item">
                        @if($mainFeature['type'] === 'image' && $mainFeature['url'])
                            <div class="features__image-wrapper">
                                @if($mainFeature['link'])
                                    <a href="{{ $mainFeature['link'] }}" class="features__image-link">
                                        <img class="lazy features__image features__image--main"
                                             data-src="{{ asset_url($mainFeature['url']) }}"
                                             alt="{{ $mainFeature['image_alt'] ?? '' }}" src="">
                                    </a>
                                @else
                                    <img class="lazy features__image features__image--main"
                                         data-src="{{ asset_url($mainFeature['url']) }}"
                                         alt="{{ $mainFeature['image_alt'] ?? '' }}" src="">
                                @endif

                                @if($mainFeature['title'])
                                    <div class="features__overlay">
                                        <h3 class="features__item-title">{{ $mainFeature['title'] }}</h3>
                                        @if($mainFeature['subtitle'])
                                            <p class="features__item-subtitle">{{ $mainFeature['subtitle'] }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Secondary Features (Right Side) -->
                <div class="col-lg-4">
                    <div class="features__secondary-items">
                        @for($i = 1; $i < 3; $i++)
                            @if(isset($displayOptions['features'][$i]))
                                @php $feature = $displayOptions['features'][$i]; @endphp
                                <div class="features__secondary-item {{ $i === 1 ? 'mb-4' : '' }}">
                                    @if($feature['type'] === 'image' && $feature['url'])
                                        <div class="features__image-wrapper">
                                            @if($feature['link'])
                                                <a href="{{ $feature['link'] }}" class="features__image-link">
                                                    <img class="lazy features__image features__image--secondary"
                                                         data-src="{{ asset_url($feature['url']) }}"
                                                         alt="{{ $feature['image_alt'] ?? '' }}" src="">
                                                </a>
                                            @else
                                                <img class="lazy features__image features__image--secondary"
                                                     data-src="{{ asset_url($feature['url']) }}"
                                                     alt="{{ $feature['image_alt'] ?? '' }}" src="">
                                            @endif

                                            @if($feature['title'] || $feature['subtitle'])
                                                <div class="features__overlay">
                                                    @if($feature['title'])
                                                        <h4 class="features__item-title">{{ $feature['title'] }}</h4>
                                                    @endif
                                                    @if($feature['subtitle'])
                                                        <p class="features__item-subtitle">{{ $feature['subtitle'] }}</p>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
