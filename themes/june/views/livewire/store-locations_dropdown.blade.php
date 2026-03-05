<section class="store-locations-dropdown py-5">
    <div class="store-locations-dropdown__container container">
        <!-- Section Header -->
        @if ($displayOptions['title'] || $displayOptions['subtitle'] || $displayOptions['description'])
            <div class="store-locations-dropdown__header text-center mb-5">
                @if ($displayOptions['title'])
                    <h2 class="store-locations-dropdown__title display-5 fw-bold mb-3">
                        {{ $displayOptions['title'] }}
                    </h2>
                @endif

                @if ($displayOptions['subtitle'])
                    <h3 class="store-locations-dropdown__subtitle h4 mb-4">
                        {{ $displayOptions['subtitle'] }}
                    </h3>
                @endif

                @if ($displayOptions['description'])
                    <p class="store-locations-dropdown__description lead">
                        {{ $displayOptions['description'] }}
                    </p>
                @endif
            </div>
        @endif

        <!-- Store Locations Dropdown Content -->
        @if (!empty($displayOptions['locations']))
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Location Dropdown -->
                    <div class="store-locations-dropdown__select-wrap mb-4">
                        <label for="store-location-select" class="form-label fw-semibold">Select a Store
                            Location:</label>
                        <select id="store-location-select" class="form-select form-select-lg"
                            aria-label="Select a store location" onchange="showSelectedLocation(this.value)">
                            <option value="">Choose a location...</option>
                            @foreach ($displayOptions['locations'] as $index => $location)
                                <option value="{{ $index }}" data-location='@json($location)'>
                                    {{ $location['name'] }}
                                    @if ($location['type'] ?? false)
                                        - {{ $location['type'] }}
                                    @endif
                                    @if ($location['featured'] ?? false)
                                        ⭐
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Selected Location Display -->
                    <div id="selected-location-display" class="store-locations-dropdown__location-details d-none">
                        <div class="card border-0 shadow-lg">
                            <div class="card-body p-4">
                                <!-- Location Header -->
                                <div
                                    class="store-locations-dropdown__header-row d-flex justify-content-between align-items-start mb-4">
                                    <div>
                                        <h4 id="selected-location-name"
                                            class="store-locations-dropdown__location-name fw-bold text-dark mb-1"></h4>
                                        <p id="selected-location-type"
                                            class="store-locations-dropdown__location-type text-muted small mb-0 text-uppercase fw-semibold">
                                        </p>
                                    </div>
                                    <div id="featured-badge" class="d-none">
                                        <span class="badge bg-dark text-white fs-6 px-3 py-2">
                                            <i class="fas fa-star me-1" aria-hidden="true"></i>Featured
                                        </span>
                                    </div>
                                </div>

                                <!-- Location Information -->
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="store-locations-dropdown__location-info mb-4">
                                            <!-- Address -->
                                            <div class="d-flex align-items-start mb-3">
                                                <i class="bi bi-geo-alt-fill text-muted me-2" aria-hidden="true"></i>
                                                <div class="flex-grow-1">
                                                    <div id="selected-location-address" class="fw-medium"></div>
                                                </div>
                                            </div>

                                            <!-- Phone -->
                                            <div id="phone-section"
                                                class="store-locations-dropdown__phone d-flex align-items-center mb-3 d-none">
                                                <i class="bi bi-telephone-fill text-muted me-2" aria-hidden="true"></i>
                                                <a id="selected-location-phone" href="#"
                                                    class="text-decoration-none text-dark fw-medium"></a>
                                            </div>

                                            <!-- Email -->
                                            <div id="email-section"
                                                class="store-locations-dropdown__email d-flex align-items-center mb-3 d-none">
                                                <i class="fas fa-envelope text-muted me-2" aria-hidden="true"></i>
                                                <a id="selected-location-email" href="#"
                                                    class="text-decoration-none text-dark fw-medium"></a>
                                            </div>

                                            <!-- Website -->
                                            <div id="website-section"
                                                class="store-locations-dropdown__website d-flex align-items-center mb-3 d-none">
                                                <i class="fas fa-globe text-muted me-2" aria-hidden="true"></i>
                                                <a id="selected-location-website" href="#" target="_blank"
                                                    rel="noopener noreferrer"
                                                    class="text-decoration-none text-dark fw-medium"></a>
                                            </div>

                                            <!-- Services -->
                                            <div id="services-section"
                                                class="store-locations-dropdown__services mb-4 d-none">
                                                <div class="small text-muted mb-2 fw-semibold">Available Services</div>
                                                <div id="selected-location-services" class="d-flex flex-wrap gap-1">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <!-- Hours -->
                                        <div id="hours-section" class="store-locations-dropdown__hours mb-4 d-none">
                                            <div id="selected-location-hours"></div>
                                        </div>

                                        <!-- Service Hours -->
                                        <div id="service-hours-section"
                                            class="store-locations-dropdown__service-hours mb-4 d-none">
                                            <div id="selected-location-service-hours"></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Information -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div id="additional-info"
                                            class="store-locations-dropdown__additional row d-none">
                                            <div id="manager-section"
                                                class="store-locations-dropdown__manager col-md-6 mb-3 d-none">
                                                <div class="small text-muted mb-1 fw-semibold">Store Manager</div>
                                                <div id="selected-location-manager" class="fw-medium"></div>
                                            </div>
                                            <div id="established-section"
                                                class="store-locations-dropdown__established col-md-6 mb-3 d-none">
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
                    <div id="no-selection-message" class="store-locations-dropdown__empty text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-map-marker-alt fa-3x mb-3" aria-hidden="true"></i>
                            <h5>Select a location above to view details</h5>
                            <p class="mb-0">Choose from our {{ count($displayOptions['locations']) }} store locations
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif


    </div>
</section>
