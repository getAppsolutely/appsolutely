<header class="header">
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <!-- Logo - Always on the left -->
            <a href="{{ route('home') }}" class="navbar-brand">
                @if($logo)
                    <img src="{{ $logo }}" alt="{{ config('appsolutely.general.site_name') }}" height="40">
                @elseif(themed_assets('images/logo.webp'))
                    <img src="{{ themed_assets('images/logo.webp') }}" alt="{{ config('appsolutely.general.site_name') }}" height="40">
                @else
                    <span>{{ config('appsolutely.general.site_name') }}</span>
                @endif
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation - Centered on desktop -->
            <div class="collapse navbar-collapse justify-content-center" id="navbarMain">
                @if($mainNavigation->isNotEmpty())
                    <ul class="navbar-nav">
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
    </nav>
</header>
