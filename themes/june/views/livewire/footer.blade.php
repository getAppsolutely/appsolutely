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
                                        @if($menuItem->url)
                                            <a href="{{ app_uri($menuItem->url) }}"
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
                                                    <a href="{{ app_uri($child->url) }}"
                                                       target="{{ $child->target->value }}"
                                                       class="text-white text-decoration-none footer-link">
                                                        {{ $child->title }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @elseif($menuItem->url)
                                        <!-- Show menu item as a link if it has no children but has a route -->
                                        <ul class="list-unstyled">
                                            <li class="mb-2">
                                                <a href="{{ app_uri($menuItem->url) }}"
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
                @if($displayOptions['logo'] ?? false)
                    <div class="mb-4">
                        @if(site_title())
                            <img src="{{ asset_url('assets/images/logo-dark.webp') }}"
                                 alt="{{ site_title() }}"
                                 height="40">
                        @endif
                    </div>
                @endif

                <!-- Social Media Menu -->
                @if($socialMediaItems->isNotEmpty())
                    <div class="social-media-menu">
                        @foreach($socialMediaItems as $socialItem)
                            <a href="{{ $socialItem->url }}"
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
                <div class="mt-3 me-3">

                </div>
            </div>
        </div>

        <!-- Second Row -->
        <div class="row py-3 border-top border-secondary">
            <!-- Left Side - Copyright -->
            <div class="col-lg-6">
                <p class="text-white mb-0">
                    @foreach (['company_name', 'address', 'email'] as $field)
                        @if (!empty($displayOptions[$field]))
                            {{ $displayOptions[$field] }}<br>
                        @endif
                    @endforeach
                    {{ $displayOptions['copyright']['text']??null }}
                </p>
            </div>

            <!-- Right Side - Policy Menu -->
            <div class="col-lg-6">
                @if($policyMenuItems->isNotEmpty())
                    <div class="policy-menu text-lg-end">
                        @foreach($policyMenuItems as $policyItem)
                            <a href="{{ app_uri($policyItem->url) }}"
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
