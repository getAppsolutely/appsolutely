<section class="features-rows container my-5">
    @if (isset($displayOptions['title']) || isset($displayOptions['subtitle']) || isset($displayOptions['description']))
        <div class="features-rows__header text-center mb-5">
            @if (!empty($displayOptions['title']))
                <h2 class="features-rows__title mb-3">{{ $displayOptions['title'] }}</h2>
            @endif
            @if (!empty($displayOptions['subtitle']))
                <p class="features-rows__subtitle lead mb-2">{{ $displayOptions['subtitle'] }}</p>
            @endif
            @if (!empty($displayOptions['descriptions']))
                @foreach ($displayOptions['descriptions'] as $description)
                    <p class="features-rows__description text-muted">{{ $description }}</p>
                @endforeach
            @endif
        </div>
    @endif

    @php($features = $displayOptions['features'] ?? [])
    @if (is_array($features) && count($features))
        @foreach ($features as $index => $feature)
            @php($isImageLeft = $index % 2 === 0)
            <article class="features-rows__row row align-items-center g-5 mb-5">
                <div
                    class="features-rows__image-wrap col-md-6 {{ $isImageLeft ? 'order-1 order-md-1' : 'order-1 order-md-2' }}">
                    @if (($feature['type'] ?? 'image') === 'image' && !empty($feature['url']))
                        <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm">
                            @if (!empty($feature['link']))
                                <a href="{{ $feature['link'] }}" class="d-block">
                                    <img class="lazy w-100 h-100 object-fit-cover"
                                        data-src="{{ asset_url($feature['url']) }}"
                                        alt="{{ $feature['image_alt'] ?? ($feature['title'] ?? '') }}">
                                </a>
                            @else
                                <img class="lazy w-100 h-100 object-fit-cover"
                                    data-src="{{ asset_url($feature['url']) }}"
                                    alt="{{ $feature['image_alt'] ?? ($feature['title'] ?? '') }}">
                            @endif
                        </div>
                    @endif
                </div>

                <div
                    class="features-rows__content col-md-6 {{ $isImageLeft ? 'order-2 order-md-2' : 'order-2 order-md-1' }}">
                    <div class="px-md-4">
                        @if (!empty($feature['eyebrow']))
                            <div class="features-rows__eyebrow text-uppercase small text-muted mb-2">
                                {{ $feature['eyebrow'] }}</div>
                        @endif

                        @if (!empty($feature['title']))
                            <h3 class="features-rows__item-title h2 mb-3">{{ $feature['title'] }}</h3>
                        @endif

                        @if (!empty($feature['subtitle']))
                            <p class="features-rows__item-subtitle lead mb-3">{{ $feature['subtitle'] }}</p>
                        @endif

                        @if (!empty($feature['description']))
                            <div class="features-rows__item-description text-muted mb-4">{!! $feature['description'] !!}</div>
                        @endif

                        @if (!empty($feature['button_text']) && !empty($feature['link']))
                            <a href="{{ $feature['link'] }}" class="features-rows__button btn btn-dark">
                                {{ $feature['button_text'] }}
                            </a>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    @endif
</section>
