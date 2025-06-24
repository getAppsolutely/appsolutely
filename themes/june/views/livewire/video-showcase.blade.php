<section class="video-showcase position-relative overflow-hidden w-100" style="min-height: 100vh;">
    <!-- Background Video -->
    <div class="video-background position-absolute top-0 start-0 w-100 h-100" style="z-index: 1;">
        <video
            class="w-100 h-100 object-fit-cover"
            @if($data['autoplay']) autoplay @endif
            @if($data['loop']) loop @endif
            @if($data['muted']) muted @endif
            @if(!$data['controls']) style="pointer-events: none;" @else controls @endif
            @if($data['poster_image']) poster="{{ asset_server($data['poster_image']) }}" @endif
            playsinline
        >

            @if($data['video_url'])
                <source src="{{ asset_server($data['video_url']) }}" type="video/{{ $data['video_format'] }}">
            @endif

            @if(!empty($data['fallback_videos']))
                @foreach($data['fallback_videos'] as $fallback)
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
    @if($data['mobile_fallback_image'])
        <div class="mobile-fallback d-block d-md-none position-absolute top-0 start-0 w-100 h-100" style="z-index: 1;">
            <img src="{{ asset_server($data['mobile_fallback_image']) }}" alt="Video fallback" class="w-100 h-100 object-fit-cover">
        </div>
    @endif

    <!-- Overlay -->
    @if($data['overlay_opacity'] > 0)
        <div class="video-overlay position-absolute top-0 start-0 w-100 h-100 bg-dark"
             style="z-index: 2; opacity: {{ $data['overlay_opacity'] }};"></div>
    @endif

    <!-- Content Overlay -->
    <div class="content-overlay position-relative d-flex align-items-center justify-content-center h-100" style="z-index: 3; min-height: 100vh;">
        <div class="container-fluid px-4">
            <div class="row justify-content-{{ $data['text_position'] === 'left' ? 'start' : ($data['text_position'] === 'right' ? 'end' : 'center') }}">
                <div class="col-12 col-lg-8 col-xl-6 text-{{ $data['text_position'] === 'center' ? 'center' : $data['text_position'] }}">
                    @if($data['title'])
                        <h1 class="display-1 fw-bold mb-4 lh-1" style="color: {{ $data['text_color'] }};">
                            {{ $data['title'] }}
                        </h1>
                    @endif

                    @if($data['subtitle'])
                        <h2 class="display-5 mb-4 fw-normal" style="color: {{ $data['text_color'] }}; opacity: 0.9;">
                            {{ $data['subtitle'] }}
                        </h2>
                    @endif

                    @if($data['description'])
                        <p class="lead mb-5 fs-4" style="color: {{ $data['text_color'] }}; opacity: 0.8;">
                            {{ $data['description'] }}
                        </p>
                    @endif

                    @if($data['cta_text'] && $data['cta_link'])
                        <div class="mt-4">
                            <a href="{{ $data['cta_link'] }}"
                               class="btn btn-primary btn-lg px-5 py-3 fs-5 rounded-pill shadow-lg">
                                {{ $data['cta_text'] }}
                                <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
