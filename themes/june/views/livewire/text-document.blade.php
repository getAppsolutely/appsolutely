<section class="text-document py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Title -->
                @if($displayOptions['title'] ?? false)
                    <h1 class="display-4 fw-bold text-dark mb-3">
                        {{ $displayOptions['title'] }}
                    </h1>
                @endif

                <!-- Subtitle -->
                @if($displayOptions['subtitle'] ?? false)
                    <p class="lead text-muted mb-4">
                        {{ $displayOptions['subtitle'] }}
                    </p>
                @endif

                <!-- Meta Information -->
                @if(($displayOptions['show_meta'] ?? true) && (($displayOptions['published_date'] ?? false) || ($displayOptions['author'] ?? false)))
                    <div class="text-muted mb-4 pb-3 border-bottom">
                        <small>
                            @if($displayOptions['author'] ?? false)
                                <i class="fas fa-user me-2"></i>
                                <span class="me-3">By {{ $displayOptions['author'] }}</span>
                            @endif
                            @if($displayOptions['published_date'] ?? false)
                                <i class="fas fa-calendar-alt me-2"></i>
                                <time datetime="{{ $displayOptions['published_date'] }}">
                                    Published: {{ \Carbon\Carbon::parse($displayOptions['published_date'])->format('F j, Y') }}
                                </time>
                            @endif
                        </small>
                    </div>
                @endif

                <!-- Content -->
                @if($displayOptions['content'] ?? false)
                    <div class="content-body">
                        {!! $displayOptions['content'] !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
