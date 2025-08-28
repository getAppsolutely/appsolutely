<div class="features-fashionable">
    @if(isset($displayOptions['title']))
        <div class="features-fashionable__header text-center mb-5">
            <div class="container">
                <h2 class="features-fashionable__title">{{ $displayOptions['title'] }}</h2>
                @if(isset($displayOptions['subtitle']) && $displayOptions['subtitle'])
                    <p class="features-fashionable__subtitle">{{ $displayOptions['subtitle'] }}</p>
                @endif
                @if(isset($displayOptions['descriptions']) && is_array($displayOptions['descriptions']))
                    <div class="features-fashionable__descriptions">
                        @foreach($displayOptions['descriptions'] as $description)
                            <p class="features-fashionable__description">{{ $description }}</p>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if(isset($displayOptions['features']) && count($displayOptions['features']) >= 3)
        <div class="features-fashionable__content">
            <div class="container px-0">
                <div class="features-fashionable__grid">
                    @foreach($displayOptions['features'] as $index => $feature)
                        <div class="features-fashionable__item">
                            @if($feature['type'] === 'image' && $feature['url'])
                                <div class="features-fashionable__image-wrapper">
                                    @if($feature['link'])
                                        <a href="{{ $feature['link'] }}" class="features-fashionable__image-link">
                                            <img src="{{ asset_server($feature['url']) }}"
                                                 alt="{{ $feature['title'] ?? '' }}"
                                                 class="features-fashionable__image">
                                        </a>
                                    @else
                                        <img src="{{ asset_server($feature['url']) }}"
                                             alt="{{ $feature['title'] ?? '' }}"
                                             class="features-fashionable__image">
                                    @endif

                                    @if($feature['title'] || $feature['subtitle'])
                                        <div class="features-fashionable__overlay">
                                            @if($feature['title'])
                                                <h3 class="features-fashionable__item-title">{{ $feature['title'] }}</h3>
                                            @endif
                                            @if($feature['subtitle'])
                                                <p class="features-fashionable__item-subtitle">{{ $feature['subtitle'] }}</p>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
