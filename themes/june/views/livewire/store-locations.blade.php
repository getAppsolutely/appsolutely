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

        <!-- Store Locations Content -->
        @if(!empty($displayOptions['locations']))
            @if($displayOptions['layout'] === 'grid')
                <!-- Grid Layout -->
                <div class="row g-4">
                    @foreach($displayOptions['locations'] as $location)
                        <div class="col-lg-{{ 12 / $displayOptions['columns'] }} col-md-6 mb-4">
                            <div class="store-card h-100 card border-0 shadow-lg position-relative
                                {{ ($location['featured'] ?? false) ? 'border-2 border-dark' : '' }}">

                                @if($location['featured'] ?? false)
                                    <div class="position-absolute top-0 end-0 m-3">
                                        <span class="badge bg-dark text-white fs-6 px-3 py-2">
                                            <i class="fas fa-star me-1"></i>Featured
                                        </span>
                                    </div>
                                @endif

                                <div class="card-body p-4">
                                    <div class="mb-3">
                                        <h5 class="store-name fw-bold text-dark mb-1">{{ $location['name'] }}</h5>
                                        @if($location['type'] ?? false)
                                            <p class="text-muted small mb-0 text-uppercase fw-semibold">{{ $location['type'] }}</p>
                                        @endif
                                    </div>

                                    <div class="store-info mb-4">
                                        <div class="d-flex align-items-start mb-2">
                                            <i class="fas fa-map-marker-alt text-muted me-3 mt-1"></i>
                                            <div class="flex-grow-1">
                                                <div class="fw-medium">{{ $location['address'] }}</div>
                                                <div class="text-muted small">
                                                    @if($location['city'] ?? false){{ $location['city'] }}@endif@if($location['state'] ?? false), {{ $location['state'] }}@endif@if($location['zip_code'] ?? false) {{ $location['zip_code'] }}@endif
                                                </div>
                                            </div>
                                        </div>

                                        @if($location['phone'] ?? false)
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="fas fa-phone text-muted me-3"></i>
                                                <a href="tel:{{ $location['phone'] }}" class="text-decoration-none text-dark fw-medium">{{ $location['phone'] }}</a>
                                            </div>
                                        @endif

                                        @if($location['hours'] ?? false)
                                            <div class="d-flex align-items-start mb-3">
                                                <i class="fas fa-clock text-muted me-3 mt-1"></i>
                                                <div class="small text-muted lh-sm">{{ $location['hours'] }}</div>
                                            </div>
                                        @endif
                                    </div>

                                    @if(!empty($location['services']))
                                        <div class="services mb-4">
                                            <div class="small text-muted mb-2 fw-semibold">Available Services:</div>
                                            <div class="d-flex flex-wrap gap-1">
                                                @foreach($location['services'] as $service)
                                                    <span class="badge bg-light text-dark border">{{ $service }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif


                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            @elseif($displayOptions['layout'] === 'list')
                <!-- List Layout -->
                <div class="store-list">
                    @foreach($displayOptions['locations'] as $location)
                        <div class="store-item card border-0 shadow-sm mb-4 position-relative
                            {{ ($location['featured'] ?? false) ? 'border-2 border-dark' : '' }}">

                            @if($location['featured'] ?? false)
                                <div class="position-absolute top-0 end-0 m-3">
                                    <span class="badge bg-dark text-white fs-6 px-3 py-2">
                                        <i class="fas fa-star me-1"></i>Featured
                                    </span>
                                </div>
                            @endif

                            <div class="card-body p-4">
                                <div class="row align-items-start">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h5 class="store-name fw-bold text-dark mb-1">{{ $location['name'] }}</h5>
                                                @if($location['type'] ?? false)
                                                    <p class="text-muted small mb-0 text-uppercase fw-semibold">{{ $location['type'] }}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="store-info mb-3">
                                                    <div class="d-flex align-items-start mb-3">
                                                        <i class="fas fa-map-marker-alt text-muted me-3 mt-1"></i>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-medium">{{ $location['address'] }}</div>
                                                            <div class="text-muted small">
                                                                @if($location['city'] ?? false){{ $location['city'] }}@endif@if($location['state'] ?? false), {{ $location['state'] }}@endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                    @if($location['phone'] ?? false)
                                                        <div class="d-flex align-items-center mb-3">
                                                            <i class="fas fa-phone text-muted me-3"></i>
                                                            <a href="tel:{{ $location['phone'] }}" class="text-decoration-none text-dark fw-medium">{{ $location['phone'] }}</a>
                                                        </div>
                                                    @endif

                                                    @if($location['hours'] ?? false)
                                                        <div class="d-flex align-items-start">
                                                            <i class="fas fa-clock text-muted me-3 mt-1"></i>
                                                            <div class="small text-muted lh-sm">{{ $location['hours'] }}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-lg-6">
                                                @if(!empty($location['services']))
                                                    <div class="services mb-4">
                                                        <div class="small text-muted mb-2 fw-semibold">Available Services:</div>
                                                        <div class="d-flex flex-wrap gap-1 mb-3">
                                                            @foreach($location['services'] as $service)
                                                                <span class="badge bg-light text-dark border">{{ $service }}</span>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif


                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

            @else
                <!-- Table Layout -->
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th scope="col">Store Name</th>
                                    <th scope="col">Address</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Hours</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($displayOptions['locations'] as $location)
                                    <tr class="{{ ($location['featured'] ?? false) ? 'table-dark text-white' : '' }}">
                                        <td>
                                            <div class="fw-semibold">{{ $location['name'] }}</div>
                                            @if($location['type'] ?? false)
                                                <small class="text-muted">{{ $location['type'] }}</small>
                                            @endif
                                            @if($location['featured'] ?? false)
                                                <span class="badge bg-light text-dark ms-1">Featured</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $location['address'] }}
                                            @if($location['city'] ?? false)<br><small class="text-muted">{{ $location['city'] }}@if($location['state'] ?? false), {{ $location['state'] }}@endif</small>@endif
                                        </td>
                                        <td>
                                            @if($location['phone'] ?? false)
                                                <a href="tel:{{ $location['phone'] }}" class="text-decoration-none">{{ $location['phone'] }}</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($location['hours'] ?? false)
                                                <small>{{ $location['hours'] }}</small>
                                            @else
                                                -
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

        <!-- Optional Map Integration -->
        @if($displayOptions['show_map'] && !empty($displayOptions['locations']) && $displayOptions['map_api_key'])
            <div class="mt-5">
                <h4 class="mb-3">Store Locations Map</h4>
                <div id="store-locations-map" style="height: 400px; border-radius: 8px;" class="border"></div>
            </div>

            <script>
                function initStoreMap() {
                    const map = new google.maps.Map(document.getElementById('store-locations-map'), {
                        zoom: 10,
                        center: { lat: {{ $displayOptions['locations'][0]['latitude'] ?? 0 }}, lng: {{ $displayOptions['locations'][0]['longitude'] ?? 0 }} }
                    });

                    const locations = @json($displayOptions['locations']);

                    locations.forEach(location => {
                        if (location.latitude && location.longitude) {
                            const marker = new google.maps.Marker({
                                position: { lat: parseFloat(location.latitude), lng: parseFloat(location.longitude) },
                                map: map,
                                title: location.name
                            });

                            const infoWindow = new google.maps.InfoWindow({
                                content: `
                                    <div>
                                        <h6>${location.name}</h6>
                                        <p class="mb-1">${location.address}</p>
                                        ${location.phone ? `<p class="mb-0"><small>📞 ${location.phone}</small></p>` : ''}
                                    </div>
                                `
                            });

                            marker.addListener('click', () => {
                                infoWindow.open(map, marker);
                            });
                        }
                    });
                }
            </script>
            <script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $displayOptions['map_api_key'] }}&callback=initStoreMap"></script>
        @endif
    </div>
</section>
