<div>
@foreach($heros as $hero)
    <div class="hero-banner section-full">
        @if(($hero['type'] ?? 'image') === 'video')
            <video class="d-block w-100" controls loading="lazy">
                <source src="{{ asset_server($hero['url']) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        @else
            <img src="{{ asset_server($hero['url']) }}" 
                 class="d-block w-100" 
                 alt="{{ $hero['title'] ?? '' }}"
                 loading="lazy"
                 decoding="async">
        @endif
        <div class="hero-banner-caption">
            @if(!empty($hero['title']))
                <h2 class="display-4 fw-bold">{{ $hero['title'] }}</h2>
            @endif
            @if(!empty($hero['subtitle']))
                <p class="lead">{{ $hero['subtitle'] }}</p>
            @endif
            @if(!empty($hero['link']))
                <a href="{{ $hero['link'] }}" class="btn btn-outline-light-primary hero-banner-btn">
                    <i class="fas fa-play me-2"></i>
                    Learn More
                </a>
            @endif
        </div>
    </div>
@endforeach
</div>
