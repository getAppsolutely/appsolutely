# Theme Documentation

## Overview

This is a modern Laravel theme built on **Bootstrap 5.3.0** with a clean, minimalist design approach. It uses **Vite** for asset compilation and follows Laravel's component-based architecture. This documentation serves as a template for any theme implementation.

## 🏗️ Architecture

### Theme Structure
```
themes/{theme-name}/
├── sass/
│   ├── app.scss          # Main SCSS entry point
│   ├── _variables.scss   # Bootstrap variable overrides
│   └── components/       # Component-specific styles
│       └── _header.scss  # Header component styles
├── js/
│   ├── app.js           # Main JS entry point
│   ├── bootstrap.js     # Bootstrap 5 + dependencies setup
│   ├── assets.js        # Asset imports for Vite
│   └── components/      # Component-specific JavaScript
│       └── header.js    # Header component functionality
├── views/
│   ├── layouts/         # Blade layout templates
│   ├── auth/           # Authentication views
│   ├── pages/          # Page-specific views
│   └── livewire/       # Livewire components
├── images/             # Theme-specific images
└── vite.config.js      # Vite configuration
```

## 🎨 Design System

### Color Palette
- **Primary**: `#3490dc` (Blue)
- **Secondary**: `#6c757d` (Gray)
- **Success**: `#38c172` (Green)
- **Info**: `#6cb2eb` (Light Blue)
- **Warning**: `#ffed4a` (Yellow)
- **Danger**: `#e3342f` (Red)
- **Light**: `#f8f9fa` (Light Gray)
- **Dark**: `#343a40` (Dark Gray)

### Typography
- **Font Family**: Nunito (Google Fonts)
- **Base Font Size**: 0.9rem
- **Line Height**: 1.6

### Spacing & Layout
- **Border Radius**: 0.375rem (default), 0.25rem (small), 0.5rem (large)
- **Container Max Widths**: Responsive breakpoints following Bootstrap 5 standards
- **Grid System**: Bootstrap 5's 12-column grid

## 🛠️ Development Setup

### Asset Compilation
The theme uses **Vite** for asset compilation with the following configuration:

```javascript
// vite.config.js
export default defineConfig({
    plugins: [
        laravel({
            input: [
                "themes/{theme-name}/sass/app.scss",
                "themes/{theme-name}/js/app.js"
            ],
            buildDirectory: "build/themes/{theme-name}",
        }),
    ],
    resolve: {
        alias: {
            '@': '/themes/{theme-name}/js',
            '~bootstrap': path.resolve('node_modules/bootstrap'),
        }
    }
});
```

### Development Server
- **Port**: 5177
- **HMR**: Enabled for hot module replacement
- **Docker Support**: Configured for containerized development

## 📦 Dependencies

### Frontend Libraries
- **Bootstrap 5.3.0**: CSS framework (no jQuery dependency)
- **Lodash**: Utility functions
- **Axios**: HTTP client for AJAX requests
- **Vue.js**: Available for reactive components (optional)

### Build Tools
- **Vite**: Fast build tool and dev server
- **Sass**: CSS preprocessor
- **Laravel Vite Plugin**: Integration with Laravel

## 🎯 Usage Patterns

### Layout System

#### Main Layout (`app.blade.php`)
```blade
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Meta tags and CSRF token -->
    @vite(['themes/' . app_theme() . '/sass/app.scss', 'themes/' . app_theme() . '/js/app.js'], app_theme())
</head>
<body>
    @yield('content')
</body>
</html>
```

#### Guest Layout (`guest.blade.php`)
Used for authentication pages and public-facing content.

### Component Architecture

#### Blade Components
- Use `<x-app-layout>` for authenticated pages
- Use `<x-guest-layout>` for public pages
- Follow Laravel's component naming conventions

#### Livewire Components
Located in `views/livewire/`:
- `header.blade.php`: Site header component
- `footer.blade.php`: Site footer component
- `feature.blade.php`: Feature showcase component
- `testimonial.blade.php`: Testimonial component

### Styling Guidelines

#### Custom CSS Classes
```scss
// Custom utility classes
.text-nunito {
    font-family: 'Nunito', sans-serif;
}
```

#### Bootstrap Integration
- All Bootstrap 5 components are available
- Custom variables override Bootstrap defaults
- Utility classes follow Bootstrap conventions

## 🔧 Development Workflow

### Adding New Styles
1. **Variables**: Add to `sass/_variables.scss`
2. **Custom Styles**: Add to `sass/app.scss` after Bootstrap imports
3. **Component Styles**: Create new SCSS files in `sass/components/` and import in `app.scss`

### Adding New JavaScript
1. **Dependencies**: Install via npm and import in `js/bootstrap.js`
2. **Custom Logic**: Add to `js/app.js` or create new modules in `js/components/`
3. **Bootstrap Components**: Initialize in `js/bootstrap.js` DOMContentLoaded event

### Creating New Views
1. **Layout**: Choose appropriate layout (`app` or `guest`)
2. **Components**: Use existing Blade components or create new ones
3. **Styling**: Use Bootstrap classes and custom theme classes

### Adding New Pages
1. **Route**: Define in appropriate route file
2. **Controller**: Create controller method
3. **View**: Create Blade template in appropriate directory
4. **Assets**: Ensure Vite compilation includes new assets

## 🎨 Design Principles

### Visual Hierarchy
- Clean, minimalist design
- Consistent spacing using Bootstrap's spacing system
- Clear typography hierarchy with Nunito font
- Subtle shadows and transitions for depth

### Responsive Design
- Mobile-first approach
- Bootstrap 5's responsive grid system
- Flexible containers and components
- Touch-friendly interface elements

### Accessibility
- Semantic HTML structure
- ARIA labels where appropriate
- Keyboard navigation support
- High contrast color ratios

## 🚀 Performance Considerations

### Asset Optimization
- Vite handles code splitting and tree shaking
- CSS is minified in production
- JavaScript is bundled and optimized
- Images should be optimized separately

### Loading Strategy
- Critical CSS inlined in layout
- Non-critical CSS loaded asynchronously
- JavaScript loaded after DOM content
- Fonts loaded with `dns-prefetch`

## 🔍 Debugging

### Development Tools
- **Vite Dev Server**: Hot module replacement
- **Browser DevTools**: CSS and JavaScript debugging
- **Laravel Debugbar**: Backend debugging
- **Bootstrap Components**: Available globally as `window.bootstrap`

### Common Issues
- **Vite Build**: Check `vite.config.js` for correct paths
- **Bootstrap Components**: Ensure proper initialization in `bootstrap.js`
- **CSS Conflicts**: Use specific selectors or `!important` sparingly
- **Font Loading**: Verify Google Fonts are loading correctly

## 📚 Best Practices

### Code Organization
- Keep SCSS files modular and well-commented
- Use semantic class names
- Follow BEM methodology for complex components
- Maintain consistent file structure

### Performance
- Minimize CSS and JavaScript bundle sizes
- Use lazy loading for images and components
- Optimize critical rendering path
- Cache static assets appropriately

### Maintainability
- Document custom components and utilities
- Use consistent naming conventions
- Keep dependencies up to date
- Test across different browsers and devices

## 🔄 Theme Customization

### Color Scheme
Modify `sass/_variables.scss` to change the color palette:
```scss
$primary: #your-primary-color;
$secondary: #your-secondary-color;
// ... other color variables
```

### Typography
Update font settings in `sass/_variables.scss`:
```scss
$font-family-sans-serif: 'Your-Font', sans-serif;
$font-size-base: 1rem;
```

### Layout
Customize spacing and layout in `sass/_variables.scss`:
```scss
$spacer: 1rem;
$border-radius: 0.5rem;
$container-max-widths: (
  // your custom breakpoints
);
```

## 🎯 Integration with Laravel

### Blade Templates
- Use Laravel's Blade syntax for dynamic content
- Leverage Laravel's localization features
- Implement CSRF protection for forms
- Use Laravel's authentication helpers

### Livewire Integration
- Components in `views/livewire/` directory
- Follow Livewire naming conventions
- Use Alpine.js for client-side interactions
- Implement proper state management

### Asset Management
- Use `@vite()` directive for asset inclusion
- Leverage Vite's hot module replacement
- Implement proper cache busting
- Use Laravel Mix for additional processing if needed

## ⚠️ Important Guidelines

### Avoid Hard-Coded Theme Names
**❌ Don't do this:**
```php
// Hard-coded theme name
$theme = 'june';
$path = "themes/{$theme}/assets";
```

**✅ Do this instead:**
```php
// Use configuration or helper functions
$theme = config('appsolutely.theme.name');
$theme = app_theme();
$path = "themes/{$theme}/assets";
```

### Use Generic File Names
**❌ Don't do this:**
```scss
// Theme-specific class names
.june-header { }
.june-navbar { }
```

**✅ Do this instead:**
```scss
// Generic, reusable class names
.header { }
.navbar { }
```

### Component Organization
- **Create separate files** for different functionalities
- **Use generic naming** that works across themes
- **Follow consistent patterns** for file organization
- **Document component dependencies** clearly

### Asset Management
- **Use `themed_assets()` helper** for asset resolution
- **Import assets in `assets.js`** for Vite processing
- **Use relative paths** in component files
- **Avoid absolute theme paths** in code

### Configuration
- **Use `app_theme()` helper** for theme name
- **Access theme config** via `config('appsolutely.theme.*')`
- **Use environment variables** for theme-specific settings
- **Keep theme logic** in configuration files

---

This documentation serves as a comprehensive guide for developing with any theme. Follow these patterns and conventions to maintain consistency and ensure the theme remains maintainable and scalable across different implementations. 
