<header id="main-header" class="main-header header">
    <nav class="header__nav navbar navbar-expand-xl">
        <!-- Hamburger Menu Toggle (pure CSS) -->
        <input type="checkbox" id="header-mobile-toggle" class="header__toggle d-none" aria-hidden="true" />
        <div class="header__container container container-responsive d-flex align-items-center justify-content-between">
            <!-- Left: Logo -->
            <div class="header__left d-flex align-items-center flex-shrink-0">
                <a href="{{ route('home') }}" class="header__logo navbar-brand m-0">
                    @if ($displayOptions['logo'])
                        <img src="{{ asset_url('assets/images/logo-dark.webp') }}" alt="{{ site_title() }}"
                            height="40" class="header__logo-img header__logo-img--dark">
                        <img src="{{ asset_url('assets/images/logo.webp') }}" alt="{{ site_title() }}" height="40"
                            class="header__logo-img header__logo-img--light">
                    @else
                        <span>{{ site_title() }}</span>
                    @endif
                </a>
            </div>

            <label for="header-mobile-toggle" class="header__toggler navbar-toggler bg-white d-xl-none ms-2 mb-0"
                aria-label="Toggle navigation" tabindex="0">
                <span class="navbar-toggler-icon" aria-hidden="true"></span>
            </label>

            <!-- Center: Main Navigation (hidden below 1200px) -->
            <div class="header__center flex-grow-1 d-none d-xl-flex justify-content-center">
                @if ($mainNavigation->isNotEmpty())
                    <ul class="navbar-nav flex-row align-items-center">
                        @foreach ($mainNavigation as $item)
                            <li
                                class="header__nav-item nav-item ms-2 me-2 position-relative {{ $item->children->isNotEmpty() ? 'header__nav-item--has-submenu has-submenu' : '' }}">
                                @if ($item->children->isNotEmpty())
                                    <a class="header__nav-link nav-link text-uppercase" href="#" role="button">
                                        @if ($item->icon)
                                            <i class="{{ $item->icon }} me-1" aria-hidden="true"></i>
                                        @endif
                                        {{ $item->title }}
                                    </a>
                                    <!-- Submenu markup -->
                                    <ul class="header__submenu submenu list-unstyled m-0 p-0 position-absolute">
                                        <li class="d-flex justify-content-center w-100">
                                            <div class="container-xxl">
                                                <div class="row justify-content-center g-4">
                                                    @foreach ($item->children as $child)
                                                        <div
                                                            class="header__submenu-item col-auto d-flex flex-column align-items-center">
                                                            <a class="dropdown-item text-center d-block p-3"
                                                                href="{{ app_uri($child->url) }}"
                                                                target="{{ $child->target->value }}">
                                                                @if ($child->icon)
                                                                    <i class="{{ $child->icon }} d-block mb-2"></i>
                                                                @endif
                                                                @if ($child->thumbnail)
                                                                    <span class="d-inline-block position-relative">
                                                                        <img class="img-fluid mb-2"
                                                                            src="{{ asset_url($child->thumbnail) }}"
                                                                            alt="{{ $child->title }}">
                                                                        @if (($child->setting['flag_coming_soon'] ?? '') === 'true')
                                                                            <img src="{{ themed_assets('/images/coming.png') }}"
                                                                                alt="Coming soon"
                                                                                class="header__coming-ribbon position-absolute"
                                                                                style="top: 56px; right: 0; width: 56px;">
                                                                        @endif
                                                                    </span>
                                                                @endif
                                                                <span
                                                                    class="d-block pt-3 fw-semibold">{{ $child->title }}</span>
                                                                @if (!empty($child->setting['price']))
                                                                    <div class="header__price-section mt-2">
                                                                        <span class="fs-6">
                                                                            {{ $child->setting['price'] }}
                                                                            <small>+ORC</small>
                                                                        </span>
                                                                    </div>
                                                                @endif
                                                            </a>
                                                            @if (!empty($child->setting['learn_more_link']) || !empty($child->setting['test_drive_link']))
                                                                <div
                                                                    class="header__submenu-buttons ms-3 d-flex justify-content-center gap-2 mt-2">
                                                                    @if (!empty($child->setting['learn_more_link']))
                                                                        <a href="{{ $child->setting['learn_more_link'] }}"
                                                                            class="btn btn-sm fw-semibold px-2 py-2 border bg-white text-dark">
                                                                            {{ __t('Learn More') }}
                                                                        </a>
                                                                    @endif
                                                                    @if (!empty($child->setting['test_drive_link']))
                                                                        <a href="{{ $child->setting['test_drive_link'] }}"
                                                                            class="btn btn-sm fw-semibold px-2 py-2 border bg-white text-dark">
                                                                            {{ __t('Test Drive') }}
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                @else
                                    <a class="header__nav-link nav-link text-uppercase {{ request()->routeIs($item->url) ? 'header__nav-link--active active' : '' }}"
                                        href="{{ app_uri($item->url) }}" target="{{ $item->target->value }}">
                                        @if ($item->icon)
                                            <i class="{{ $item->icon }} me-1" aria-hidden="true"></i>
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
            @if (($displayOptions['booking']['text'] ?? null) && ($displayOptions['booking']['url'] ?? null))
                <div class="header__right d-none d-xl-block flex-shrink-0">
                    <a href="{{ $displayOptions['booking']['url'] }}" class="btn btn-outline-light-primary">
                        {{ $displayOptions['booking']['text'] }}
                    </a>
                </div>
            @endif
        </div>

        <!-- Hamburger Menu Overlay/Layer (for mobile/tablet, pure CSS toggle) -->
        <div class="header__mobile-overlay d-xl-none">
            <label for="header-mobile-toggle" class="header__mobile-close" tabindex="0"
                aria-label="Close menu">&times;</label>
            <nav class="header__mobile-nav" aria-label="Mobile navigation">
                @if ($mainNavigation->isNotEmpty())
                    <ul class="navbar-nav flex-column align-items-stretch">
                        @foreach ($mainNavigation as $item)
                            <li
                                class="header__nav-item nav-item {{ $item->children->isNotEmpty() ? 'header__nav-item--has-submenu has-submenu' : '' }}">
                                <a class="header__nav-link nav-link text-uppercase {{ request()->routeIs($item->url) ? 'header__nav-link--active active' : '' }}"
                                    href="{{ app_uri($item->url) }}" target="{{ $item->target->value }}">
                                    @if ($item->icon)
                                        <i class="{{ $item->icon }} me-1" aria-hidden="true"></i>
                                    @endif
                                    {{ $item->title }}
                                </a>
                                @if ($item->children->isNotEmpty())
                                    <ul class="header__submenu submenu list-unstyled m-0 p-0">
                                        @foreach ($item->children as $child)
                                            <li>
                                                <a class="dropdown-item text-center" href="{{ app_uri($child->url) }}"
                                                    target="{{ $child->target->value }}">
                                                    @if ($child->icon)
                                                        <i class="{{ $child->icon }} me-2" aria-hidden="true"></i>
                                                    @endif
                                                    @if ($child->thumbnail)
                                                        <span class="d-block position-relative">
                                                            <img class=""
                                                                src="{{ asset_url($child->thumbnail) }}"
                                                                alt="{{ $child->title }}" style="width: 100%">
                                                            @if (($child->setting['flag_coming_soon'] ?? '') === 'true')
                                                                <img src="{{ themed_assets('/images/coming.png') }}"
                                                                    alt="Coming soon"
                                                                    class="header__coming-ribbon position-absolute"
                                                                    style="top: 48px; right: -6px; width: 48px;">
                                                            @endif
                                                        </span>
                                                    @endif
                                                    <span class="d-inline-block">{{ $child->title }}</span>
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
                @if (($displayOptions['booking']['text'] ?? null) && ($displayOptions['booking']['url'] ?? null))
                    <div class="header__mobile-booking mt-4">
                        <a href="{{ $displayOptions['booking']['url'] }}" class="btn btn-outline-light-primary w-100">
                            {{ $displayOptions['booking']['text'] }}
                        </a>
                    </div>
                @endif
            </nav>
        </div>
    </nav>
</header>
