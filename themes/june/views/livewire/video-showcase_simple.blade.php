<section class="video-showcase bg-black pt-5 {{ $style }}">
    <!-- Background Video -->
    <div class="container">
        <video
            class="lazy w-100 h-100 object-fit-cover"
            @if($displayOptions['autoplay']) autoplay @endif
            @if($displayOptions['loop']) loop @endif
            @if($displayOptions['muted']) muted @endif
            @if(!$displayOptions['controls']) style="pointer-events: none;" @else controls @endif
            @if($displayOptions['poster_image']) poster="{{ asset_url($displayOptions['poster_image']) }}" @endif
            playsinline
            preload="none"
        >

            @if($displayOptions['video_url'])
                <source data-src="{{ asset_url($displayOptions['video_url']) }}" type="video/{{ $displayOptions['video_format'] }}">
            @endif

            @if(!empty($displayOptions['fallback_videos']))
                @foreach($displayOptions['fallback_videos'] as $fallback)
                    <source data-src="{{ asset_url($fallback['url']) }}" type="video/{{ $fallback['format'] }}">
                @endforeach
            @endif

            <!-- Fallback message for browsers that don't support video -->
            @if (!empty($displayOptions['fallback_text']))
                <div class="d-flex align-items-center justify-content-center h-100 bg-secondary text-white">
                    <p class="mb-0">{{ $displayOptions['fallback_text'] }}</p>
                </div>
            @endif
        </video>
    </div>
</section>
