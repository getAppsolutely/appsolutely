<section class="user-manual py-5">
    <div class="user-manual__container container">
        @if (!empty($displayOptions['title']) || !empty($displayOptions['subtitle']))
            <div class="user-manual__header text-center mb-5">
                @if (!empty($displayOptions['title']))
                    <h2 class="user-manual__title mb-2">{{ $displayOptions['title'] }}</h2>
                @endif
                @if (!empty($displayOptions['subtitle']))
                    <p class="user-manual__subtitle text-muted lead">{{ $displayOptions['subtitle'] }}</p>
                @endif
            </div>
        @endif

        @php
            $items = array_values(
                array_filter(
                    $displayOptions['items'] ?? [],
                    fn($item) => !empty($item['title']) || !empty($item['image_src']) || !empty($item['links']),
                ),
            );
        @endphp

        @if (!empty($items))
            <div class="user-manual__grid row g-4">
                @foreach ($items as $item)
                    <div class="user-manual__item col-md-6 col-lg-4">
                        <div class="user-manual__card card h-100 shadow-sm">
                            @if (!empty($item['image_src']))
                                <div class="user-manual__card-image-wrap ratio ratio-4x3">
                                    <img class="user-manual__card-img lazy card-img-top object-fit-cover"
                                        data-src="{{ asset_url($item['image_src']) }}" alt="{{ $item['title'] ?? '' }}"
                                        src="">
                                </div>
                            @endif
                            <div class="user-manual__card-body card-body d-flex flex-column">
                                @if (!empty($item['title']))
                                    <h3 class="user-manual__card-title card-title h5">{{ $item['title'] }}</h3>
                                @endif
                                @if (!empty($item['links']) && is_array($item['links']))
                                    <div class="user-manual__card-links pt-3 d-flex flex-column gap-2">
                                        @foreach ($item['links'] as $link)
                                            @if (!empty($link['url']))
                                                <a href="{{ asset_url($link['url']) }}" class="user-manual__link"
                                                    target="_blank" rel="noopener noreferrer">
                                                    <i class="fas fa-file-pdf" aria-hidden="true"></i>
                                                    {{ $link['label'] ?? 'Download PDF' }}
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
