<section class="specifications-section py-5">
    <div class="container">
        <!-- Section Header -->
        @if($data['title'] || $data['subtitle'] || $data['description'])
            <div class="text-center mb-5">
                @if($data['title'])
                    <h2 class="display-5 fw-bold mb-3">
                        {{ $data['title'] }}
                    </h2>
                @endif

                @if($data['subtitle'])
                    <h3 class="h4 mb-4">
                        {{ $data['subtitle'] }}
                    </h3>
                @endif

                @if($data['description'])
                    <p class="lead">
                        {{ $data['description'] }}
                    </p>
                @endif
            </div>
        @endif

        <!-- Specifications Content -->
        @if(!empty($data['specifications']))
            @if($data['layout'] === 'grid')
                <!-- Grid Layout -->
                <div class="row g-4">
                    @foreach($data['specifications'] as $spec)
                        <div class="col-md-{{ 12 / $data['columns'] }}">
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

            @elseif($data['layout'] === 'list')
                <!-- List Layout -->
                <div class="specification-list">
                    @foreach($data['specifications'] as $spec)
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
                                @foreach($data['specifications'] as $spec)
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
    </div>
</section>
