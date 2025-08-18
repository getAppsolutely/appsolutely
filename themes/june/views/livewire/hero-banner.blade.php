<div>
    @if (!empty($displayOptions['heroes']))
        @foreach($displayOptions['heroes'] as $hero)
            <div class="hero-banner {{ @$displayOptions['style'] }}">
                @if(($hero['type'] ?? 'image') === 'video')
                    <div class="hero-video-container position-absolute top-0 start-0 w-100 h-100">
                        <video class="w-100 h-100 object-fit-cover" controls loading="lazy">
                            <source src="{{ asset_server($hero['url']) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                @else
                    @if($displayOptions['style'] == 'fullscreen')
                        <div class="hero-image-container"
                             style="background-image: url('{{ asset_server($hero['url']) }}');">
                        </div>
                    @else
                        <img src="{{ asset_server($hero['url']) }}" class="w-100 h-auto d-block" alt="">
                    @endif
                @endif

                <!-- Content Overlay -->
                <div class="hero-banner-caption d-flex align-items-center justify-content-center">
                    <div class="container text-center text-white">
                        <div class="row justify-content-center">
                            <div class="col-12 col-lg-8 col-xl-6">
                                @if(!empty($hero['model']) && !empty($hero['title']))
                                    <img src="{{ asset_server($hero['model']) }}" alt="{{ $hero['title'] }}" class="mb-4">
                                @elseif(empty($hero['model']) && !empty($hero['title']))
                                    <h4 class="display-6 fw-bold mb-3">{{ $hero['title'] }}</h4>
                                @endif
                                @if(!empty($hero['subtitle']))
                                    @if(is_string($hero['subtitle']))
                                        <p class="lead mb-4">{{ $hero['subtitle'] }}</p>
                                    @elseif(is_array($hero['subtitle']))
                                        @foreach($hero['subtitle'] as $subtitle)
                                            <p class="lead mb-1">{{ $subtitle }}</p>
                                        @endforeach
                                    @endif
                                @endif
                                @if(!empty($hero['link']))
                                    <a href="{{ $hero['link'] }}" class="btn btn-outline-light btn-lg px-4 py-2">
                                        <i class="fas fa-play me-2"></i>
                                        Learn More
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Optional Overlay for better text readability -->
                <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"
                     style="opacity: 0.3; z-index: 1;"></div>
            </div>
        @endforeach
    @endif
</div>
