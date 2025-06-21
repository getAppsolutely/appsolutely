<section class="video-showcase position-relative overflow-hidden w-100" style="min-height: 100vh;">
    <!-- Background Video -->
    <div class="video-background position-absolute top-0 start-0 w-100 h-100" style="z-index: 1;">
        <video 
            class="w-100 h-100 object-fit-cover"
            @if($videoShowcase['autoplay']) autoplay @endif
            @if($videoShowcase['loop']) loop @endif
            @if($videoShowcase['muted']) muted @endif
            @if(!$videoShowcase['controls']) style="pointer-events: none;" @else controls @endif
            @if($videoShowcase['poster_image']) poster="{{ asset_server($videoShowcase['poster_image']) }}" @endif
            playsinline
        >
            @if($videoShowcase['video_url'])
                <source src="{{ asset_server($videoShowcase['video_url']) }}" type="video/{{ $videoShowcase['video_format'] }}">
            @endif
            
            @if(!empty($videoShowcase['fallback_videos']))
                @foreach($videoShowcase['fallback_videos'] as $fallback)
                    <source src="{{ asset_server($fallback['url']) }}" type="video/{{ $fallback['format'] }}">
                @endforeach
            @endif
            
            <!-- Fallback message for browsers that don't support video -->
            <div class="d-flex align-items-center justify-content-center h-100 bg-secondary text-white">
                <p class="mb-0">Your browser does not support the video tag.</p>
            </div>
        </video>
    </div>
    
    <!-- Mobile Fallback Image -->
    @if($videoShowcase['mobile_fallback_image'])
        <div class="mobile-fallback d-block d-md-none position-absolute top-0 start-0 w-100 h-100" style="z-index: 1;">
            <img src="{{ asset_server($videoShowcase['mobile_fallback_image']) }}" alt="Video fallback" class="w-100 h-100 object-fit-cover">
        </div>
    @endif
    
    <!-- Overlay -->
    @if($videoShowcase['overlay_opacity'] > 0)
        <div class="video-overlay position-absolute top-0 start-0 w-100 h-100 bg-dark" 
             style="z-index: 2; opacity: {{ $videoShowcase['overlay_opacity'] }};"></div>
    @endif
    
    <!-- Content Overlay -->
    <div class="content-overlay position-relative d-flex align-items-center justify-content-center h-100" style="z-index: 3; min-height: 100vh;">
        <div class="container-fluid px-4">
            <div class="row justify-content-{{ $videoShowcase['text_position'] === 'left' ? 'start' : ($videoShowcase['text_position'] === 'right' ? 'end' : 'center') }}">
                <div class="col-12 col-lg-8 col-xl-6 text-{{ $videoShowcase['text_position'] === 'center' ? 'center' : $videoShowcase['text_position'] }}">
                    @if($videoShowcase['title'])
                        <h1 class="display-1 fw-bold mb-4 lh-1" style="color: {{ $videoShowcase['text_color'] }};">
                            {{ $videoShowcase['title'] }}
                        </h1>
                    @endif
                    
                    @if($videoShowcase['subtitle'])
                        <h2 class="display-5 mb-4 fw-normal" style="color: {{ $videoShowcase['text_color'] }}; opacity: 0.9;">
                            {{ $videoShowcase['subtitle'] }}
                        </h2>
                    @endif
                    
                    @if($videoShowcase['description'])
                        <p class="lead mb-5 fs-4" style="color: {{ $videoShowcase['text_color'] }}; opacity: 0.8;">
                            {{ $videoShowcase['description'] }}
                        </p>
                    @endif
                    
                    @if($videoShowcase['cta_text'] && $videoShowcase['cta_link'])
                        <div class="mt-4">
                            <a href="{{ $videoShowcase['cta_link'] }}" 
                               class="btn btn-primary btn-lg px-5 py-3 fs-5 rounded-pill shadow-lg">
                                {{ $videoShowcase['cta_text'] }}
                                <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section> 