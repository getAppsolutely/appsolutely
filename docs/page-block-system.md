# PageBlock Schema System

## Overview

The PageBlock Schema System provides a flexible and structured way to define configuration fields for page blocks. It allows you to specify what data each block can accept, validate that data, and generate appropriate form interfaces.

## Architecture

### Database Structure

- **`page_blocks.schema`** (JSON): Defines the field structure and validation rules for each block

### Core Components

1. **SchemaService**: Handles schema validation, default values, and form generation
2. **PageBlockSettingController**: Enhanced controller with schema-aware form handling
3. **Schema Validation**: Built-in Laravel validation rules based on schema definitions

## Schema Definition Format

### Basic Field Structure

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

### Field Properties

#### Common Properties

- **`type`** (required): The field type as listed above
- **`label`** (optional): Human-readable label for the field
- **`description`** (optional): Help text for the field
- **`required`** (optional): Whether the field is required (default: false)
- **`default`** (optional): Default value for the field

#### Validation Properties

- **`max_length`**: Maximum character length (for text fields)
- **`min`/`max`**: Numeric range (for number fields)
- **`pattern`**: Regular expression pattern (for text fields)
- **`max_size`**: Maximum file size (for file/image fields)
- **`allowed_types`**: Allowed file extensions (for file fields)
- **`options`**: Array of options for select fields
- **`max_items`**: Maximum number of items (for table fields)

## Example Schemas

### Hero Section Schema

```json
{
    "title": {
        "type": "text",
        "label": "Hero Title",
        "description": "Main headline for the hero section",
        "required": true,
        "max_length": 100,
        "default": "Welcome to Our Site"
    },
    "subtitle": {
        "type": "textarea",
        "label": "Hero Subtitle",
        "description": "Supporting text below the main title",
        "required": false,
        "max_length": 200,
        "default": "Discover amazing features and services"
    },
    "background_image": {
        "type": "image",
        "label": "Background Image",
        "description": "Hero background image",
        "required": false,
        "max_size": "5MB"
    },
    "cta_button": {
        "type": "object",
        "label": "Call to Action Button",
        "fields": {
            "text": {
                "type": "text",
                "label": "Button Text",
                "required": true,
                "default": "Get Started"
            },
            "url": {
                "type": "text",
                "label": "Button URL",
                "required": true,
                "default": "/contact"
            },
            "style": {
                "type": "select",
                "label": "Button Style",
                "options": [
                    { "value": "primary", "label": "Primary" },
                    { "value": "secondary", "label": "Secondary" },
                    { "value": "outline", "label": "Outline" }
                ],
                "default": "primary"
            }
        }
    }
}
```

### Navigation Menu Schema

```json
{
    "mainNav": {
        "type": "text",
        "label": "Main Navigation",
        "description": "The main navigation text",
        "required": true,
        "max_length": 100,
        "default": "Home"
    },
    "menus": {
        "type": "table",
        "label": "Menu Items",
        "description": "List of menu items",
        "max_items": 10,
        "fields": {
            "label": {
                "type": "text",
                "label": "Menu Label",
                "required": true,
                "max_length": 50
            },
            "url": {
                "type": "text",
                "label": "URL",
                "required": true
            },
            "image": {
                "type": "image",
                "label": "Menu Image",
                "max_size": "2MB"
            },
            "is_active": {
                "type": "boolean",
                "label": "Active",
                "default": true
            }
        }
    },
    "layout": {
        "type": "select",
        "label": "Layout Style",
        "description": "Choose the layout style",
        "required": true,
        "options": [
            { "value": "horizontal", "label": "Horizontal" },
            { "value": "vertical", "label": "Vertical" },
            { "value": "dropdown", "label": "Dropdown" }
        ],
        "default": "horizontal"
    },
    "colors": {
        "type": "object",
        "label": "Color Settings",
        "fields": {
            "primary": {
                "type": "color",
                "label": "Primary Color",
                "default": "#007bff"
            },
            "secondary": {
                "type": "color",
                "label": "Secondary Color",
                "default": "#6c757d"
            }
        }
    }
}
```

## Example Schema Values

```json
{
    "mainNav": "Main Menu",
    "menus": [
        {
            "label": "Home",
            "url": "/home",
            "image": "/uploads/menu-home.jpg",
            "is_active": true
        },
        {
            "label": "About",
            "url": "/about",
            "image": null,
            "is_active": false
        }
    ],
    "layout": "horizontal",
    "colors": {
        "primary": "#007bff",
        "secondary": "#6c757d"
    }
}
```

## Usage

### 1. Defining a Block Schema

When creating or updating a page block, define its schema in the `schema` field:

```php
$block = PageBlock::create([
    'title' => 'Hero Section',
    'reference' => 'hero-section',
    'class' => 'App\\Http\\Livewire\\HeroSection',
    'schema' => [
        'title' => [
            'type' => 'text',
            'label' => 'Hero Title',
            'required' => true,
            'max_length' => 100
        ],
        // ... more fields
    ]
]);
```

### 2. Using Schema in Controllers

The `PageBlockSettingController` automatically handles schema validation:

### 3. Accessing Schema Values in Livewire Components

```php
class HeroSection extends Component
{
    public $title;
    public $subtitle;
    public $backgroundImage;

    public function mount($schemaValues = [])
    {
        $this->title = $schemaValues['title'] ?? 'Default Title';
        $this->subtitle = $schemaValues['subtitle'] ?? '';
        $this->backgroundImage = $schemaValues['background_image'] ?? null;
    }

    public function render()
    {
        return view('livewire.hero-section');
    }
}
```

### 4. Schema Validation

The system automatically validates schema values against the defined rules:

```php
use App\Services\PageBlockSchemaService;

$schemaService = app(PageBlockSchemaService::class);
$block = PageBlock::find($blockId);
$schema = $schemaService->getBlockSchema($block);

try {
    $validatedValues = $schemaService->validateSchemaValues($schema, $inputValues);
} catch (ValidationException $e) {
    // Handle validation errors
    $errors = $e->errors();
}
```

## Admin Interface

### Dynamic Form Generation

The admin interface automatically generates form fields based on the block's schema:

1. When selecting a block, the system loads its schema
2. Form fields are dynamically generated based on the schema definition
3. Validation rules are automatically applied
4. Help text shows field descriptions and requirements

### Schema Field Information

The form displays helpful information about available fields:

- Field names and types
- Required/optional status
- Field descriptions
- Validation rules

## Best Practices

### 1. Schema Design

- **Use descriptive labels**: Make field labels clear and user-friendly
- **Provide descriptions**: Add helpful descriptions for complex fields
- **Set reasonable defaults**: Provide sensible default values where appropriate
- **Use appropriate field types**: Choose the right field type for the data

### 2. Validation Rules

- **Set max_length for text fields**: Prevent overly long input
- **Use min/max for numbers**: Ensure values are within acceptable ranges
- **Limit file sizes**: Set reasonable file size limits for uploads
- **Validate URLs and emails**: Use appropriate validation for these fields

### 3. Nested Structures

- **Use objects for related fields**: Group related fields together
- **Use tables for repeatable content**: For lists of similar items
- **Keep nesting shallow**: Avoid deeply nested structures

### 4. Performance

- **Limit table max_items**: Set reasonable limits for table fields
- **Optimize file uploads**: Use appropriate file size limits
- **Cache schema data**: Consider caching frequently accessed schemas

## Migration from Existing System

If you have existing blocks without schemas:

1. **Run the schema seeder**: `php artisan db:seed --class=PageBlockSchemaSeeder`
2. **Update existing blocks**: Add schemas to blocks that don't have them
3. **Test validation**: Ensure existing data passes new validation rules
4. **Update Livewire components**: Modify components to use schema values

## Troubleshooting

### Common Issues

1. **Validation errors**: Check that schema values match the defined validation rules
2. **Missing fields**: Ensure all required fields are provided
3. **Type mismatches**: Verify that values match the expected field types
4. **File upload issues**: Check file size and type restrictions

### Debugging

- Use `dd($schema)` to inspect schema definitions
- Check validation errors with `$e->errors()`

## Future Enhancements

Potential improvements to consider:

1. **Conditional fields**: Show/hide fields based on other field values
2. **Computed values**: Auto-calculate values based on other fields
3. **Field dependencies**: Make fields depend on other field values
4. **Custom field types**: Add support for custom field types
5. **Schema versioning**: Support for schema evolution over time
6. **Import/Export**: Tools for importing/exporting schema definitions
