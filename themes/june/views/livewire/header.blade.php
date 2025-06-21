<header class="june-navbar navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="navbar-brand d-flex align-items-center">
            @if($logo)
                <img src="{{ $logo }}" alt="{{ config('appsolutely.general.site_name') }}" height="40" class="me-2">
            @elseif(themed_asset('images/logo.webp'))
                <img src="{{ themed_asset('images/logo.webp') }}" alt="{{ config('appsolutely.general.site_name') }}" height="40" class="me-2">
            @endif

        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Navigation -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <!-- Main Navigation -->
            @if($mainNavigation->isNotEmpty())
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    @foreach($mainNavigation as $item)
                        <li class="nav-item {{ $item->children->isNotEmpty() ? 'dropdown' : '' }}">
                            @if($item->children->isNotEmpty())
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    @if($item->icon)
                                        <i class="{{ $item->icon }} me-1"></i>
                                    @endif
                                    {{ $item->title }}
                                </a>
                                <ul class="dropdown-menu">
                                    @foreach($item->children as $child)
                                        <li>
                                            <a class="dropdown-item" href="{{ $child->route }}" target="{{ $child->target->value }}">
                                                @if($child->icon)
                                                    <i class="{{ $child->icon }} me-2"></i>
                                                @endif
                                                {{ $child->title }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <a class="nav-link {{ request()->routeIs($item->route) ? 'active' : '' }}" href="{{ $item->route }}" target="{{ $item->target->value }}">
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
    </div>
</header>
