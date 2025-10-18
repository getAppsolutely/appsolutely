<section class="container my-5 feature-rows">
    @if(isset($displayOptions['title']) || isset($displayOptions['subtitle']) || isset($displayOptions['description']))
        <div class="text-center mb-5">
            @if(!empty($displayOptions['title']))
                <h2 class="mb-3">{{ $displayOptions['title'] }}</h2>
            @endif
            @if(!empty($displayOptions['subtitle']))
                <p class="lead mb-2">{{ $displayOptions['subtitle'] }}</p>
            @endif
            @if(!empty($displayOptions['descriptions']))
                @foreach($displayOptions['descriptions'] as $description)
                <p class="text-muted">{{ $description }}</p>
                @endforeach
            @endif
        </div>
    @endif

    @php($features = $displayOptions['features'] ?? [])
    @if(is_array($features) && count($features))
        @foreach($features as $index => $feature)
            @php($isImageLeft = ($index % 2) === 0)
            <div class="row align-items-center g-5 mb-5 feature-row">
                <div class="col-md-6 {{ $isImageLeft ? 'order-1 order-md-1' : 'order-1 order-md-2' }}">
                    @if(($feature['type'] ?? 'image') === 'image' && !empty($feature['url']))
                        <div class="ratio ratio-16x9 rounded overflow-hidden shadow-sm">
                            @if(!empty($feature['link']))
                                <a href="{{ $feature['link'] }}" class="d-block">
                                    <img
                                        class="lazy w-100 h-100 object-fit-cover"
                                        data-src="{{ asset_url($feature['url']) }}"
                                        alt="{{ $feature['title'] ?? '' }}"
                                    >
                                </a>
                            @else
                                <img
                                    class="lazy w-100 h-100 object-fit-cover"
                                    data-src="{{ asset_url($feature['url']) }}"
                                    alt="{{ $feature['title'] ?? '' }}"
                                >
                            @endif
                        </div>
                    @endif
                </div>

                <div class="col-md-6 {{ $isImageLeft ? 'order-2 order-md-2' : 'order-2 order-md-1' }}">
                    <div class="px-md-4">
                        @if(!empty($feature['eyebrow']))
                            <div class="text-uppercase small text-muted mb-2">{{ $feature['eyebrow'] }}</div>
                        @endif

                        @if(!empty($feature['title']))
                            <h3 class="h2 mb-3">{{ $feature['title'] }}</h3>
                        @endif

                        @if(!empty($feature['subtitle']))
                            <p class="lead mb-3">{{ $feature['subtitle'] }}</p>
                        @endif

                        @if(!empty($feature['description']))
                            <div class="text-muted mb-4">{!! $feature['description'] !!}</div>
                        @endif

                        @if(!empty($feature['button_text']) && !empty($feature['link']))
                            <a href="{{ $feature['link'] }}" class="btn btn-dark">
                                {{ $feature['button_text'] }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</section>


