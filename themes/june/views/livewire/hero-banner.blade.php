<div>
@foreach($heros as $hero)
    <div class="hero-banner position-relative section-full">
        @if(($hero['type'] ?? 'image') === 'video')
            <div class="hero-video-container position-absolute top-0 start-0 w-100 h-100">
                <video class="w-100 h-100 object-fit-cover" controls loading="lazy">
                    <source src="{{ asset_server($hero['url']) }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        @else
            <div class="hero-image-container position-absolute top-0 start-0 w-100 h-100"
                 style="background-image: url('{{ asset_server($hero['url']) }}');
                        background-size: cover;
                        background-position: center center;
                        background-repeat: no-repeat;">
            </div>
        @endif

        <!-- Content Overlay -->
        <div class="hero-banner-caption position-relative d-flex align-items-center justify-content-center">
            <div class="container text-center text-white">
                <div class="row justify-content-center">
                    <div class="col-12 col-lg-8 col-xl-6">
                        @if(!empty($hero['title']))
                            <h2 class="display-4 fw-bold mb-4">{{ $hero['title'] }}</h2>
                        @endif
                        @if(!empty($hero['subtitle']))
                            <p class="lead mb-4">{{ $hero['subtitle'] }}</p>
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
        <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100 bg-dark"
             style="opacity: 0.3; z-index: 1;"></div>
    </div>
@endforeach
</div>
