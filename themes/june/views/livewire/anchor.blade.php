@php
    $anchorItems = $anchorItems ?? [];
    $ctaText = $displayOptions['cta_text'] ?? '';
    $ctaUrl = $displayOptions['cta_url'] ?? '#';
    $ctaIcon = $displayOptions['cta_icon'] ?? 'fa-steering-wheel';
@endphp
@if (count($anchorItems) > 0)
    <div>
        <nav class="anchor-nav sticky-top bg-dark py-3" role="navigation"
            aria-label="{{ __('global.anchor.section_navigation') }}">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <ul class="anchor-nav__list nav mb-0 flex-grow-1 flex-wrap" role="list">
                        @foreach ($anchorItems as $index => $item)
                            <li class="anchor-nav__item nav-item">
                                <a class="anchor-nav__link nav-link text-white text-uppercase {{ $index === 0 ? 'anchor-nav__link--active' : '' }}"
                                    href="#block-{{ $item['reference'] }}" data-anchor-index="{{ $index }}">
                                    {{ $item['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    @if ($ctaText !== '')
                        <a href="{{ $ctaUrl }}"
                            class="anchor-nav__cta btn btn-outline-light btn-sm text-nowrap ms-auto flex-shrink-0">
                            @if ($ctaIcon !== '')
                                <i class="fa {{ $ctaIcon }} me-1" aria-hidden="true"></i>
                            @endif
                            {{ $ctaText }}
                        </a>
                    @endif
                </div>
            </div>
        </nav>
        <button type="button" class="anchor-nav__to-top" aria-label="{{ __('global.anchor.go_to_top') }}"
            title="{{ __('global.anchor.go_to_top') }}">
            <i class="bi bi-chevron-double-up" aria-hidden="true"></i>
        </button>
    </div>
@endif
