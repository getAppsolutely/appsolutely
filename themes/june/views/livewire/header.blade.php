<header id="mainHeader">
    <nav class="navbar navbar-expand-xl">
        <div class="container container-responsive d-flex align-items-center justify-content-between">
            <!-- Left: Logo -->
            <div class="header-left d-flex align-items-center flex-shrink-0">
                <a href="{{ route('home') }}" class="navbar-brand m-0">
                    @if($logo)
                        <img src="{{ $logo }}" alt="{{ config('appsolutely.general.site_name') }}" height="40">
                    @elseif(themed_assets('images/logo.webp'))
                        <img src="{{ themed_assets('images/logo-dark.webp') }}"
                             alt="{{ config('appsolutely.general.site_name') }}" height="40" class="logo-dark">
                        <img src="{{ themed_assets('images/logo.webp') }}"
                             alt="{{ config('appsolutely.general.site_name') }}" height="40" class="logo-light">
                    @else
                        <span>{{ config('appsolutely.general.site_name') }}</span>
                    @endif
                </a>
            </div>

            <!-- Center: Main Navigation -->
            <div class="header-center flex-grow-1 d-flex justify-content-center">
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
                                    <ul class="submenu list-unstyled m-0 p-0 position-absolute w-100 start-0 top-100">
                                        <li class="d-flex flex-row justify-content-center w-100">
                                            @foreach($item->children as $child)
                                                <a class="dropdown-item text-center flex-fill"
                                                   href="{{ $child->route }}" target="{{ $child->target->value }}">
                                                    @if($child->icon)
                                                        <i class="{{ $child->icon }} me-2"></i>
                                                    @endif
                                                    {{ $child->title }}
                                                </a>
                                            @endforeach
                                        </li>
                                    </ul>
                                @else
                                    <a class="nav-link text-uppercase {{ request()->routeIs($item->route) ? 'active' : '' }}"
                                       href="{{ $item->route }}" target="{{ $item->target->value }}">
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

            <!-- Right: Test Drive Button -->
            <div class="header-right d-none d-xl-block flex-shrink-0">
                <a href="{{ route('book') }}" class="btn btn-primary">
                    <i class="fas fa-play me-2"></i>
                    Test Drive
                </a>
            </div>
        </div>
    </nav>
</header>
