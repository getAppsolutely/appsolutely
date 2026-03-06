<section class="hero-banner">
    <div class="hero-banner__wrapper">
        @if (!empty($displayOptions['heroes']))
            @foreach ($displayOptions['heroes'] as $hero)
                <div class="hero-banner hero-banner--{{ $viewStyle ?? 'default' }}">
                    @if (($hero['type'] ?? 'image') === 'video')
                        <div class="hero-banner__video-wrap position-absolute top-0 start-0 w-100 h-100">
                            <video class="lazy w-100 h-100 object-fit-cover" controls preload="none">
                                <source data-src="{{ asset_url($hero['url']) }}" type="video/mp4">
                                {{ __t('Your browser does not support the video tag.') }}
                            </video>
                        </div>
                    @else
                        @if ($viewStyle == 'fullscreen')
                            <div class="hero-banner__image-wrap lazy lazy-bg" data-bg="{{ asset_url($hero['url']) }}">
                            </div>
                        @else
                            <img class="lazy w-100 h-auto d-block" data-src="{{ asset_url($hero['url']) }}"
                                alt="{{ $hero['image_alt'] ?? '' }}" src="">
                        @endif
                    @endif

                    <!-- Content Overlay -->
                    @if (!empty($hero['model']) || !empty($hero['title']) || !empty($hero['subtitle']) || !empty($hero['link']))
                        <div class="hero-banner__caption d-flex align-items-center justify-content-center">
                            <div class="container text-center text-white">
                                <div class="row justify-content-center">
                                    <div class="col-12 col-lg-8 col-xl-6">
                                        @if (!empty($hero['model']) && !empty($hero['title']))
                                            <img src="{{ asset_url($hero['model']) }}"
                                                alt="{{ $hero['image_alt'] ?? '' }}" class="mb-4">
                                        @elseif(empty($hero['model']) && !empty($hero['title']))
                                            <h4 class="display-6 fw-bold mb-3">{{ $hero['title'] }}</h4>
                                        @endif
                                        @if (!empty($hero['subtitle']))
                                            @if (is_string($hero['subtitle']))
                                                <p class="lead mb-4">{{ $hero['subtitle'] }}</p>
                                            @elseif(is_array($hero['subtitle']))
                                                @foreach ($hero['subtitle'] as $subtitle)
                                                    <p class="lead mb-2">{{ $subtitle }}</p>
                                                @endforeach
                                            @endif
                                        @endif
                                        @if (!empty($hero['link']))
                                            <a href="{{ $hero['link'] }}"
                                                class="hero-banner__btn btn btn-light btn-lg fw-semibold fs-6 px-3 py-2 border border-white">
                                                {{ __t('Learn More') }}
                                            </a>
                                        @endif
                                        @if (!empty($hero['test_drive_link']))
                                            <a href="{{ $hero['test_drive_link'] }}"
                                                class="hero-banner__btn btn btn-light btn-lg fw-semibold fs-6 ms-3 px-3 py-2 border border-white">
                                                {{ __t('Test Drive') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Optional Overlay for better text readability -->
                    <div class="hero-banner__overlay position-absolute top-0 start-0 w-100 h-100"></div>

                    @include('components.overseas-model-notice', [
                        'show' => !empty($hero['flag_overseas']),
                    ])
                </div>
            @endforeach
        @endif
    </div>
</section>
