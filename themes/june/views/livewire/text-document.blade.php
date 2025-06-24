<section class="text-document py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Title -->
                @if($data['title'] ?? false)
                    <h1 class="display-4 fw-bold text-dark mb-3">
                        {{ $data['title'] }}
                    </h1>
                @endif

                <!-- Subtitle -->
                @if($data['subtitle'] ?? false)
                    <p class="lead text-muted mb-4">
                        {{ $data['subtitle'] }}
                    </p>
                @endif

                <!-- Meta Information -->
                @if(($data['show_meta'] ?? true) && (($data['published_date'] ?? false) || ($data['author'] ?? false)))
                    <div class="text-muted mb-4 pb-3 border-bottom">
                        <small>
                            @if($data['author'] ?? false)
                                <i class="fas fa-user me-2"></i>
                                <span class="me-3">By {{ $data['author'] }}</span>
                            @endif
                            @if($data['published_date'] ?? false)
                                <i class="fas fa-calendar-alt me-2"></i>
                                <time datetime="{{ $data['published_date'] }}">
                                    Published: {{ \Carbon\Carbon::parse($data['published_date'])->format('F j, Y') }}
                                </time>
                            @endif
                        </small>
                    </div>
                @endif

                <!-- Content -->
                @if($data['content'] ?? false)
                    <div class="content-body">
                        {!! $data['content'] !!}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section> 