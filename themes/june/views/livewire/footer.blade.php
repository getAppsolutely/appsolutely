<footer class="footer bg-black text-white">
    <div class="container container-responsive">
        <!-- First Row -->
        <div class="row py-5">
            <!-- Left Side - Footer Menu -->
            <div class="col-lg-8">
                @if($footerMenuItems->isNotEmpty())
                    <div class="row">
                        @foreach($footerMenuItems->take(4) as $menuItem)
                            <div class="col-md-3">
                                <div class="footer-menu-section">
                                    <h5 class="text-white fw-semibold text-uppercase mb-3">
                                        @if($menuItem->route)
                                            <a href="{{ $menuItem->route }}"
                                               target="{{ $menuItem->target->value }}"
                                               class="text-white text-decoration-none">
                                                {{ $menuItem->title }}
                                            </a>
                                        @else
                                            {{ $menuItem->title }}
                                        @endif
                                    </h5>
                                    @if($menuItem->children->isNotEmpty())
                                        <ul class="list-unstyled">
                                            @foreach($menuItem->children as $child)
                                                <li class="mb-2">
                                                    <a href="{{ $child->route }}"
                                                       target="{{ $child->target->value }}"
                                                       class="text-white text-decoration-none footer-link">
                                                        {{ $child->title }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @elseif($menuItem->route)
                                        <!-- Show menu item as a link if it has no children but has a route -->
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <a href="{{ $menuItem->route }}"
                                                   target="{{ $menuItem->target->value }}"
                                                   class="text-white text-decoration-none footer-link">
                                                    {{ $menuItem->title }}
                                                </a>
                                            </li>
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right Side - Logo and Social Media -->
            <div class="col-lg-4 text-lg-end">
                <!-- Logo -->
                @if(($config['logo'] ?? false))
                    <div class="mb-4">
                        @if(config('appsolutely.general.logo'))
                            <img src="{{ config('appsolutely.general.logo') }}"
                                 alt="{{ config('appsolutely.general.site_name') }}"
                                 height="40">
                        @elseif(themed_assets('images/logo.webp'))
                            <img src="{{ themed_assets('images/logo-dark.webp') }}"
                                 alt="{{ config('appsolutely.general.site_name') }}"
                                 height="40">
                        @else
                            <span class="h4 text-white">{{ config('appsolutely.general.site_name') }}</span>
                        @endif
                    </div>
                @endif

                <!-- Social Media Menu -->
                @if($socialMediaItems->isNotEmpty())
                    <div class="social-media-menu">
                        @foreach($socialMediaItems as $socialItem)
                            <a href="{{ $socialItem->route }}"
                               target="{{ $socialItem->target->value }}"
                               class="text-white text-decoration-none me-3 footer-link">
                                @if($socialItem->icon)
                                    <i class="{{ $socialItem->icon }} fs-5"></i>
                                @else
                                    {{ $socialItem->title }}
                                @endif
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Second Row -->
        <div class="row py-3 border-top border-secondary">
            <!-- Left Side - Copyright -->
            <div class="col-lg-6">
                <p class="text-white mb-0">
                    {{ $config['copyright']['text'] ?? '© ' . date('Y') . ' ' . config('appsolutely.general.site_name') . '. All rights reserved.' }}
                </p>
            </div>

            <!-- Right Side - Policy Menu -->
            <div class="col-lg-6">
                @if($policyMenuItems->isNotEmpty())
                    <div class="policy-menu text-lg-end">
                        @foreach($policyMenuItems as $policyItem)
                            <a href="{{ $policyItem->route }}"
                               target="{{ $policyItem->target->value }}"
                               class="text-white text-decoration-none me-3 footer-link">
                                {{ $policyItem->title }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</footer>
