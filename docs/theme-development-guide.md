# Theme Development Guide

This guide outlines best practices for developing themes in a Laravel-based CMS system with Livewire components, SCSS styling, and TypeScript functionality.

## Table of Contents

1. [Theme Structure](#theme-structure)
2. [Blade Templates (Livewire Components)](#blade-templates-livewire-components)
3. [SCSS Styling](#scss-styling)
4. [TypeScript Components](#typescript-components)
5. [Asset Management](#asset-management)
6. [Best Practices](#best-practices)
7. [File Organization](#file-organization)

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
    ├── vite.config.ts           # Vite build configuration
    └── README.md                # Theme documentation
```

## Blade Templates (Livewire Components)

### Style-Specific Views

Livewire components support multiple style variants through a naming convention pattern:

- **Default Style**: `component-name.blade.php` (e.g., `hero-banner.blade.php`)
- **Style-Specific**: `component-name_style.blade.php` (e.g., `hero-banner_fullscreen.blade.php`)

When a component's `$style` property is set to a specific value (like "fullscreen"), the system automatically looks for the corresponding style-specific view file. If not found, it falls back to the default view.

**Examples:**

- `hero-banner.blade.php` - Default hero banner layout
- `hero-banner_fullscreen.blade.php` - Fullscreen hero banner variant

### Component Structure & CSS Naming

Livewire components should follow a consistent structure with proper CSS class naming for style control:

**CSS Class Naming Convention:**
Use `block [block_name] [block_name]-[style]` pattern on the **root div/section element**:

```php
{{-- themes/theme-name/views/livewire/hero-banner.blade.php Default style --}}
<div class="block hero-banner hero-banner-default">
    {{-- Content --}}
</div>

{{-- themes/theme-name/views/livewire/hero-banner_fullscreen.blade.php Style-specific variant --}}
<div class="block hero-banner hero-banner-fullscreen">
    {{-- Content --}}
</div>
```

**Examples:**

- `block hero-banner hero-banner-default` - Default hero banner
- `block hero-banner hero-banner-fullscreen` - Fullscreen hero banner
- `block media-slider media-slider-carousel` - Carousel media slider

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
<div class="block hero-banner hero-banner-default">
    <img class="hero-image" src="{{ $image }}" alt="{{ $title }}">
    <div class="hero-caption">
        <h2>{{ $title }}</h2>
    </div>
</div>

{{-- hero-banner_fullscreen.blade.php - Complex fullscreen layout --}}
<div class="block hero-banner hero-banner-fullscreen">
    <div class="hero-video-background">
        <video autoplay muted loop>
            <source src="{{ $video }}" type="video/mp4">
        </video>
    </div>
    <div class="hero-content-overlay">
        <div class="hero-text-animation">
            <h1 class="animated-title">{{ $title }}</h1>
            <p class="animated-subtitle">{{ $subtitle }}</p>
        </div>
        <div class="hero-interactive-elements">
            <button class="cta-button">{{ $ctaText }}</button>
        </div>
    </div>
</div>
```

**Use Conditional Rendering When:**

- **Minor Style Differences**: Only CSS classes or simple attributes change
- **Simple Conditions**: One or two straightforward conditional elements
- **Shared Structure**: Same HTML structure with different content or styling
- **Dynamic Content**: Content varies based on data, not layout structure

**Example - Use Conditional Rendering:**

```php
{{-- hero-banner.blade.php - Same structure, different styling --}}
<div class="block hero-banner {{ $style === 'fullscreen' ? 'hero-banner-fullscreen' : 'hero-banner-default' }}">
    <div class="hero-image-container {{ $style === 'fullscreen' ? 'hero-fullscreen' : 'hero-standard' }}">
        <img src="{{ $image }}" alt="{{ $title }}">
    </div>
    <div class="hero-caption {{ $style === 'fullscreen' ? 'hero-caption-center' : 'hero-caption-bottom' }}">
        <h2>{{ $title }}</h2>
        @if($style === 'fullscreen')
            <p class="hero-subtitle-large">{{ $subtitle }}</p>
        @else
            <p class="hero-subtitle">{{ $subtitle }}</p>
        @endif
    </div>
</div>
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
.block.hero-banner {
    // Common styles for all hero banner variants
}

// Target only default hero-banner blocks
.block.hero-banner.hero-banner-default {
    // Default-specific styles
}

// Target only fullscreen hero-banner blocks
.block.hero-banner.hero-banner-fullscreen {
    // Fullscreen-specific styles
}
```

### Best Practices for Blade Templates

1. **Consistent Structure**: Always wrap content in a root `<div>` or `<section>` for Livewire components
2. **CSS Class Naming**: Use `block [block_name] [block_name]-[style]` pattern on the **root div/section element** for precise CSS control
3. **Conditional Rendering**: Use `@if (!empty($displayOptions['key']))` for optional content
4. **Asset URLs**: Always use `asset_url()` helper for media assets
5. **Accessibility**: Include proper `alt` attributes and semantic HTML
6. **Responsive Design**: Use Bootstrap classes for responsive behavior
7. **Component Reusability**: Extract common patterns into separate components
8. **Lazy Loading**: Use `lazy` class and `data-src` for performance

## SCSS Styling

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

// Import animation mixins
@import 'mixins';

// Common styles for all hero-banner blocks
.block.hero-banner {
    position: relative;

    // Intersection Observer trigger
    &.in-view {
        .hero-banner-caption {
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

            .hero-banner-btn {
                opacity: 1;
                transform: translateY(0);
            }
        }
    }
}

// Default hero banner styles
.block.hero-banner.hero-banner-default {
    // Default-specific styles
    .hero-image-container {
        aspect-ratio: 16 / 9;
    }
}

// Fullscreen hero banner styles
.block.hero-banner.hero-banner-fullscreen {
    // Fullscreen-specific styles
    .hero-image-container {
        height: 100vh;
        width: 100%;
    }

    .hero-banner-caption {
        top: 50% !important;
        transform: translate(-50%, -50%);
    }
}

// Hero banner caption
.hero-banner-caption {
    @extend %overlay-caption;

    top: 25% !important;

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

    .hero-banner-btn {
        @include fade-in-up(0.6s);
    }
}

// Button styles
.hero-banner-btn {
    @extend %overlay-btn;
}

.hero-image-container {
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}

:not(.fullscreen) {
    & > .hero-image-container {
        aspect-ratio: 16 / 9;
    }

    & > .hero-banner-caption {
        top: 12% !important;

        h4 {
            font-weight: 500 !important;
            font-size: 2rem !important;
        }
    }
}

.fullscreen {
    & > .hero-image-container {
        width: 100%;
        height: 100vh;
    }
}
```

### SCSS Best Practices

1. **Import Order**: Follow Bootstrap's recommended import order
2. **Variable Overrides**: Override Bootstrap variables before importing Bootstrap
3. **Component Organization**: Create separate SCSS files for each component
4. **Mixins**: Use mixins for reusable animation and styling patterns
5. **Responsive Design**: Use Bootstrap's media query mixins consistently
6. **Naming Conventions**: Use BEM methodology for custom classes
7. **Performance**: Avoid deep nesting (max 3-4 levels)

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
    const heroBanners = document.querySelectorAll<HTMLElement>('.block.hero-banner');

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

### TypeScript Best Practices

1. **Strict Typing**: Use TypeScript strict mode and proper type annotations
2. **IIFE Pattern**: Wrap component code in immediately invoked function expressions
3. **Null Checks**: Always check for element existence before manipulation
4. **Event Handling**: Use proper event types and handle cleanup
5. **Performance**: Use Intersection Observer for scroll-based animations
6. **Modularity**: Keep components focused and single-purpose
7. **Error Handling**: Include try-catch blocks for critical operations

## Asset Management

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
            '@theme': path.resolve(__dirname, 'resources/themes/theme-name'),
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
