# Theme Development Guide

This guide outlines best practices for developing themes in a Laravel-based CMS system with Livewire components, SCSS styling, and TypeScript functionality.

**Theme stack variants:** Themes can use different CSS frameworks. The **default** theme uses **Tailwind** (`css/app.css`); the **june** theme uses **Bootstrap 5** (`sass/app.scss`). The examples in this guide are Bootstrap-oriented, but the structure and conventions apply to any stack.

## Table of Contents

1. [Theme Structure](#theme-structure)
2. [Theme Manifest](#theme-manifest)
3. [Parent Themes](#parent-themes)
4. [Blade Templates (Livewire Components)](#blade-templates-livewire-components)
5. [SCSS Styling](#scss-styling)
6. [TypeScript Components](#typescript-components)
7. [Asset Management](#asset-management)
8. [Best Practices](#best-practices)
9. [File Organization](#file-organization)

## Theme Structure

A well-organized theme follows this directory structure:

```
themes/
└── theme-name/
    ├── images/                    # Static images and icons
    │   ├── logo.svg
    │   └── hero-bg.jpg
    ├── js/                       # TypeScript/JavaScript files
    │   ├── app.ts                # Main entry point
    │   ├── bootstrap.ts          # Bootstrap configuration
    │   ├── assets.ts             # Asset management
    │   ├── components/           # Component-specific JS
    │   │   ├── header.ts
    │   │   └── hero-banner.ts
    │   └── types/               # TypeScript type definitions
    │       └── index.ts
    ├── sass/                    # SCSS stylesheets
    │   ├── _variables.scss      # Bootstrap variable overrides
    │   ├── app.scss             # Main SCSS entry point
    │   └── components/          # Component-specific styles
    │       ├── _mixins.scss
    │       ├── _common.scss
    │       ├── _header.scss
    │       └── _hero-banner.scss
    ├── views/                   # Blade templates
    │   ├── layouts/
    │   │   └── public.blade.php
    │   ├── livewire/            # Livewire component views
    │   │   ├── hero-banner.blade.php              # Default style
    │   │   └── hero-banner_fullscreen.blade.php   # Style-specific variant
    │   └── components/          # Reusable Blade components
    │       └── notice.blade.php
    ├── manifest.json            # Block templates for Page Builder (see Theme Manifest)
    ├── vite.config.ts           # Vite build configuration
    └── README.md                # Theme documentation
```

## Theme Manifest

Each theme can define a `manifest.json` file that registers block templates for the Page Builder. The manifest is the source of truth for which blocks are available in the active theme.

**Location:** `themes/{theme-name}/manifest.json`

### Structure

```json
{
    "version": "1.0.0",
    "description": "Theme manifest - defines available templates and their configurations",
    "templates": {
        "block-key": {
            "label": "Block Label",
            "description": "Short description for the Page Builder",
            "component": "App\\Livewire\\GeneralBlock",
            "view": "block-view-name",
            "displayOptions": {
                "title": "Default Title",
                "anchor_label": "Section Name"
            },
            "queryOptions": {},
            "styles": ["default", "variant"]
        }
    }
}
```

### Key Fields

| Field                      | Description                                                                                                  |
| -------------------------- | ------------------------------------------------------------------------------------------------------------ |
| `component`                | Livewire component class (e.g. `App\\Livewire\\GeneralBlock` or a custom block)                              |
| `view`                     | View name (without path) — maps to `themes/{theme}/views/livewire/{view}.blade.php`                          |
| `displayOptions`           | Default visual/presentation config passed to the view as `$displayOptions`                                   |
| `queryOptions`             | Default data-fetching config (for custom blocks that query repositories)                                     |
| `displayOptionsDefinition` | Schema for form generation — field definitions (type, label, default, options, etc.) for `displayOptions`    |
| `queryOptionsDefinition`   | Schema for form generation — field definitions for `queryOptions`                                            |
| `styles`                   | Available style variants (e.g. `["default", "fullscreen"]`) — enables `component-name_style.blade.php` views |

### displayOptionsDefinition and queryOptionsDefinition

These optional keys define the **schema** for each field in `displayOptions` and `queryOptions`. They follow the same format as `page_blocks.schema` (see [Block System](block-system.md#schema-system)) and are used by the Page Builder to generate dynamic forms for editing block configuration.

Each field in a definition can have:

- `type` — `text`, `textarea`, `number`, `boolean`, `select`, `url`, `email`, `date`, `color`, `object`, `table`
- `label` — Human-readable label for the form
- `description` — Optional help text
- `required` — Whether the field is required
- `default` — Default value
- `options` — For `select`: `[{ "value": "...", "label": "..." }]`
- `fields` — For `object` and `table`: nested field definitions
- Type-specific: `min`, `max`, `step`, `max_length`, etc.

### When to Add a Manifest Entry

- **GeneralBlock templates** — Content-only blocks (no data querying) require a manifest entry and a view. No custom Livewire component or `page_blocks` record is needed.
- **Custom blocks** — Blocks with their own Livewire component and `page_blocks` record still need a manifest entry so they appear in the Page Builder. The manifest `component` must match `page_blocks.class`.

For full details on the block system, schemas, and block creation, see [Block System](block-system.md).

## Parent Themes

A theme can inherit views from a **parent theme**. If a view is not found in the active theme, Laravel looks for it in the parent.

**Configuration:** `config/theme.php`

```php
'parent' => 'default',  // Parent theme name
```

**How it works:**

- `ThemeService::setupTheme($themeName, $parentTheme)` is called with the resolved parent from `config('theme.parent')`.
- The qirolab/laravel-themer package resolves views: **child theme first**, then **parent theme**.
- Use a parent when building a child theme that overrides only some views (e.g. layouts, specific blocks) and inherits the rest.

**Example:** If `june` has `parent => 'default'`, a missing `themes/june/views/livewire/some-block.blade.php` falls back to `themes/default/views/livewire/some-block.blade.php`.

**Note:** Assets (SCSS, JS) are **not** inherited — each theme has its own Vite build. Only Blade views participate in the parent/child hierarchy.

## Blade Templates (Livewire Components)

### Style-Specific Views

Livewire components support multiple style variants through a naming convention pattern:

- **Default Style**: `component-name.blade.php` (e.g., `hero-banner.blade.php`)
- **Style-Specific**: `component-name_style.blade.php` (e.g., `hero-banner_fullscreen.blade.php`)

When a component's `$style` property is set to a specific value (like "fullscreen"), the system automatically looks for the corresponding style-specific view file. If not found, it falls back to the default view.

**Examples:**

- `hero-banner.blade.php` - Default hero banner layout
- `hero-banner_fullscreen.blade.php` - Fullscreen hero banner variant

### Page Wrapper Alignment

Each block is rendered inside a page-level wrapper in `resources/views/pages/show.blade.php`:

```blade
<div id="block-{{ $block->reference }}" class="block-wrapper">
    @renderBlock($block, $page)
</div>
```

**Livewire Blade views should align with this structure:**

- Use a **single root element** that wraps all block content
- The root element becomes the direct child of `block-wrapper`; keep structure simple and predictable
- Mirror the wrapper's purpose: one semantic container per block, no stray siblings

### Component Structure & CSS Naming (Unified)

Use a consistent root element and BEM-style class naming across all Livewire block views. The page wraps each block in `block-wrapper`; the block view outputs its own root. SCSS targets blocks by their class (e.g. `.hero-banner`, `.faq-section`) — no `.block` prefix is used.

**Root Element:**

- **`<section>`** — For most blocks (hero-banner, features, faq-section, etc.). Semantic and accessible.
- **`<header>`** — For header blocks (matches Livewire component name).
- **`<footer>`** — For footer blocks (matches Livewire component name).
- **`<nav>`** — For navigation blocks (e.g. anchor/section navigation).

**Root Classes (BEM-style):**

Use `block-name` on the root. For style variants, add `block-name--style` (BEM modifier) or `block-name-style` (compound class). For blocks with multiple items (e.g. hero carousel), use an outer wrapper `block-name__wrapper`; the actual block instances go on inner elements with `block-name block-name-{style}`.

```php
{{-- Simple block: single root with block-name --}}
<section class="faq-section">
    <div class="faq-section__container container">...</div>
</section>

{{-- Block with multiple items: wrapper + inner block instances --}}
<section class="hero-banner__wrapper">
    @foreach ($displayOptions['heroes'] as $hero)
        <div class="hero-banner hero-banner-{{ $style ?? 'default' }}">
            <div class="hero-banner__caption">...</div>
            <div class="hero-banner__image-wrap">...</div>
        </div>
    @endforeach
</section>

{{-- header.blade.php — semantic element matches component name --}}
<header class="header">
    {{-- Content --}}
</header>

{{-- anchor.blade.php — nav for section navigation --}}
<nav class="anchor-nav" role="navigation" aria-label="...">
    {{-- Content --}}
</nav>
```

**Child elements:** Use BEM `block-name__element` for inner structure (e.g. `hero-banner__caption`, `faq-section__container`).

**Examples:**

- `hero-banner hero-banner-default` — Default hero banner (inner instance)
- `hero-banner hero-banner-fullscreen` — Fullscreen hero banner (inner instance)
- `hero-banner__wrapper` — Outer wrapper for multi-item hero block
- `media-slider media-slider--carousel` — Carousel media slider
- `header` — Header block (root `<header>`)
- `footer` — Footer block (root `<footer>`)

### When to Use Style Variants vs Conditional Rendering

**Use Style-Specific Views When:**

- **Significant Layout Differences**: The layout structure changes substantially between styles
- **Different HTML Structure**: Different elements, containers, or component hierarchy
- **Complex Conditional Logic**: Multiple nested conditions that make the template hard to read
- **Performance**: Different styles require different JavaScript initialization or dependencies
- **Maintainability**: Easier to maintain separate files than complex conditional logic

**Example - Use Style Variants:**

```php
{{-- hero-banner.blade.php - Simple banner with image --}}
<section class="hero-banner hero-banner--default">
    <img class="hero-banner__image" src="{{ $image }}" alt="{{ $title }}">
    <div class="hero-banner__caption">
        <h2>{{ $title }}</h2>
    </div>
</section>

{{-- hero-banner_fullscreen.blade.php - Complex fullscreen layout --}}
<section class="hero-banner hero-banner--fullscreen">
    <div class="hero-banner__video-background">
        <video autoplay muted loop>
            <source src="{{ $video }}" type="video/mp4">
        </video>
    </div>
    <div class="hero-banner__content-overlay">
        <div class="hero-banner__text-animation">
            <h1 class="hero-banner__title">{{ $title }}</h1>
            <p class="hero-banner__subtitle">{{ $subtitle }}</p>
        </div>
        <div class="hero-banner__actions">
            <button class="hero-banner__cta">{{ $ctaText }}</button>
        </div>
    </div>
</section>
```

**Use Conditional Rendering When:**

- **Minor Style Differences**: Only CSS classes or simple attributes change
- **Simple Conditions**: One or two straightforward conditional elements
- **Shared Structure**: Same HTML structure with different content or styling
- **Dynamic Content**: Content varies based on data, not layout structure

**Example - Use Conditional Rendering:**

```php
{{-- hero-banner.blade.php - Same structure, different styling --}}
<section class="hero-banner {{ $style === 'fullscreen' ? 'hero-banner--fullscreen' : 'hero-banner--default' }}">
    <div class="hero-banner__image-wrap {{ $style === 'fullscreen' ? 'hero-banner__image-wrap--fullscreen' : 'hero-banner__image-wrap--standard' }}">
        <img src="{{ $image }}" alt="{{ $title }}">
    </div>
    <div class="hero-banner__caption {{ $style === 'fullscreen' ? 'hero-banner__caption--center' : 'hero-banner__caption--bottom' }}">
        <h2>{{ $title }}</h2>
        @if($style === 'fullscreen')
            <p class="hero-banner__subtitle hero-banner__subtitle--large">{{ $subtitle }}</p>
        @else
            <p class="hero-banner__subtitle">{{ $subtitle }}</p>
        @endif
    </div>
</section>
```

**Decision Matrix:**

| Factor            | Style Variants    | Conditional Rendering |
| ----------------- | ----------------- | --------------------- |
| Layout Structure  | Different         | Same                  |
| HTML Complexity   | High              | Low                   |
| Conditional Logic | Simple            | Complex               |
| File Size         | Multiple files    | Single file           |
| Maintainability   | High (separated)  | Medium (unified)      |
| Performance       | Better (targeted) | Good (shared)         |

**CSS Benefits:**

```scss
// Target all hero-banner blocks regardless of style
.hero-banner {
    // Common styles for all hero banner variants
}

// Target only default hero-banner blocks
.hero-banner.hero-banner--default {
    // Default-specific styles
}

// Target only fullscreen hero-banner blocks
.hero-banner.hero-banner--fullscreen {
    // Fullscreen-specific styles
}
```

### Asset Helpers: `asset_url()` vs `themed_assets()`

Use the correct helper depending on the asset source:

| Helper                     | Use For                                                                                                | Example                                                                                                                     |
| -------------------------- | ------------------------------------------------------------------------------------------------------ | --------------------------------------------------------------------------------------------------------------------------- |
| **`asset_url($uri)`**      | CMS/storage assets: uploads, config-driven URLs (e.g. from `display_options`), paths under `assets/`   | `asset_url($displayOptions['video_url'])`, `asset_url($slide['url'])`, `asset_url('assets/images/logo.webp')`               |
| **`themed_assets($path)`** | Theme assets built by Vite: images/fonts imported in JS or SCSS, or explicitly added to the Vite build | `themed_assets('/images/coming.png')` — asset must be imported in `assets.ts` or similar so it appears in the Vite manifest |

**Rules of thumb:**

- **Blade templates** — Use `asset_url()` for URLs that come from `$displayOptions`, `$queryOptions`, or CMS content (user uploads, media library).
- **Theme static assets** — Use `themed_assets()` only for assets that are part of the theme build (imported in Vite). If the asset is not in the Vite manifest, `themed_assets()` will throw at runtime.

### Best Practices for Blade Templates

1. **Consistent Structure**: Use a single root element per Livewire view — `<section>` for most blocks; `<header>`, `<footer>`, or `<nav>` when the block purpose matches. Align with the page's `block-wrapper` structure.
2. **CSS Class Naming**: Use BEM-style `block-name` and `block-name--style` on the root; `block-name__element` for children
3. **Conditional Rendering**: Use `@if (!empty($displayOptions['key']))` for optional content
4. **Asset URLs**: Use `asset_url()` for CMS/storage media; use `themed_assets()` only for theme assets built by Vite (see [Asset Helpers](#asset-helpers-asset_url-vs-themed_assets) above)
5. **Accessibility**: Include proper `alt` attributes and semantic HTML
6. **Responsive Design**: Use Bootstrap classes for responsive behavior
7. **Component Reusability**: Extract common patterns into separate components
8. **Lazy Loading**: Use `lazy` class and `data-src` for performance

## SCSS Styling

**Note:** This section uses **Bootstrap 5** (e.g. June theme). Themes using **Tailwind** (e.g. default) will have a different structure (`css/app.css`, Tailwind config) but the same organizational principles apply.

### Main SCSS Entry Point

```scss
// themes/theme-name/sass/app.scss

// 1. Include functions first (so you can manipulate colors, SVGs, calc, etc)
@import '~bootstrap/scss/functions';

// 2. Include any default variable overrides here
@import 'variables';

// 3. Include remainder of required Bootstrap stylesheets
@import '~bootstrap/scss/variables';
@import '~bootstrap/scss/variables-dark';
@import '~bootstrap/scss/maps';
@import '~bootstrap/scss/mixins';
@import '~bootstrap/scss/utilities';

// 4. Include any optional Bootstrap CSS as needed
@import '~bootstrap/scss/root';
@import '~bootstrap/scss/reboot';
@import '~bootstrap/scss/type';
@import '~bootstrap/scss/images';
@import '~bootstrap/scss/containers';
@import '~bootstrap/scss/grid';
@import '~bootstrap/scss/tables';
@import '~bootstrap/scss/forms';
@import '~bootstrap/scss/buttons';
@import '~bootstrap/scss/transitions';
@import '~bootstrap/scss/dropdown';
@import '~bootstrap/scss/button-group';
@import '~bootstrap/scss/nav';
@import '~bootstrap/scss/navbar';
@import '~bootstrap/scss/card';
@import '~bootstrap/scss/accordion';
@import '~bootstrap/scss/breadcrumb';
@import '~bootstrap/scss/pagination';
@import '~bootstrap/scss/badge';
@import '~bootstrap/scss/alert';
@import '~bootstrap/scss/progress';
@import '~bootstrap/scss/list-group';
@import '~bootstrap/scss/close';
@import '~bootstrap/scss/toasts';
@import '~bootstrap/scss/modal';
@import '~bootstrap/scss/tooltip';
@import '~bootstrap/scss/popover';
@import '~bootstrap/scss/carousel';
@import '~bootstrap/scss/spinners';
@import '~bootstrap/scss/offcanvas';
@import '~bootstrap/scss/placeholders';

// 5. Optionally include utilities API last to generate classes based on the Sass map in `_utilities.scss`
@import '~bootstrap/scss/utilities/api';
@import 'bootstrap-icons/font/bootstrap-icons';

// Google Fonts
@import 'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Noto+Sans+Thai:wght@100..900&display=swap';

// Third-party CSS
@import 'swiper/css/bundle';

// 6. Component imports
@import 'components/mixins';
@import 'components/common';
@import 'components/lazy-loading';
@import 'components/header';
@import 'components/footer';
@import 'components/media-slider';
@import 'components/hero-banner';
@import 'components/video-showcase';
@import 'components/transition-section';
@import 'components/features';
@import 'components/specifications';

// 7. Custom styles
body {
    font-family: $font-family-sans-serif;
    background-color: $body-bg;
    color: $body-color;
}

// 8. Custom theme styles
.text-custom-font {
    font-family: Montserrat, 'Noto Sans Thai', sans-serif;
}
```

### Variables File

```scss
// themes/theme-name/sass/_variables.scss

// Override Bootstrap's default variables here
$bootstrap-icons-font-dir: 'bootstrap-icons/font/fonts';

// Body
$body-bg: #f8fafc;
$body-color: #212529;

// Typography
$font-family-sans-serif:
    'Montserrat',
    'Noto Sans Thai',
    -apple-system,
    blinkmacsystemfont,
    'Segoe UI',
    roboto,
    'Helvetica Neue',
    arial,
    sans-serif;
$font-size-base: 0.9rem;
$line-height-base: 1.6;

// Colors - Bootstrap 5.3.0 compatible
$primary: #3490dc;
$secondary: #6c757d;
$success: #38c172;
$info: #6cb2eb;
$warning: #ffed4a;
$danger: #e3342f;
$light: #f8f9fa;
$dark: #343a40;

// Legacy color variables for backward compatibility
$blue: $primary;
$indigo: #6574cd;
$purple: #9561e2;
$pink: #f66d9b;
$red: $danger;
$orange: #f6993f;
$yellow: $warning;
$green: $success;
$teal: #4dc0b5;
$cyan: $info;

// Border radius
$border-radius: 0.375rem;
$border-radius-sm: 0.25rem;
$border-radius-lg: 0.5rem;

// Spacing
$spacer: 1rem;
$spacers: (
    0: 0,
    1: $spacer * 0.25,
    2: $spacer * 0.5,
    3: $spacer,
    4: $spacer * 1.5,
    5: $spacer * 3,
);

// Grid breakpoints
$grid-breakpoints: (
    xs: 0,
    sm: 576px,
    md: 768px,
    lg: 992px,
    xl: 1200px,
    xxl: 1400px,
);

// Container max widths
$container-max-widths: (
    sm: 540px,
    md: 720px,
    lg: 960px,
    xl: 1140px,
    xxl: 1320px,
);

// Navbar
$navbar-padding-y: 0.5rem;
$navbar-nav-link-padding-x: 0.5rem;

// Cards
$card-border-width: 0;
$card-border-radius: $border-radius;
$card-cap-bg: rgb(0 0 0 / 3%);

// Buttons
$btn-border-radius: $border-radius;
$btn-border-radius-sm: $border-radius-sm;
$btn-border-radius-lg: $border-radius-lg;

// Forms
$form-control-border-radius: $border-radius;
$input-btn-padding-y: 0.375rem;
$input-btn-padding-x: 0.75rem;
```

### Mixins File

```scss
// themes/theme-name/sass/components/_mixins.scss

// Animation mixins for reuse across components

@mixin fade-in-up($delay: 0s) {
    opacity: 0;
    transform: translateY(30px);
    transition:
        opacity 0.8s ease $delay,
        transform 0.8s ease $delay;

    &.animate {
        opacity: 1;
        transform: translateY(0);
    }
}

@mixin fade-in-left($delay: 0s) {
    opacity: 0;
    transform: translateX(-30px);
    transition:
        opacity 0.8s ease $delay,
        transform 0.8s ease $delay;

    &.animate {
        opacity: 1;
        transform: translateX(0);
    }
}

@mixin fade-in-right($delay: 0s) {
    opacity: 0;
    transform: translateX(30px);
    transition:
        opacity 0.8s ease $delay,
        transform 0.8s ease $delay;

    &.animate {
        opacity: 1;
        transform: translateX(0);
    }
}

@mixin stagger-animation($base-delay: 0.2s, $stagger: 0.2s) {
    @for $i from 1 through 3 {
        &:nth-child(#{$i}) {
            @include fade-in-up($base-delay + ($i - 1) * $stagger);
        }
    }
}

@mixin scale-in($delay: 0s) {
    opacity: 0;
    transform: scale(0.8);
    transition:
        opacity 0.6s ease $delay,
        transform 0.6s ease $delay;

    &.animate {
        opacity: 1;
        transform: scale(1);
    }
}
```

### Component SCSS Example

```scss
// themes/theme-name/sass/components/_hero-banner.scss

@use 'mixins' as *;

// Common styles for all hero-banner blocks (target inner instances; no .block prefix)
.hero-banner {
    position: relative;

    // Intersection Observer trigger
    &.in-view {
        .hero-banner__caption {
            opacity: 1;
            transform: translate(-50%, -30%) translateY(0);

            h4 {
                opacity: 1;
                transform: translateY(0);
            }

            p.lead {
                opacity: 1;
                transform: translateY(0);
            }

            .hero-banner__btn {
                opacity: 1;
                transform: translateY(0);
            }
        }
    }
}

// Default hero banner styles (not fullscreen)
.hero-banner:not(.hero-banner-fullscreen) {
    & > .hero-banner__image-wrap {
        aspect-ratio: 16 / 9;
    }

    & > .hero-banner__caption {
        top: 12% !important;

        h4 {
            font-weight: 500 !important;
            font-size: 2rem !important;
        }
    }
}

// Fullscreen style modifier
.hero-banner-fullscreen {
    & > .hero-banner__image-wrap {
        width: 100%;
        height: 100vh;
    }
}

// Hero banner caption (BEM: block__element)
.hero-banner__caption {
    @include overlay-caption;

    top: 15% !important;

    @media (prefers-reduced-motion: reduce) {
        opacity: 1;
        transform: translate(-50%, -30%);
        transition: none;
    }

    // Initial state
    opacity: 0;
    transform: translate(-50%, -30%) translateY(30px);
    transition:
        opacity 0.8s ease,
        transform 0.8s ease;

    // Animate children with staggered delays
    img {
        max-width: 20vw;
        max-height: 40px;

        &[src$='.webp'] {
            width: 10vw;
        }

        @include media-breakpoint-down(xxl) {
            max-width: 50vw !important;
        }
    }

    h4 {
        @include fade-in-up(0.2s);
    }

    p.lead.mb-4 {
        @include fade-in-up(0.4s);

        font-size: 2vw;
        font-weight: 500;

        @include media-breakpoint-down(xxl) {
            font-size: 1.25rem;
        }
    }

    .hero-banner__btn {
        @include fade-in-up(0.6s);
    }
}

// Button styles (BEM: block__element)
.hero-banner__btn {
    @include overlay-btn;
}

.hero-banner__image-wrap {
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

// Optional overlay for text readability
.hero-banner__overlay {
    opacity: 0.3;
    z-index: 1;
}
```

### SCSS Best Practices

1. **Import Order**: Follow Bootstrap's recommended import order
2. **Variable Overrides**: Override Bootstrap variables before importing Bootstrap
3. **Component Organization**: Create separate SCSS files for each component
4. **Mixins**: Use mixins for reusable animation and styling patterns. Two animation patterns: `fade-in-up` (transition-based, triggered by `.animate` via Intersection Observer) for scroll-triggered effects; `animate-fade-in-up` (keyframe-based) for load-triggered effects. Add `@media (prefers-reduced-motion: reduce)` override when using `animate-fade-in-up`.
5. **Responsive Design**: Use Bootstrap's media query mixins consistently
6. **Naming Conventions**: Use BEM methodology: `block-name__element` for children, `block-name--modifier` for variants (e.g. `hero-banner__caption`, `hero-banner--fullscreen`)
7. **Performance**: Avoid deep nesting (max 3-4 levels)

### SCSS Conventions

- **Comments**: Use `//` for single-line and section comments; avoid `/* */` except for multi-line blocks
- **Variables**: Use `$touch-target-min` (44px) for interactive elements to meet accessibility guidelines. Add other semantic variables only when a value is used in 2+ places.

### SCSS Module System (@import vs @use)

The June theme uses a **hybrid** approach because Bootstrap 5 and some third-party CSS (Swiper, Bootstrap Icons) only support `@import`.

| Layer             | Directive                  | Reason                                                                                                                                                                         |
| ----------------- | -------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| **app.scss**      | `@import`                  | Bootstrap, Swiper, and Bootstrap Icons require `@import`. The entry point loads everything into a single global scope so variables and mixins are available to all components. |
| **components/**   | `@use "mixins" as *`       | Components that need mixins use the modern module system. `as *` loads mixins into the current scope (no namespace prefix).                                                    |
| **\_mixins.scss** | `@use "../variables" as *` | Mixins need variables; they load the variables module.                                                                                                                         |

**Rules:**

1. **app.scss** — Use `@import` for Bootstrap, variables, and all component partials. Do not change to `@use`; Bootstrap's Sass does not support it.
2. **Component partials** — Use `@use "mixins" as *` when you need mixins (e.g. `fade-in-up`, `custom-scrollbar`, `media-breakpoint-*`). Variables are in the global scope from app.scss, so no `@use` for variables.
3. **\_mixins.scss** — Use `@use "../variables" as *` so mixins can reference `$gray-100`, `$black-alpha-10`, etc.
4. **New components** — If a component needs only variables (no mixins), no `@use` is needed. If it needs mixins, add `@use "mixins" as *` at the top.

**Example:**

```scss
// themes/june/sass/components/_hero-banner.scss
@use 'mixins' as *;

.hero-banner-caption {
    h4 {
        @include fade-in-up(0.2s); // from mixins
    }
    color: $light; // from global scope (app.scss imports variables)
}
```

## TypeScript Components

### Main Entry Point

```typescript
// themes/theme-name/js/app.ts

/**
 * Main application entry point
 * Loads all component JavaScript files
 */

import './bootstrap';
import './components/lazy-loading.ts';
import './assets';

// Import component JavaScript
import './components/header';
import './components/hero-banner';
import './components/video-showcase';
import './components/media-slider';
import './components/features';
import './components/text-document-collapsible';
import './components/store-locations-dropdown';
import './components/photo-gallery';
```

### Component TypeScript Example

```typescript
// themes/theme-name/js/components/hero-banner.ts

/**
 * Hero Banner Component JavaScript
 * Handles scroll-triggered animations for hero banner captions
 */

(() => {
    const heroBanners = document.querySelectorAll<HTMLElement>('.hero-banner');

    if (!heroBanners.length) return;

    const observerOptions: IntersectionObserverInit = {
        root: null,
        rootMargin: '0px',
        threshold: 0.3, // Trigger when 30% of the hero banner is visible
    };

    const observer = new IntersectionObserver((entries: IntersectionObserverEntry[]) => {
        entries.forEach((entry: IntersectionObserverEntry) => {
            if (entry.isIntersecting) {
                // Add in-view class to trigger SCSS animations
                entry.target.classList.add('in-view');
            } else {
                // Remove in-view class to reset animations
                entry.target.classList.remove('in-view');
            }
        });
    }, observerOptions);

    // Observe all hero banners
    heroBanners.forEach((banner: HTMLElement) => {
        observer.observe(banner);
    });
})();
```

### TypeScript Types

```typescript
// themes/theme-name/js/types/index.ts

/**
 * Theme Type Definitions
 */

// Asset types
export interface AssetPaths {
    images: {
        logo: string;
        heroBg: string;
    };
}

// Component interfaces
export interface HeaderInstance {
    header: HTMLElement | null;
    navbar: HTMLElement | null;
    navbarToggler: HTMLElement | null;
    navbarCollapse: HTMLElement | null;
    submenuItems: NodeListOf<Element> | null;
    init(): void;
    bindEvents(): void;
    checkScroll(): void;
    toggleMobileMenu(): void;
    closeMobileMenu(): void;
    handleDropdownHover(): void;
    showMegaMenu(submenu: HTMLElement): void;
    hideMegaMenu(submenu: HTMLElement): void;
}

export interface MediaSliderConfig {
    loop: boolean;
    autoplay: {
        delay: number;
        disableOnInteraction: boolean;
    };
    keyboard: {
        enabled: boolean;
    };
    slidesPerView: number | 'auto';
    spaceBetween: number;
    breakpoints: {
        [key: number]: {
            slidesPerView: number | 'auto';
            spaceBetween: number;
        };
    };
}

export {};
```

### Client-Side Asset URLs

When TypeScript needs to build asset URLs from data (e.g. CMS image paths in `data-photos`), pass the base URL via a data attribute on the container. **Do not** use a separate `asset_url` utility — prefer the data-attribute pattern.

**Blade:** Add `data-asset-base-url` to the component container:

```blade
{{-- Example: themes/june/views/livewire/dynamic-form_interactive.blade.php --}}
<section class="my-component" data-asset-base-url="{{ asset_url(null, false) }}" data-photos='@json($photos)'>
```

**TypeScript:** Read the base URL from the container and build full URLs:

```typescript
const baseUrl = container.getAttribute('data-asset-base-url') || '/assets/';
const hash = container.getAttribute('data-asset-hash') || ''; // optional: build_hash()
const fullUrl = (path: string) => {
    const cleanBase = baseUrl.replace(/\/$/, '');
    const cleanPath = path.startsWith('/') ? path : `/${path}`;
    return `${cleanBase}${cleanPath}${hash ? `?v=${hash}` : ''}`;
};
```

**When both are needed:** Add `data-asset-hash="{{ build_hash() }}"` for cache busting.

**When to use:** Client-side rendering of image/video URLs from `displayOptions`, `data-*` attributes, or API responses. For theme static assets built by Vite, use `themed_assets()` in Blade or import the asset in your build.

### TypeScript Best Practices

1. **Strict Typing**: Use TypeScript strict mode and proper type annotations
2. **IIFE Pattern**: Wrap component code in immediately invoked function expressions
3. **Null Checks**: Always check for element existence before manipulation
4. **Event Handling**: Use proper event types and handle cleanup
5. **Performance**: Use Intersection Observer for scroll-based animations
6. **Modularity**: Keep components focused and single-purpose
7. **Error Handling**: Include try-catch blocks for critical operations

## Asset Management

### Theme Build & Dev Workflow

Themes are built and served via `themes.ts` (run with `tsx`). Each theme with a `vite.config.ts` is included.

**Build (production):**

```bash
npm run build:themes
# or: tsx themes.ts build
# Builds all themes; optionally: tsx themes.ts build june default
```

**Dev (with HMR):**

```bash
npm run dev:themes
# or: tsx themes.ts dev
# Runs Vite dev server(s) for each theme; optionally: tsx themes.ts dev june
```

**Notes:**

- Each theme uses its own port from `vite.config.ts` (e.g. June: 5177, default: 5175).
- `npm run build` runs the main Vite build, then `build:themes`, then `build:page-builder`.
- `npm run dev:all` runs main app, themes, and page builder concurrently.
- Theme builds output to `public/build/themes/{theme-name}/`.

### Including Theme Assets in Layouts

Use the `@vite` directive with `themed_path()` and `themed_build_path()` so layouts work with any active theme:

```blade
{{-- themes/theme-name/views/layouts/public.blade.php --}}
<head>
    @livewireStyles
    @vite([themed_path() . '/sass/app.scss', themed_path() . '/js/app.ts'], themed_build_path())
</head>
<body>
    @yield('content')
    @livewireScripts
</body>
```

**Helpers:**

| Helper                | Returns                                      | Use                                                              |
| --------------------- | -------------------------------------------- | ---------------------------------------------------------------- |
| `themed_path()`       | `themes/{active-theme}` (e.g. `themes/june`) | Input paths for `@vite` (relative to project root)               |
| `themed_build_path()` | `build/themes/{active-theme}`                | Second argument to `@vite` (build directory for manifest lookup) |

**Avoid hard-coding theme names:**

```blade
{{-- ❌ Don't: hard-coded theme --}}
@vite(['themes/june/sass/app.scss', 'themes/june/js/app.ts'], 'build/themes/june')

{{-- ✅ Do: theme-agnostic --}}
@vite([themed_path() . '/sass/app.scss', themed_path() . '/js/app.ts'], themed_build_path())
```

### Vite Configuration

```typescript
// themes/theme-name/vite.config.ts

import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export default defineConfig({
    base: `/build/themes/theme-name`,
    plugins: [
        laravel({
            input: ['themes/theme-name/sass/app.scss', 'themes/theme-name/js/app.ts'],
            buildDirectory: 'build/themes/theme-name',
        }),
        {
            name: 'blade',
            handleHotUpdate({ file, server }) {
                if (file.endsWith('.blade.php')) {
                    server.ws.send({
                        type: 'full-reload',
                        path: '*',
                    });
                }
            },
        },
    ],
    resolve: {
        alias: {
            // __dirname = theme root (themes/theme-name) when config is at themes/theme-name/vite.config.ts
            '@theme': path.resolve(__dirname),
            '~bootstrap': path.resolve('node_modules/bootstrap'),
        },
    },
    server: {
        host: '0.0.0.0', // Docker-safe
        port: 5177,
        strictPort: true,
        hmr: {
            host: 'localhost',
            protocol: 'ws',
            clientPort: 5177,
        },
        cors: {
            origin: true,
            methods: ['GET', 'HEAD', 'PUT', 'PATCH', 'POST', 'DELETE'],
            credentials: true,
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                silenceDeprecations: ['import', 'mixed-decls', 'color-functions', 'global-builtin'],
            },
        },
    },
    build: {
        assetsInlineLimit: 0,
        rollupOptions: {
            output: {
                assetFileNames: (assetInfo) => {
                    if (!assetInfo.name) return 'assets/[name].[hash][extname]';
                    const ext = assetInfo.name.split('.').pop();
                    if (ext && ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'].includes(ext)) {
                        return 'images/[name].[hash][extname]';
                    }
                    if (ext && ['woff2', 'woff', 'ttf'].includes(ext)) {
                        return 'fonts/[name].[hash][extname]';
                    }
                    return 'assets/[name].[hash][extname]';
                },
            },
        },
    },
});
```

## Best Practices

### General Principles

1. **Consistency**: Maintain consistent patterns across all components
2. **Performance**: Optimize for Core Web Vitals (LCP, FID, CLS)
3. **Accessibility**: Follow WCAG 2.1 AA guidelines
4. **Responsive Design**: Mobile-first approach with progressive enhancement
5. **SEO**: Semantic HTML and proper meta tags
6. **Maintainability**: Clear code structure and documentation

### Code Quality

1. **Linting**: Use ESLint for TypeScript and Stylelint for SCSS
2. **Formatting**: Use Prettier for consistent code formatting
3. **Testing**: Write unit tests for complex JavaScript components
4. **Documentation**: Document complex logic and component APIs
5. **Version Control**: Use conventional commits for clear history

### Performance Optimization

1. **Lazy Loading**: Implement lazy loading for images and components
2. **Code Splitting**: Split JavaScript into logical chunks
3. **Asset Optimization**: Compress images and optimize fonts
4. **Caching**: Implement proper caching strategies
5. **Bundle Size**: Monitor and optimize bundle sizes

## File Organization

### Naming Conventions

- **Files**: Use kebab-case for all files (`hero-banner.blade.php`)
- **Classes**: Use BEM methodology for CSS classes (`.hero-banner__caption`)
- **Variables**: Use camelCase for JavaScript variables (`heroBanners`)
- **Constants**: Use UPPER_SNAKE_CASE for constants (`MAX_SLIDES`)

### Directory Structure Guidelines

1. **Logical Grouping**: Group related files together
2. **Clear Separation**: Separate concerns (styles, scripts, templates)
3. **Scalability**: Structure should accommodate growth
4. **Discoverability**: Easy to find and understand file locations

### Import/Export Patterns

```typescript
// Component exports
export class HeroBanner {
    // Implementation
}

// Default exports for main components
export default HeroBanner;

// Named exports for utilities
export { fadeInUp, fadeInLeft } from './animations';
```

This guide provides a comprehensive foundation for theme development, ensuring maintainable, performant, and scalable themes that follow modern web development best practices.
