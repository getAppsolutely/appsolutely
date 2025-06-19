<header class="header" id="mainHeader">
    <nav class="navbar navbar-expand-xl">
        <div class="container container-responsive">
            <!-- Logo - Always on the left -->
            <a href="{{ route('home') }}" class="navbar-brand header-logo">
                @if($logo)
                    <img src="{{ $logo }}" alt="{{ config('appsolutely.general.site_name') }}" height="40">
                @elseif(themed_assets('images/logo.webp'))
                    <img src="{{ themed_assets('images/logo-dark.webp') }}" alt="{{ config('appsolutely.general.site_name') }}" height="40" class="logo-dark">
                    <img src="{{ themed_assets('images/logo.webp') }}" alt="{{ config('appsolutely.general.site_name') }}" height="40" class="logo-light">
                @else
                    <span>{{ config('appsolutely.general.site_name') }}</span>
                @endif
            </a>

            <!-- Mobile Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navigation - Position controlled by CSS -->
            <div class="collapse navbar-collapse" id="navbarMain">
                @if($mainNavigation->isNotEmpty())
                    <ul class="navbar-nav">
                        @foreach($mainNavigation as $item)
                            <li class="nav-item {{ $item->children->isNotEmpty() ? 'dropdown' : '' }}">
                                @if($item->children->isNotEmpty())
                                    <a class="nav-link dropdown-toggle text-uppercase no-caret main-nav-link" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        @if($item->icon)
                                            <i class="{{ $item->icon }} me-1"></i>
                                        @endif
                                        {{ $item->title }}
                                    </a>
                                    <ul class="dropdown-menu mega-dropdown p-0 border-0 shadow-none">
                                        <li class="d-flex flex-row w-100 justify-content-center mega-dropdown-items">
                                            @foreach($item->children as $child)
                                                <a class="dropdown-item text-center flex-fill" href="{{ $child->route }}" target="{{ $child->target->value }}">
                                                    @if($child->icon)
                                                        <i class="{{ $child->icon }} me-2"></i>
                                                    @endif
                                                    {{ $child->title }}
                                                </a>
                                            @endforeach
                                        </li>
                                    </ul>
                                @else
                                    <a class="nav-link text-uppercase main-nav-link {{ request()->routeIs($item->route) ? 'active' : '' }}" href="{{ $item->route }}" target="{{ $item->target->value }}">
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

            <!-- Test Drive Button - Right side for desktop -->
            <div class="header-actions d-none d-xl-block">
                <a href="{{ route('book') }}" class="btn btn-primary test-drive-btn">
                    <i class="fas fa-play me-2"></i>
                    Test Drive
                </a>
            </div>
        </div>
    </nav>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const header = document.getElementById('mainHeader');
  const navLinks = document.querySelectorAll('.main-nav-link');
  const logo = document.querySelector('.header-logo');

  function addHover() { header.classList.add('header-hovered'); }
  function removeHover() {
    if (
      ![...navLinks].some(link => link.matches(':hover')) &&
      !(logo && logo.matches(':hover'))
    ) {
      header.classList.remove('header-hovered');
    }
  }

  navLinks.forEach(link => {
    link.addEventListener('mouseenter', addHover);
    link.addEventListener('focus', addHover);
    link.addEventListener('mouseleave', removeHover);
    link.addEventListener('blur', removeHover);
  });
  if (logo) {
    logo.addEventListener('mouseenter', addHover);
    logo.addEventListener('focus', addHover);
    logo.addEventListener('mouseleave', removeHover);
    logo.addEventListener('blur', removeHover);
  }
});
</script>
