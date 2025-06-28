<header id="mainHeader">
    <nav class="navbar navbar-expand-xl">
        <!-- Hamburger Menu Toggle (pure CSS) -->
        <input type="checkbox" id="header-mobile-toggle" class="d-none" />
        <div class="container container-responsive d-flex align-items-center justify-content-between">
            <!-- Left: Logo -->
            <div class="header-left d-flex align-items-center flex-shrink-0">
                <a href="{{ route('home') }}" class="navbar-brand m-0">
                    @if($data['logo'])
                        <img src="{{ asset_server('assets/images/logo-dark.webp') }}"
                             alt="{{ config('appsolutely.general.site_name') }}" height="40" class="logo-dark">
                        <img src="{{ asset_server('assets/images/logo.webp') }}"
                             alt="{{ config('appsolutely.general.site_name') }}" height="40" class="logo-light">
                    @else
                        <span>{{ config('appsolutely.general.site_name') }}</span>
                    @endif
                </a>
            </div>

            <label for="header-mobile-toggle" class="navbar-toggler d-xl-none ms-2 mb-0" aria-label="Toggle navigation" tabindex="0">
                <span class="navbar-toggler-icon"></span>
            </label>

            <!-- Center: Main Navigation (hidden below 1200px) -->
            <div class="header-center flex-grow-1 d-none d-xl-flex justify-content-center">
                @if($mainNavigation->isNotEmpty())
                    <ul class="navbar-nav flex-row align-items-center">
                        @foreach($mainNavigation as $item)
                            <li class="nav-item position-relative {{ $item->children->isNotEmpty() ? 'has-submenu' : '' }}">
                                @if($item->children->isNotEmpty())
                                    <a class="nav-link text-uppercase" href="#" role="button">
                                        @if($item->icon)
                                            <i class="{{ $item->icon }} me-1"></i>
                                        @endif
                                        {{ $item->title }}
                                    </a>
                                    <!-- Submenu markup -->
                                    <ul class="submenu list-unstyled m-0 p-0 position-absolute w-80 start-0 top-100">
                                        <li class="d-flex flex-row justify-content-center w-80">
                                            @foreach($item->children as $child)
                                                <a class="dropdown-item text-center flex-fill"
                                                   href="{{ app_uri($child->url) }}" target="{{ $child->target->value }}">
                                                    @if($child->icon)
                                                        <i class="{{ $child->icon }} me-2"></i>
                                                    @endif
                                                    @if($child->thumbnail)
                                                        <img class="w-100 h-80" src="{{ asset_server($child->thumbnail) }}" alt="{{ $child->title }}">
                                                    @endif
                                                    <span>{{ $child->title }}</span>
                                                </a>
                                            @endforeach
                                        </li>
                                    </ul>
                                @else
                                    <a class="nav-link text-uppercase {{ request()->routeIs($item->url) ? 'active' : '' }}"
                                       href="{{ app_uri($item->url) }}" target="{{ $item->target->value }}">
                                        @if($item->icon)
                                            <i class="{{ $item->icon }} me-1"></i>
                                        @endif
                                        {{ $item->title }}
                                    </a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- Right: Booking Button (hidden below 1200px) -->
            @if(($data['booking']['text'] ?? null) && ($data['booking']['url'] ?? null))
                <div class="header-right d-none d-xl-block flex-shrink-0">
                    <a href="{{ $data['booking']['url'] }}"
                       class="btn btn-outline-light-primary">
                        {{ $data['booking']['text'] }}
                    </a>
                </div>
            @endif
        </div>

    <!-- Hamburger Menu Overlay/Layer (for mobile/tablet, pure CSS toggle) -->
    <div class="header-mobile-overlay d-xl-none">
        <label for="header-mobile-toggle" class="header-mobile-close" tabindex="0">&times;</label>
        <nav class="header-mobile-nav">
            @if($mainNavigation->isNotEmpty())
                <ul class="navbar-nav flex-column align-items-stretch">
                    @foreach($mainNavigation as $item)
                        <li class="nav-item {{ $item->children->isNotEmpty() ? 'has-submenu' : '' }}">
                            <a class="nav-link text-uppercase {{ request()->routeIs($item->url) ? 'active' : '' }}"
                               href="{{ app_uri($item->url) }}" target="{{ $item->target->value }}">
                                @if($item->icon)
                                    <i class="{{ $item->icon }} me-1"></i>
                                @endif
                                {{ $item->title }}
                            </a>
                            @if($item->children->isNotEmpty())
                                <ul class="submenu list-unstyled m-0 p-0">
                                    @foreach($item->children as $child)
                                        <li>
                                            <a class="dropdown-item text-center"
                                               href="{{ app_uri($child->url) }}" target="{{ $child->target->value }}">
                                                @if($child->icon)
                                                    <i class="{{ $child->icon }} me-2"></i>
                                                @endif
                                                @if($child->thumbnail)
                                                    <img class="" src="{{ asset_server($child->thumbnail) }}" alt="{{ $child->title }}">
                                                @endif
                                                {{ $child->title }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif

            <!-- Mobile Booking Button -->
            @if(($data['booking']['text'] ?? null) && ($data['booking']['url'] ?? null))
                <div class="header-mobile-testdrive mt-4">
                    <a href="{{ $data['booking']['url'] }}"
                       class="btn btn-outline-light-primary w-100">
                        <i class="fas fa-play me-2"></i>
                        {{ $data['booking']['text'] }}
                    </a>
                </div>
            @endif
        </nav>
    </div>
    </nav>
</header>
