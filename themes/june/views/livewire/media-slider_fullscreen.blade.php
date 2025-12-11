<div id="mediaSliderCarousel-{{ $this->getId() }}" class="carousel slide section-full" data-bs-ride="carousel">
    <div class="carousel-inner">
        @if (!empty($displayOptions['slides']))
            @foreach ($displayOptions['slides'] as $index => $slide)
                <div class="carousel-item @if ($index === 0) active @endif">
                    @if (($slide['type'] ?? 'image') === 'video')
                        <video class="lazy d-block w-100" data-src="{{ asset_url($slide['url']) }}" controls
                            preload="none">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <img class="lazy d-block w-100" data-src="{{ asset_url($slide['url']) }}"
                            alt="{{ $slide['title'] ?? '' }}">
                    @endif
                    <div class="carousel-caption d-none d-md-block media-slider-caption">
                        @if (!empty($slide['model']) && !empty($slide['title']))
                            <img class="lazy mb-4" data-src="{{ asset_url($slide['model']) }}"
                                alt="{{ $slide['title'] }}">
                        @elseif(empty($slide['model']) && !empty($slide['title']))
                            <h2 class="display-6 fw-bold mb-3">{{ $slide['title'] }}</h2>
                        @endif
                        @if (!empty($slide['subtitle']))
                            <p class="lead">{{ $slide['subtitle'] }}</p>
                        @endif
                        @if (!empty($slide['link']))
                            <a href="{{ $slide['link'] }}" class="btn btn-primary media-slider-btn">
                                Learn More
                            </a>
                        @endif
                    </div>

                    <!-- Overseas model notice -->
                    @include('components.overseas-model-notice', [
                        'show' => !empty($slide['flag_overseas']),
                    ])
                </div>
            @endforeach
        @endif
    </div>

    <!-- Previous/Next Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#mediaSliderCarousel-{{ $this->getId() }}"
        data-bs-slide="prev" aria-label="Previous">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mediaSliderCarousel-{{ $this->getId() }}"
        data-bs-slide="next" aria-label="Next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
    </button>

    <div class="carousel-indicators">
        @if (!empty($displayOptions['slides']))
            @foreach ($displayOptions['slides'] as $index => $slide)
                <button type="button" data-bs-target="#mediaSliderCarousel-{{ $this->getId() }}"
                    data-bs-slide-to="{{ $index }}"
                    @if ($index === 0) class="active" aria-current="true" @endif
                    aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        @endif
    </div>
</div>
