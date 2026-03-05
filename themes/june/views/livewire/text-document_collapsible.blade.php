<section class="text-document-collapsible py-5">
    <div class="text-document-collapsible__wrapper container">
        <div class="text-document-collapsible__row row justify-content-center">
            <div class="text-document-collapsible__col col-lg-8">
                <div class="text-document-collapsible__container">
                    <!-- Collapsible Header -->
                    <button type="button"
                        class="text-document-collapsible__header w-100 text-start border-0 bg-transparent p-0"
                        data-bs-toggle="collapse" data-bs-target="#textDocumentCollapsible{{ $blockId ?? 'default' }}"
                        aria-expanded="false" aria-controls="textDocumentCollapsible{{ $blockId ?? 'default' }}">

                        <div class="text-document-collapsible__header-content">
                            <!-- Title -->
                            @if ($displayOptions['title'] ?? false)
                                <h2 class="text-document-collapsible__title h4 fw-bold text-dark mb-0">
                                    {{ $displayOptions['title'] }}
                                </h2>
                            @endif

                            <!-- Subtitle -->
                            @if ($displayOptions['subtitle'] ?? false)
                                <p class="text-document-collapsible__subtitle text-muted mb-0 small">
                                    {{ $displayOptions['subtitle'] }}
                                </p>
                            @endif
                        </div>

                        <!-- Arrow Icon -->
                        <div class="text-document-collapsible__arrow">
                            <i class="bi bi-chevron-down text-primary" aria-hidden="true"></i>
                        </div>
                    </button>

                    <!-- Collapsible Content -->
                    <div class="collapse text-document-collapsible__content"
                        id="textDocumentCollapsible{{ $blockId ?? 'default' }}">

                        <div class="text-document-collapsible__body">
                            <!-- Meta Information -->
                            @if (
                                ($displayOptions['show_meta'] ?? true) &&
                                    (($displayOptions['published_date'] ?? false) || ($displayOptions['author'] ?? false)))
                                <div class="text-document-collapsible__meta text-muted mb-3 pb-3 border-bottom">
                                    <small>
                                        @if ($displayOptions['author'] ?? false)
                                            <i class="bi bi-person me-2" aria-hidden="true"></i>
                                            <span class="me-3">By {{ $displayOptions['author'] }}</span>
                                        @endif
                                        @if ($displayOptions['published_date'] ?? false)
                                            <i class="bi bi-calendar3 me-2" aria-hidden="true"></i>
                                            <time datetime="{{ $displayOptions['published_date'] }}">
                                                Published:
                                                {{ \Carbon\Carbon::parse($displayOptions['published_date'])->format('F j, Y') }}
                                            </time>
                                        @endif
                                    </small>
                                </div>
                            @endif

                            <!-- Content -->
                            @if ($displayOptions['content'] ?? false)
                                <div class="text-document-collapsible__content-body markdown">
                                    {!! md2html($displayOptions['content']) !!}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
