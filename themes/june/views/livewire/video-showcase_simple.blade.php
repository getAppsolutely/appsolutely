<section class="video-showcase bg-black pt-5">
    <!-- Background Video -->
    <div class="container">
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
</section>
