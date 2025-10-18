@php
    $sliderId = 'mediaSlider-' . $this->getId();
@endphp

<div class="media-slider-carousel-container media-slider">
    {{-- Title, Subtitle, Description Section --}}
    @if(!empty($displayOptions['title']) || !empty($displayOptions['subtitle']) || !empty($displayOptions['description']))
        <div class="media-slider-header container">
            @if(!empty($displayOptions['title']))
                <h2 class="media-slider-title">{{ $displayOptions['title'] }}</h2>
            @endif

            @if(!empty($displayOptions['subtitle']))
                <h3 class="media-slider-subtitle">{{ $displayOptions['subtitle'] }}</h3>
            @endif

            @if(!empty($displayOptions['description']))
                <div class="media-slider-description">
                    @if(is_array($displayOptions['description']))
                        @foreach($displayOptions['description'] as $desc)
                            <p>{{ $desc }}</p>
                        @endforeach
                    @else
                        <p>{{ $displayOptions['description'] }}</p>
                    @endif
                </div>
            @endif
        </div>
    @endif

    {{-- Navigation Controls --}}
    @if(!empty($displayOptions['slides']) && count($displayOptions['slides']) > 1)
        <div class="media-slider-controls container">
            <div class="media-slide-caption-container">
                <div class="title-slider">
                    <div class="title-slider-wrapper">
                        @foreach($displayOptions['slides'] as $index => $slide)
                            <div class="title-slide @if($index === 0) active @endif" data-slide-index="{{ $index }}">
                                @if(!empty($slide['title']))
                                    <h4 class="slide-title">{{ $slide['title'] }}</h4>
                                @endif
                                @if(!empty($slide['subtitle']))
                                    <p class="slide-subtitle">{{ $slide['subtitle'] }}</p>
                                @endif
                                @if(!empty($slide['link']))
                                    <a href="{{ $slide['link'] }}" class="btn btn-primary slide-btn">
                                        Learn More
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="media-slider-buttons">
                <div class="swiper-button-prev" data-slider-id="{{ $sliderId }}">
                    <i class="bi bi-chevron-left"></i>
                </div>
                <div class="swiper-button-next" data-slider-id="{{ $sliderId }}">
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div>
        </div>
    @endif

    {{-- Swiper Carousel --}}
    @if(!empty($displayOptions['slides']))
        <div class="container swiper {{ $sliderId }}" data-slider-id="{{ $sliderId }}">
            <div class="swiper-wrapper">
                @foreach($displayOptions['slides'] as $index => $slide)
                    <div class="swiper-slide">
                        <div class="media-slide-content">
                            @if(($slide['type'] ?? 'image') === 'video')
                                <div class="media-slide-video">
                                    <video class="lazy" data-src="{{ asset_url($slide['url']) }}" controls preload="none">
                                        Your browser does not support the video tag.
                                    </video>
                                </div>
                            @else
                                <div class="media-slide-image">
                                    <img class="lazy" 
                                         data-src="{{ asset_url($slide['url']) }}"
                                         alt="{{ $slide['title'] ?? 'Slide ' . ($index + 1) }}">
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="alert alert-info">No slides available</div>
    @endif
</div>
