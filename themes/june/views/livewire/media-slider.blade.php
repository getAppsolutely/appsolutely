@php
    $sliderId = 'mediaSlider-' . $this->getId();
@endphp

<div class="media-slider-simple-container">
    {{-- Swiper Carousel --}}
    @if(!empty($displayOptions['slides']))
        <div class="swiper {{ $sliderId }}" data-slider-id="{{ $sliderId }}">
            <div class="swiper-wrapper">
                @foreach($displayOptions['slides'] as $index => $slide)
                    <div class="swiper-slide">
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
                @endforeach
            </div>

            {{-- Pagination (if enabled) --}}
            @if($displayOptions['show_indicators'] && count($displayOptions['slides']) > 1)
                <div class="swiper-pagination"></div>
            @endif
        </div>

        {{-- Navigation Controls at bottom center (if enabled) --}}
        @if($displayOptions['show_controls'] && count($displayOptions['slides']) > 1)
            <div class="simple-slider-controls">
                <div class="swiper-button-prev" data-slider-id="{{ $sliderId }}">
                    <i class="bi bi-chevron-left"></i>
                </div>
                <div class="swiper-button-next" data-slider-id="{{ $sliderId }}">
                    <i class="bi bi-chevron-right"></i>
                </div>
            </div>
        @endif
    @else
        <div class="alert alert-info">No slides available</div>
    @endif
</div>
