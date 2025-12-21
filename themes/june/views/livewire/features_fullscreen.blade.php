<div class="features-fullscreen">
    @if(isset($displayOptions['title']))
        <div class="features-fullscreen__header text-center">
            <div class="container">
                <h2 class="features-fullscreen__title">{{ $displayOptions['title'] }}</h2>
                @if(isset($displayOptions['subtitle']) && $displayOptions['subtitle'])
                    <p class="features-fullscreen__subtitle">{{ $displayOptions['subtitle'] }}</p>
                @endif
                @if(isset($displayOptions['descriptions']) && is_array($displayOptions['descriptions']))
                    <div class="features-fullscreen__descriptions">
                        @foreach($displayOptions['descriptions'] as $description)
                            <p class="features-fullscreen__description">{{ $description }}</p>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if(isset($displayOptions['features']) && count($displayOptions['features']) > 0)
        <div class="features-fullscreen__content">
            <div class="features-fullscreen__grid">
                @foreach($displayOptions['features'] as $index => $feature)
                    <div class="features-fullscreen__item">
                        @if($feature['type'] === 'image' && $feature['url'])
                            <div class="features-fullscreen__image-wrapper">
                                @if($feature['link'])
                                    <a href="{{ $feature['link'] }}" class="features-fullscreen__image-link">
                                        <img class="lazy features-fullscreen__image"
                                             data-src="{{ asset_url($feature['url']) }}"
                                             alt="{{ $feature['image_alt'] ?? '' }}" src="">
                                    </a>
                                @else
                                    <img class="lazy features-fullscreen__image"
                                         data-src="{{ asset_url($feature['url']) }}"
                                         alt="{{ $feature['image_alt'] ?? '' }}" src="">
                                @endif

                                @if($feature['title'] || $feature['subtitle'])
                                    <div class="features-fullscreen__overlay">
                                        @if($feature['title'])
                                            <h3 class="features-fullscreen__item-title">{{ $feature['title'] }}</h3>
                                        @endif
                                        @if($feature['subtitle'])
                                            <p class="features-fullscreen__item-subtitle">{{ $feature['subtitle'] }}</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
