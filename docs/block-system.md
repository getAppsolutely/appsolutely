# BaseBlock Pattern for Livewire Components

This document explains how to use the `BaseBlock` pattern for creating reusable Livewire components that accept parameters and render views automatically.

## Overview

The `BaseBlock` is an abstract Livewire component that provides a standardized way to:
- Accept parameters through a `$data` array
- Automatically render views based on component name
- Provide helper methods for data access
- Allow customization through inheritance

## Basic Usage

### 1. Extend BaseBlock (Minimal Implementation)

```php
<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Livewire\BaseBlock;final class MyComponent extends BaseBlock
{
    // That's it! The BaseBlock handles everything automatically
}
```

### 2. Create the View

Create a view file at `themes/{theme}/views/livewire/my-component.blade.php`:

```blade
<div class="my-component">
    <h2>{{ $data['title'] ?? 'Default Title' }}</h2>
    <p>{{ $data['description'] ?? '' }}</p>
</div>
```

### 3. Use the Component

```blade
<livewire:my-component :data="[
    'title' => 'Custom Title',
    'description' => 'This is a custom description'
]" />
```

## Key Features

### Automatic View Rendering

The `BaseBlock` automatically renders views using the `themed_view()` helper. View names are automatically generated from the class name:

- `LocationMap` → `livewire.location-map`
- `ContactForm` → `livewire.contact-form`
- `MyComponent` → `livewire.my-component`

### Data Access in Views

In your Blade views, you can access data directly from the `$data` array:

```blade
<div>
    <h1>{{ $data['title'] ?? 'Default Title' }}</h1>
    <p>{{ $data['content'] ?? '' }}</p>
    
    @if(isset($data['items']))
        <ul>
            @foreach($data['items'] as $item)
                <li>{{ $item }}</li>
            @endforeach
        </ul>
    @endif
</div>
```

### Optional Customization

If you need custom logic, you can optionally override methods:

#### 1. Custom View Name

```php
final class MyComponent extends BaseBlock
{
    protected string $viewName = 'custom-view-name';
}
```

#### 2. Initialization Logic

```php
final class MyComponent extends BaseBlock
{
    protected function initializeComponent(): void
    {
        // Set default values
        if (!$this->hasData('title')) {
            $this->setData('title', 'Default Title');
        }
    }
}
```

#### 3. Custom Getter Methods

```php
final class MyComponent extends BaseBlock
{
    public function getTitle(): string
    {
        return (string) $this->getData('title', 'Default Title');
    }
}
```

## Examples

### Simple Component (Recommended)

```php
final class LocationMap extends BaseBlock
{
    // Empty! BaseBlock handles everything automatically
}
```

Usage:
```blade
<livewire:location-map :data="[
    'latitude' => 40.7128,
    'longitude' => -74.0060,
    'title' => 'New York Office',
    'address' => '123 Main St, New York, NY'
]" />
```

View (`themes/tabler/views/livewire/location-map.blade.php`):
```blade
<div class="location-map-container">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $data['title'] ?? 'Location' }}</h3>
        </div>
        <div class="card-body">
            @if(isset($data['address']))
                <div class="mb-3">
                    <strong>Address:</strong> {{ $data['address'] }}
                </div>
            @endif
            
            <div class="location-map" 
                 data-latitude="{{ $data['latitude'] ?? 0 }}" 
                 data-longitude="{{ $data['longitude'] ?? 0 }}" 
                 data-zoom="{{ $data['zoom'] ?? 15 }}"
                 style="height: 400px; width: 100%;">
                <!-- Map content -->
            </div>
        </div>
    </div>
</div>
```

### Component with Custom Logic

```php
final class ContactForm extends BaseBlock
{
    protected function initializeComponent(): void
    {
        // Set default submit URL if not provided
        if (!$this->hasData('submit_url')) {
            $this->setData('submit_url', route('contact.submit'));
        }
    }
}
```

## Best Practices

1. **Keep components minimal** - Let `BaseBlock` handle the heavy lifting
2. **Access data directly in views** - Use `$data['key']` with null coalescing
3. **Only add custom logic when needed** - Don't override methods unless necessary
4. **Use descriptive component names** - They become the view names automatically
5. **Document expected data structure** - Add comments in the component class

## Migration from Existing Components

To migrate existing Livewire components to use `BaseBlock`:

1. Change the parent class from `Component` to `BaseBlock`
2. Remove the `render()` method (handled automatically)
3. Move component properties to the `$data` array
4. Update the view to use `$data['key']` instead of component properties
5. Remove any unnecessary custom logic

## Benefits

- **Simplicity** - Components can be as simple as an empty class
- **Consistency** - All components follow the same pattern
- **Reusability** - Easy to reuse with different data
- **Maintainability** - Standardized structure makes code easier to maintain
- **Flexibility** - Easy to extend when custom logic is needed 
