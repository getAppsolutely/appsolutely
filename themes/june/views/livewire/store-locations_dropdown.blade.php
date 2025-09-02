<section class="store-locations-section py-5">
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

        <!-- Store Locations Dropdown Content -->
        @if(!empty($displayOptions['locations']))
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Location Dropdown -->
                    <div class="mb-4">
                        <label for="store-location-select" class="form-label fw-semibold">Select a Store Location:</label>
                        <select id="store-location-select" class="form-select form-select-lg" onchange="showSelectedLocation(this.value)">
                            <option value="">Choose a location...</option>
                            @foreach($displayOptions['locations'] as $index => $location)
                                <option value="{{ $index }}" data-location='@json($location)'>
                                    {{ $location['name'] }}
                                    @if($location['type'] ?? false) - {{ $location['type'] }}@endif
                                    @if($location['featured'] ?? false) ⭐@endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Selected Location Display -->
                    <div id="selected-location-display" class="location-details" style="display: none;">
                        <div class="card border-0 shadow-lg">
                            <div class="card-body p-4">
                                <!-- Location Header -->
                                <div class="d-flex justify-content-between align-items-start mb-4">
                                    <div>
                                        <h4 id="selected-location-name" class="fw-bold text-dark mb-1"></h4>
                                        <p id="selected-location-type" class="text-muted small mb-0 text-uppercase fw-semibold"></p>
                                    </div>
                                    <div id="featured-badge" class="d-none">
                                        <span class="badge bg-dark text-white fs-6 px-3 py-2">
                                            <i class="fas fa-star me-1"></i>Featured
                                        </span>
                                    </div>
                                </div>

                                <!-- Location Information -->
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="location-info mb-4">
                                            <!-- Address -->
                                            <div class="d-flex align-items-start mb-3">
                                                <i class="fas bi-geo-alt-fill text-muted me-2"></i>
                                                <div class="flex-grow-1">
                                                    <div id="selected-location-address" class="fw-medium"></div>
                                                </div>
                                            </div>

                                            <!-- Phone -->
                                            <div id="phone-section" class="d-flex align-items-center mb-3" style="display: none !important;">
                                                <i class="fas bi-telephone-fill text-muted me-2"></i>
                                                <a id="selected-location-phone" href="#" class="text-decoration-none text-dark fw-medium"></a>
                                            </div>

                                            <!-- Email -->
                                            <div id="email-section" class="d-flex align-items-center mb-3" style="display: none !important;">
                                                <i class="fas fa-envelope text-muted me-2"></i>
                                                <a id="selected-location-email" href="#" class="text-decoration-none text-dark fw-medium"></a>
                                            </div>

                                            <!-- Website -->
                                            <div id="website-section" class="d-flex align-items-center mb-3" style="display: none !important;">
                                                <i class="fas fa-globe text-muted me-2"></i>
                                                <a id="selected-location-website" href="#" target="_blank" class="text-decoration-none text-dark fw-medium"></a>
                                            </div>

                                            <!-- Services -->
                                            <div id="services-section" class="mb-4" style="display: none !important;">
                                                <div class="small text-muted mb-2 fw-semibold">Available Services</div>
                                                <div id="selected-location-services" class="d-flex flex-wrap gap-1"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <!-- Hours -->
                                        <div id="hours-section" class="mb-4" style="display: none !important;">
                                            <div id="selected-location-hours"></div>
                                        </div>

                                        <!-- Service Hours -->
                                        <div id="service-hours-section" class="mb-4" style="display: none !important;">
                                            <div id="selected-location-service-hours"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Information -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div id="additional-info" class="row" style="display: none !important;">
                                            <div id="manager-section" class="col-md-6 mb-3" style="display: none !important;">
                                                <div class="small text-muted mb-1 fw-semibold">Store Manager</div>
                                                <div id="selected-location-manager" class="fw-medium"></div>
                                            </div>
                                            <div id="established-section" class="col-md-6 mb-3" style="display: none !important;">
                                                <div class="small text-muted mb-1 fw-semibold">Established</div>
                                                <div id="selected-location-established" class="fw-medium"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Selection Message -->
                    <div id="no-selection-message" class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
                            <h5>Select a location above to view details</h5>
                            <p class="mb-0">Choose from our {{ count($displayOptions['locations']) }} store locations</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif


    </div>
</section>
