<section class="container my-5 photo-gallery">
    @php($photos = $displayOptions['photos'] ?? [])

    @if(isset($displayOptions['title']) || isset($displayOptions['subtitle']) || isset($displayOptions['descriptions']))
        <div class="text-center mb-4">
            @if(!empty($displayOptions['title']))
                <h2 class="mb-2">{{ $displayOptions['title'] }}</h2>
            @endif
            @if(!empty($displayOptions['subtitle']))
                <p class="lead mb-2">{{ $displayOptions['subtitle'] }}</p>
            @endif
            @if(!empty($displayOptions['descriptions']))
                @foreach($displayOptions['descriptions'] as $description)
                    <p class="text-muted mb-0">{{ $description }}</p>
                @endforeach
            @endif
        </div>
    @endif

    <div class="mb-4 d-flex flex-wrap gap-2 justify-content-center" id="gallery-filters" aria-label="Photo filters"></div>

    <div class="row g-3" id="gallery-grid" data-photos='@json($photos)'></div>

    <template id="gallery-card-template">
        <div class="col-12 col-sm-6 col-lg-4 gallery-card">
            <div class="card h-100 shadow-sm">
                <div class="ratio ratio-4x3">
                    <img class="card-img-top object-fit-cover" alt="">
                </div>
                <div class="card-body">
                    <h3 class="h5 card-title mb-1"></h3>
                    <p class="card-subtitle text-muted small mb-2"></p>
                    <div class="card-text text-muted small mb-2"></div>
                    <div class="card-price fw-semibold"></div>
                </div>
            </div>
        </div>
    </template>
</section>


