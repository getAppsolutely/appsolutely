# Page Block System

## Overview

The Page Block System is a flexible, modular architecture for building dynamic page layouts with reusable components. It separates **structure definition** (schema), **visual configuration** (display_options), and **data querying** (query_options) to provide maximum flexibility and reusability.

## Core Concepts

### 1. Schema-Driven Configuration

Each block type defines a **schema** that describes what fields it accepts, their types, validation rules, and default values. This provides:

- Type safety and validation
- Automatic form generation in admin
- Consistent data structure
- Self-documenting configuration

### 2. Options-Based Rendering

Blocks receive two types of configuration:

- **`display_options`**: Visual/presentation settings (titles, layouts, styles, UI elements)
- **`query_options`**: Data fetching parameters (filters, sorting, pagination, limits)

This separation allows:

- Same block logic, different presentations
- Reusable data queries across pages
- Independent styling without affecting data
- Easier testing and maintenance

## Architecture

### Database Structure

```
┌─────────────────┐
│  page_blocks    │  (Block Definitions)
├─────────────────┤
│ id              │
│ title           │
│ reference       │
│ class           │  → Livewire component class
│ scope           │  → 'global' | 'page'
│ schema          │  → JSON: Field definitions
│ schema_values   │  → JSON: Default values for global scope
│ ...             │
└─────────────────┘
         │
         │ 1:N
         ▼
┌──────────────────┐
│ page_block_values│  (Configuration Instances)
├──────────────────┤
│ id               │
│ block_id         │
│ template         │
│ scripts          │
│ stylesheets      │
│ styles           │
│ display_options  │  → JSON: Visual config
│ query_options    │  → JSON: Data query config
└──────────────────┘
         ▲
         │ N:1
         │
┌──────────────────────┐
│ page_block_settings  │  (Page-Block Assignments)
├──────────────────────┤
│ id                   │
│ page_id              │
│ block_id             │
│ block_value_id       │
│ reference            │
│ sort                 │
│ status               │
│ published_at         │
│ expired_at           │
└──────────────────────┘
         │
         │ N:1
         ▼
┌─────────────────┐
│     pages       │
└─────────────────┘
```

### Model Relationships

#### PageBlock (Block Definition)

- Defines the block type and its schema
- Contains the Livewire component class
- Has default values via `schema_values`
- Can have many `PageBlockSettings` and `PageBlockValues`

#### PageBlockValue (Configuration Instance)

- Stores specific configuration for blocks
- Contains `display_options` and `query_options`
- Can be shared across multiple settings (for reusability)
- Contains rendering data (template, styles, scripts)

#### PageBlockSetting (Assignment)

- Links a Page to a Block with specific BlockValue
- Controls visibility (status, published_at, expired_at)
- Provides accessors: `displayOptionsValue` and `queryOptionsValue`
- Falls back to block's `schema_values` when no BlockValue exists

### Data Flow

```
Page Render Request
        │
        ▼
┌────────────────────────────────┐
│  BlockRendererService          │
│  - Loads PageBlockSettings     │
│  - Gets display_options via    │
│    displayOptionsValue accessor│
│  - Gets query_options via      │
│    queryOptionsValue accessor  │
└────────────────────────────────┘
        │
        ▼
┌────────────────────────────────┐
│  Livewire::mount()             │
│  - Passes $page array          │
│  - Passes $displayOptions      │
│  - Passes $queryOptions        │
└────────────────────────────────┘
        │
        ▼
┌────────────────────────────────┐
│  BaseBlock Component           │
│  - Merges with defaults        │
│  - Initializes component       │
│  - Loads data using            │
│    $queryOptions               │
│  - Renders view with           │
│    $displayOptions             │
└────────────────────────────────┘
        │
        ▼
┌────────────────────────────────┐
│  Blade View                    │
│  - Access via $displayOptions  │
│  - Access via $queryOptions    │
│  - Display loaded data         │
└────────────────────────────────┘
```

## Schema System

### Schema Definition Format

Schemas are defined in the `page_blocks.schema` JSON column:

```json
{
  "fieldName": {
    "type": "field_type",
    "label": "Human Readable Label",
    "description": "Optional description",
    "required": true|false,
    "default": "default_value",
    "validation_rules": {}
  }
}
```

### Supported Field Types

| Type          | Description            | Properties                       | Example Value           |
| ------------- | ---------------------- | -------------------------------- | ----------------------- |
| `text`        | Single line text       | `max_length`, `pattern`          | `"Hello World"`         |
| `textarea`    | Multi-line text        | `max_length`, `rows`             | `"Long description..."` |
| `number`      | Numeric value          | `min`, `max`, `step`             | `42`                    |
| `boolean`     | True/false value       | `default`                        | `true`                  |
| `select`      | Single choice dropdown | `options`                        | `"option1"`             |
| `multiSelect` | Multiple choice        | `options`, `max_selections`      | `["opt1", "opt2"]`      |
| `image`       | Image upload           | `max_size`, `allowed_types`      | `"/uploads/image.jpg"`  |
| `file`        | File upload            | `max_size`, `allowed_types`      | `"/uploads/file.pdf"`   |
| `date`        | Date picker            | `format`, `min_date`, `max_date` | `"2024-01-15"`          |
| `datetime`    | Date & time picker     | `format`                         | `"2024-01-15T10:30:00"` |
| `color`       | Color picker           | `format`                         | `"#ff0000"`             |
| `url`         | URL input              | `protocols`                      | `"https://example.com"` |
| `email`       | Email input            | -                                | `"user@example.com"`    |
| `table`       | Array of objects       | `fields`, `max_items`            | `[{"key": "value"}]`    |
| `object`      | Nested object          | `fields`                         | `{"nested": "value"}`   |

### Example Schema

```json
{
    "title": {
        "type": "text",
        "label": "Section Title",
        "description": "Main headline for the section",
        "required": true,
        "max_length": 100,
        "default": "Welcome"
    },
    "layout": {
        "type": "select",
        "label": "Layout Style",
        "required": true,
        "options": [
            { "value": "grid", "label": "Grid" },
            { "value": "list", "label": "List" },
            { "value": "masonry", "label": "Masonry" }
        ],
        "default": "grid"
    },
    "posts_per_page": {
        "type": "number",
        "label": "Posts Per Page",
        "min": 1,
        "max": 50,
        "default": 6
    }
}
```

### PageBlockSchemaService

The `PageBlockSchemaService` provides schema operations:

```php
use App\Services\PageBlockSchemaService;

$schemaService = app(PageBlockSchemaService::class);

// Get schema for a block
$schema = $schemaService->getBlockSchema($block);

// Validate values against schema
$validated = $schemaService->validateSchemaValues($schema, $inputValues);

// Get default values
$defaults = $schemaService->getDefaultValues($schema);

// Merge with defaults
$merged = $schemaService->mergeWithDefaults($schema, $values);

// Generate form config for admin
$formConfig = $schemaService->generateFormConfig($schema);
```

## Creating Page Blocks

### Step 1: Define the Block (Database)

Create a block record in `page_blocks` table:

```php
PageBlock::create([
    'title' => 'Article List',
    'reference' => 'article-list',
    'class' => 'App\\Livewire\\ArticleList',
    'scope' => 'page', // or 'global'
    'schema' => [
        'title' => [
            'type' => 'text',
            'label' => 'Section Title',
            'default' => 'Latest Articles'
        ],
        'layout' => [
            'type' => 'select',
            'label' => 'Layout',
            'options' => [
                ['value' => 'grid', 'label' => 'Grid'],
                ['value' => 'list', 'label' => 'List']
            ],
            'default' => 'grid'
        ]
    ],
    'schema_values' => [], // Default values for global scope
    'status' => 1
]);
```

### Step 2: Create Livewire Component

Extend `BaseBlock` to create your component:

```php
<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Repositories\ArticleRepository;
use Livewire\WithPagination;

final class ArticleList extends BaseBlock
{
    use WithPagination;

    // Define default display options
    protected array $defaultDisplayOptions = [
        'title'               => 'Latest Articles',
        'subtitle'            => 'Stay updated',
        'show_featured_image' => true,
        'show_excerpt'        => true,
        'show_author'         => true,
        'show_date'           => true,
        'layout'              => 'grid',
    ];

    // Define default query options
    protected array $defaultQueryOptions = [
        'posts_per_page'  => 6,
        'category_filter' => '',
        'order_by'        => 'published_at',
        'order_direction' => 'desc',
    ];

    // Provide additional data to the view
    protected function getExtraData(): array
    {
        return [
            'articles' => $this->loadArticles(),
        ];
    }

    private function loadArticles()
    {
        $repository = app(ArticleRepository::class);
        $query = $repository->getPublishedArticles($this->queryOptions);

        return $query->paginate($this->queryOptions['posts_per_page']);
    }
}
```

### Step 3: Create the View

Create view at `themes/{theme}/views/livewire/article-list.blade.php`:

```blade
<div class="article-list {{ $displayOptions['layout'] ?? 'grid' }}">
    @if(!empty($displayOptions['title']))
        <h2 class="section-title">{{ $displayOptions['title'] }}</h2>
    @endif

    @if(!empty($displayOptions['subtitle']))
        <p class="section-subtitle">{{ $displayOptions['subtitle'] }}</p>
    @endif

    <div class="articles-container layout-{{ $displayOptions['layout'] ?? 'grid' }}">
        @foreach($articles as $article)
            <article class="article-card">
                @if($displayOptions['show_featured_image'] ?? true)
                    <img src="{{ $article->featured_image }}" alt="{{ $article->title }}">
                @endif

                <h3>{{ $article->title }}</h3>

                @if($displayOptions['show_excerpt'] ?? true)
                    <p>{{ $article->excerpt }}</p>
                @endif

                @if($displayOptions['show_date'] ?? true)
                    <time>{{ $article->published_at->format('M d, Y') }}</time>
                @endif

                @if($displayOptions['show_author'] ?? true)
                    <span class="author">By {{ $article->author->name }}</span>
                @endif
            </article>
        @endforeach
    </div>

    {{ $articles->links() }}
</div>
```

### Step 4: Assign to Page

Create a `PageBlockSetting` to add the block to a page:

```php
PageBlockSetting::create([
    'page_id' => $page->id,
    'block_id' => $block->id,
    'block_value_id' => $blockValue->id, // or null
    'reference' => 'article-list-home',
    'sort' => 1,
    'status' => 1,
    'published_at' => now(),
]);
```

## BaseBlock Component

The `BaseBlock` is an abstract Livewire component providing standardized functionality:

### Key Features

1. **Automatic View Resolution**: Auto-generates view names from class names
2. **Style Variants**: Supports multiple view styles (e.g., `article-list_card.blade.php`)
3. **Options Management**: Merges default options with provided options
4. **Data Access Helpers**: `getData()`, `setData()`, `hasData()`
5. **Visibility Control**: Respects `published_at` and `expired_at` dates

### Public Properties

- `array $page`: Current page data
- `?Model $model`: Associated model (if any)
- `array $displayOptions`: Visual configuration
- `array $queryOptions`: Data query configuration
- `string $style`: Style variant (default: 'default')

### Protected Properties

- `array $defaultDisplayOptions`: Default display configuration
- `array $defaultQueryOptions`: Default query configuration
- `string $viewName`: Custom view name (auto-generated if empty)
- `?Carbon $publishedAt`: Publication date
- `?Carbon $expiredAt`: Expiration date

### Key Methods

```php
// Mount component (called automatically)
public function mount(array $page, array $data = []): void

// Override to add initialization logic
protected function initializeComponent(Container $container): void

// Override to provide additional view data
protected function getExtraData(): array

// Data access helpers
protected function getData(string $key, $default = null)
protected function setData(string $key, $value): void
protected function hasData(string $key): bool

// Check visibility
protected function isVisible(): bool
```

## Display Options vs Query Options

### Display Options

**Purpose**: Control visual presentation and UI elements

**Examples**:

- Titles, subtitles, descriptions
- Layout types (grid, list, masonry)
- Show/hide flags (show_author, show_date)
- Button text and labels
- Color schemes
- CSS classes
- Image display settings

```php
$displayOptions = [
    'title' => 'Featured Products',
    'subtitle' => 'Check out our latest offerings',
    'layout' => 'grid',
    'columns' => 3,
    'show_price' => true,
    'show_add_to_cart' => true,
    'button_text' => 'Add to Cart',
    'style' => 'modern',
];
```

### Query Options

**Purpose**: Control data fetching and filtering

**Examples**:

- Pagination (posts_per_page, per_page)
- Sorting (order_by, order_direction)
- Filters (category, tag, status)
- Limits and offsets
- Search terms
- Date ranges
- Related data loading

```php
$queryOptions = [
    'posts_per_page' => 12,
    'category_filter' => 'electronics',
    'order_by' => 'price',
    'order_direction' => 'asc',
    'min_price' => 100,
    'max_price' => 1000,
    'in_stock_only' => true,
];
```

### Best Practices

1. **Display Options**: Should never affect what data is loaded
2. **Query Options**: Should never contain text/labels for UI
3. **Keep them independent**: Changing display shouldn't require changing queries
4. **Use defaults**: Define sensible defaults in `defaultDisplayOptions` and `defaultQueryOptions`
5. **Validate both**: Ensure both are validated via schema

## Admin Interface

### Dynamic Form Generation

Forms in the admin interface are automatically generated based on block schemas:

```php
// In PageBlockSettingForm.php
$this->textarea('blockValue.display_options', __t('Display Options'))
    ->rows(10)
    ->help(__t('JSON format for display options'));

$this->textarea('blockValue.query_options', __t('Query Options'))
    ->rows(10)
    ->help(__t('JSON format for query options'));
```

### Versioning System

When you modify `display_options` or `query_options` on a `PageBlockValue` that's used by multiple settings:

- If block scope is `'page'`: A new `PageBlockValue` is automatically created
- If block scope is `'global'`: The existing value is updated

This is handled by `PageBlockSetting::checkAndCreateNewBlockValue()`.

## Block Scope

### Page Scope (`scope = 'page'`)

- Each page can have different configurations
- Editing one page's block doesn't affect others
- Creates new `PageBlockValue` when modified

### Global Scope (`scope = 'global'`)

- Shared configuration across all pages
- Editing affects all instances
- Uses block's `schema_values` as defaults
- Updates existing `PageBlockValue`

## Best Practices

### 1. Component Design

- **Keep components minimal**: Let `BaseBlock` handle standard functionality
- **Use defaults**: Define sensible `defaultDisplayOptions` and `defaultQueryOptions`
- **Separate concerns**: Display logic in component, data fetching in repositories
- **Type everything**: Use typed properties and return types

### 2. Schema Design

- **Use descriptive labels**: Make field purposes clear
- **Provide descriptions**: Add help text for complex fields
- **Set reasonable defaults**: Users should get working blocks out of the box
- **Validate appropriately**: Use min/max, required, patterns

### 3. View Design

- **Use null coalescing**: Always provide fallbacks (`$displayOptions['title'] ?? 'Default'`)
- **Respect display options**: Check flags before showing elements
- **Keep logic minimal**: Complex logic belongs in the component
- **Theme awareness**: Use themed helpers and paths

### 4. Performance

- **Eager load relationships**: Use `with()` in repositories
- **Implement pagination**: For lists, always paginate
- **Cache when appropriate**: Cache expensive queries
- **Optimize N+1**: Be mindful of query counts

### 5. Reusability

- **Share BlockValues**: Reuse configurations when possible
- **Use inheritance**: Extend BaseBlock, don't duplicate
- **Component composition**: Build complex blocks from simple ones
- **Document your blocks**: Add clear docblocks

## Troubleshooting

### Common Issues

**Block not rendering**

- Check class exists and extends `BaseBlock`
- Verify view file exists in current theme
- Check `published_at` and `expired_at` dates
- Ensure block is active (`status = 1`)

**Options not loading**

- Check JSON syntax in database
- Verify `blockValue` relationship is loaded
- Check accessor names: `displayOptionsValue`, `queryOptionsValue`

**Schema validation failing**

- Review validation rules in schema
- Check field types match input
- Ensure required fields are provided

**Data not appearing**

- Verify `queryOptions` are being used in repository calls
- Check `getExtraData()` returns correct keys
- Ensure pagination is working

## Example Blocks

### Simple Block (No Data Loading)

```php
final class HeroBanner extends BaseBlock
{
    protected array $defaultDisplayOptions = [
        'title' => 'Welcome',
        'subtitle' => 'Discover amazing things',
        'background_image' => '/images/default-hero.jpg',
        'button_text' => 'Learn More',
        'button_url' => '/about',
    ];
}
```

### Complex Block (With Data Loading)

```php
final class ProductShowcase extends BaseBlock
{
    use WithPagination;

    protected array $defaultDisplayOptions = [
        'title' => 'Featured Products',
        'layout' => 'grid',
        'columns' => 4,
        'show_price' => true,
        'show_rating' => true,
    ];

    protected array $defaultQueryOptions = [
        'limit' => 12,
        'category' => '',
        'featured_only' => true,
        'order_by' => 'popularity',
    ];

    protected function initializeComponent(Container $container): void
    {
        $this->productRepository = $container->make(ProductRepository::class);
    }

    protected function getExtraData(): array
    {
        return [
            'products' => $this->loadProducts(),
            'categories' => $this->loadCategories(),
        ];
    }

    private function loadProducts()
    {
        return $this->productRepository
            ->getProducts($this->queryOptions)
            ->paginate($this->queryOptions['limit']);
    }

    private function loadCategories()
    {
        return $this->productRepository->getActiveCategories();
    }
}
```

## Further Reading

- [Theme Development Guide](theme-development-guide.md)
- [Development Workflow](development-workflow.md)
- [ADR 008: Theme System Architecture](adr/008-theme-system-architecture.md)

## Glossary

- **Block**: A reusable UI component type defined in `page_blocks`
- **BlockValue**: A specific configuration instance of a block
- **BlockSetting**: The assignment of a block to a page with a specific value
- **Schema**: JSON definition of acceptable fields and validation rules
- **Display Options**: Visual/UI configuration
- **Query Options**: Data fetching configuration
- **BaseBlock**: Abstract Livewire component for building blocks
- **Scope**: Whether a block is `global` (shared) or `page` (independent)
