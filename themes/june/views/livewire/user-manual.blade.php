<section class="user-manual py-5">
    <div class="container">
        @if (!empty($displayOptions['title']) || !empty($displayOptions['subtitle']))
            <div class="text-center mb-5">
                @if (!empty($displayOptions['title']))
                    <h2 class="user-manual__title mb-2">{{ $displayOptions['title'] }}</h2>
                @endif
                @if (!empty($displayOptions['subtitle']))
                    <p class="user-manual__subtitle text-muted lead">{{ $displayOptions['subtitle'] }}</p>
                @endif
            </div>
        @endif

        @php
            $items = array_values(array_filter($displayOptions['items'] ?? [], fn ($item) => !empty($item['title']) || !empty($item['image_src']) || !empty($item['links'])));
        @endphp

        @if (!empty($items))
            <div class="row g-4">
                @foreach ($items as $item)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm">
                            @if (!empty($item['image_src']))
                                <div class="ratio ratio-4x3">
                                    <img
                                        class="lazy card-img-top object-fit-cover"
                                        data-src="{{ asset_url($item['image_src']) }}"
                                        alt="{{ $item['title'] ?? '' }}"
                                        src=""
                                    >
                                </div>
                            @endif
                            <div class="card-body d-flex flex-column">
                                @if (!empty($item['title']))
                                    <h3 class="card-title h5">{{ $item['title'] }}</h3>
                                @endif
                                @if (!empty($item['links']) && is_array($item['links']))
                                    <div class="pt-3 d-flex flex-column gap-2">
                                        @foreach ($item['links'] as $link)
                                            @if (!empty($link['url']))
                                                <a
                                                    href="{{ asset_url($link['url']) }}"
                                                    class="user-manual__link"
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                >
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
