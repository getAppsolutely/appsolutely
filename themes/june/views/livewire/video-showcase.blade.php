<section class="video-showcase position-relative overflow-hidden w-100" style="min-height: 100vh;">
    <!-- Background Video -->
    <div class="video-background position-absolute top-0 start-0 w-100 h-100" style="z-index: 1;">
        <video
            class="w-100 h-100 object-fit-cover"
            @if($displayOptions['autoplay']) autoplay @endif
            @if($displayOptions['loop']) loop @endif
            @if($displayOptions['muted']) muted @endif
            @if(!$displayOptions['controls']) style="pointer-events: none;" @else controls @endif
            @if($displayOptions['poster_image']) poster="{{ asset_url($displayOptions['poster_image']) }}" @endif
            playsinline
        >

            @if($displayOptions['video_url'])
                <source src="{{ asset_url($displayOptions['video_url']) }}" type="video/{{ $displayOptions['video_format'] }}">
            @endif

            @if(!empty($displayOptions['fallback_videos']))
                @foreach($displayOptions['fallback_videos'] as $fallback)
                    <source src="{{ asset_url($fallback['url']) }}" type="video/{{ $fallback['format'] }}">
                @endforeach
            @endif

            <!-- Fallback message for browsers that don't support video -->
            <div class="d-flex align-items-center justify-content-center h-100 bg-secondary text-white">
                <p class="mb-0">Your browser does not support the video tag.</p>
            </div>
        </video>
    </div>

    <!-- Mobile Fallback Image -->
    @if($displayOptions['mobile_fallback_image'])
        <div class="mobile-fallback d-block d-md-none position-absolute top-0 start-0 w-100 h-100" style="z-index: 1;">
            <img src="{{ asset_url($displayOptions['mobile_fallback_image']) }}" alt="Video fallback" class="w-100 h-100 object-fit-cover">
        </div>
    @endif

    <!-- Overlay -->
    @if($displayOptions['overlay_opacity'] > 0)
        <div class="video-overlay position-absolute top-0 start-0 w-100 h-100 bg-dark"
             style="z-index: 2; opacity: {{ $displayOptions['overlay_opacity'] }};"></div>
    @endif

    <!-- Content Overlay -->
    <div class="content-overlay position-relative d-flex align-items-center justify-content-center h-100" style="z-index: 3; min-height: 100vh;">
        <div class="container-fluid px-4">
            <div class="row justify-content-{{ $displayOptions['text_position'] === 'left' ? 'start' : ($displayOptions['text_position'] === 'right' ? 'end' : 'center') }}">
                <div class="col-12 col-lg-8 col-xl-6 text-{{ $displayOptions['text_position'] === 'center' ? 'center' : $displayOptions['text_position'] }}">
                    @if($displayOptions['title'])
                        <h1 class="display-3 fw-bold mb-4 lh-1" style="color: {{ $displayOptions['text_color'] }};">
                            {{ $displayOptions['title'] }}
                        </h1>
                    @endif

                    @if($displayOptions['subtitle'])
                        <h2 class="display-7 mb-4 fw-light" style="color: {{ $displayOptions['text_color'] }}; opacity: 0.9;">
                            {{ $displayOptions['subtitle'] }}
                        </h2>
                    @endif

                    @if($displayOptions['description'])
                        <p class="lead mb-5 fs-4" style="color: {{ $displayOptions['text_color'] }}; opacity: 0.8;">
                            {{ $displayOptions['description'] }}
                        </p>
                    @endif

                    @if($displayOptions['cta_text'] && $displayOptions['cta_link'])
                        <div class="mt-4">
                            <a href="{{ $displayOptions['cta_link'] }}"
                               class="btn btn-primary btn-lg px-5 py-3 fs-5 rounded-pill shadow-lg">
                                {{ $displayOptions['cta_text'] }}
                                <i class="bi bi-arrow-right ms-2"></i>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('components.overseas-model-notice', [
        'show' => !empty($displayOptions['flag_overseas'])
    ])
</section>
