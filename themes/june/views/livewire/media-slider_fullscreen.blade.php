<div id="mediaSliderCarousel-{{ $this->getId() }}" class="carousel slide section-full" data-bs-ride="carousel">
    <div class="carousel-inner">
        @if (!empty($displayOptions['slides']))
            @foreach($displayOptions['slides'] as $index => $slide)
                <div class="carousel-item @if($index === 0) active @endif">
                    @if(($slide['type'] ?? 'image') === 'video')
                        <video class="d-block w-100" controls>
                            <source src="{{ asset_url($slide['url']) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    @else
                        <img src="{{ asset_url($slide['url']) }}" class="d-block w-100"
                             alt="{{ $slide['title'] ?? '' }}">
                    @endif
                    <div class="carousel-caption d-none d-md-block media-slider-caption">
                        @if(!empty($slide['model']) && !empty($slide['title']))
                            <img src="{{ asset_url($slide['model']) }}" alt="{{ $slide['title'] }}" class="mb-4">
                        @elseif(empty($slide['model']) && !empty($slide['title']))
                            <h2 class="display-6 fw-bold mb-3">{{ $slide['title'] }}</h2>
                        @endif
                        @if(!empty($slide['subtitle']))
                            <p class="lead">{{ $slide['subtitle'] }}</p>
                        @endif
                        @if(!empty($slide['link']))
                            <a href="{{ $slide['link'] }}" class="btn btn-primary media-slider-btn">
                                <i class="fas fa-play me-2"></i>
                                Learn More
                            </a>
                        @endif
                    </div>

                    <!-- Overseas model notice -->
                    @include('components.overseas-model-notice', [
                        'show' => !empty($slide['flag_overseas'])
                    ])
                </div>
            @endforeach
        @endif
    </div>
    <div class="carousel-indicators">
        @if (!empty($displayOptions['slides']))
            @foreach($displayOptions['slides'] as $index => $slide)
                <button type="button" data-bs-target="#mediaSliderCarousel-{{ $this->getId() }}" data-bs-slide-to="{{ $index }}"
                        @if($index === 0) class="active" aria-current="true"
                        @endif aria-label="Slide {{ $index + 1 }}"></button>
            @endforeach
        @endif
    </div>
</div>
