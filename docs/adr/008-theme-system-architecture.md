# ADR 008: Multi-Theme System Architecture

**Status:** Accepted  
**Date:** 2025  
**Deciders:** Architecture Team

## Context

We need a flexible theming system that:

- Supports multiple themes
- Allows theme switching at runtime
- Maintains theme-specific assets and views
- Works with Laravel's view system
- Supports parent/child theme relationships

## Decision

We will implement a **Multi-Theme System** using the Qirolab Theme package with custom middleware and service layer for theme resolution and management.

### Implementation Details

1. **Theme Package**: Use `qirolab/laravel-theme` for core theme functionality
2. **Theme Service**: `ThemeService` handles theme resolution and setup logic
3. **Middleware**: `SetThemeMiddleware` applies theme on each request
4. **Theme Structure**: Themes in `themes/` directory with views, assets, config
5. **Parent Themes**: Support for theme inheritance via parent theme configuration
6. **View Namespaces**: Theme views automatically prioritized over default views

### Architecture

```
themes/
├── default/          # Default theme
│   ├── views/
│   ├── assets/
│   └── vite.config.ts
├── june/            # Custom theme
│   ├── views/
│   ├── assets/
│   └── vite.config.ts
└── tabler/          # Another theme
    └── views/
```

### Flow

1. **Request Arrives** → `SetThemeMiddleware` handles request
2. **Theme Resolution** → `ThemeService::resolveThemeName()` determines active theme
3. **Theme Application** → `ThemeService::setupTheme()` configures view finder
4. **View Rendering** → Laravel uses theme views when available

### Example

```php
use App\Config\BasicConfig;

// Theme Service
final readonly class ThemeService implements ThemeServiceInterface
{
    public function resolveThemeName(): ?string
    {
        $basicConfig = new BasicConfig();
        $basicTheme  = $basicConfig->theme();
        if (!empty($basicTheme) && file_exists(themed_absolute_path($basicTheme, 'views'))) {
            return $basicTheme;
        }
        return config('theme.active');
    }

    public function setupTheme(string $themeName, ?string $parentTheme = null): void
    {
        Theme::set($themeName, $parentTheme);
        // Configure view finder paths
    }
}

// Middleware
final class SetThemeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $themeName = $this->themeService->resolveThemeName();
        if ($themeName && $this->themeService->shouldApplyTheme($request->path())) {
            $this->themeService->setupTheme($themeName, $this->themeService->getParentTheme());
        }
        return $next($request);
    }
}
```

## Consequences

### Positive

- ✅ **Flexibility**: Easy to add new themes
- ✅ **Separation**: Theme logic separated from middleware
- ✅ **Testability**: Theme service can be tested independently
- ✅ **Maintainability**: Clear theme resolution logic
- ✅ **Reusability**: Theme service can be used in commands, jobs, etc.

### Negative

- ⚠️ **Complexity**: Additional layer for theme management
- ⚠️ **Performance**: Theme resolution on every request (mitigated by caching)
- ⚠️ **Dependency**: Relies on third-party package

### Mitigations

- Cache theme resolution results
- Document theme creation process
- Provide theme development guide

## References

- Theme Service: `app/Services/ThemeService.php`
- Theme Middleware: `app/Http/Middleware/SetThemeMiddleware.php`
- Theme Config: `config/theme.php`
- Theme Development Guide: `docs/theme-development-guide.md`
