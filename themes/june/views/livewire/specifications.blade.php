<section class="specifications-section py-5">
    <div class="container">
        <!-- Section Header -->
        @if($displayOptions['title'] || $displayOptions['subtitle'] || $displayOptions['description'])
            <div class="text-center mb-5">
                @if($displayOptions['title'])
                    <h2 class="display-5 fw-bold mb-3">
                        {{ $displayOptions['title'] }}
                    </h2>
                @endif

                @if($displayOptions['subtitle'])
                    <h3 class="h4 mb-4">
                        {{ $displayOptions['subtitle'] }}
                    </h3>
                @endif

                @if($displayOptions['description'])
                    <p class="lead">
                        {{ $displayOptions['description'] }}
                    </p>
                @endif
            </div>
        @endif

        <!-- Specifications Content -->
        @if(!empty($displayOptions['specifications']))
            @if($displayOptions['layout'] === 'grid')
                <!-- Grid Layout -->
                <div class="row g-4">
                    @foreach($displayOptions['specifications'] as $spec)
                        <div class="col-md-{{ 12 / $displayOptions['columns'] }}">
                            <div class="specification-item p-4 h-100 border rounded-3 shadow-sm">
                                <div class="specification-content">
                                    <h5 class="specification-label fw-semibold mb-2">
                                        @if($spec['icon'] ?? false)
                                            <i class="{{ $spec['icon'] }} me-2"></i>
                                        @endif
                                        {{ $spec['label'] }}
                                    </h5>

                                    <div class="specification-value">
                                        {{ $spec['value'] }}
                                        @if($spec['unit'] ?? false)
                                            <span class="text-muted">{{ $spec['unit'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            @elseif($displayOptions['layout'] === 'list')
                <!-- List Layout -->
                <div class="row justify-content-center">
                    <div class="specification-list col-lg-6">
                        @foreach($displayOptions['specifications'] as $spec)
                            <div class="specification-item d-flex align-items-center py-3 border-bottom">
                                <div class="specification-content flex-grow-1">
                                    <h6 class="specification-label fw-semibold mb-1">
                                        @if($spec['icon'] ?? false)
                                            <i class="{{ $spec['icon'] }} me-2"></i>
                                        @endif
                                        {{ $spec['label'] }}
                                    </h6>

                                    <div class="specification-value">
                                        {{ $spec['value'] }}
                                        @if($spec['unit'] ?? false)
                                            <span class="text-muted">{{ $spec['unit'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Table Layout -->
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th scope="col">Specification</th>
                                    <th scope="col">Value</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($displayOptions['specifications'] as $spec)
                                    <tr>
                                        <td>
                                            @if($spec['icon'] ?? false)
                                                <i class="{{ $spec['icon'] }} me-2"></i>
                                            @endif
                                            {{ $spec['label'] }}
                                        </td>
                                        <td>
                                            {{ $spec['value'] }}
                                            @if($spec['unit'] ?? false)
                                                <span class="text-muted">{{ $spec['unit'] }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        @endif

        <!-- Download Button -->
        @if($displayOptions['download_url'] ?? false)
            <div class="text-center mt-5">
                <a href="{{ $displayOptions['download_url'] }}"
                   class="btn btn-primary px-5 py-3 fs-6 rounded-pill shadow-lg"
                   download="{{ $displayOptions['download_filename'] ?? 'specifications' }}"
                   target="_blank">
                    <i class="fas fa-download me-2"></i>
                    {{ !empty($displayOptions['download_label']) ? $displayOptions['download_label'] : 'Download Brochure' }}
                </a>
            </div>
        @endif
    </div>
</section>
