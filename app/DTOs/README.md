# Laravel Data Usage Guide

This guide explains how to use `spatie/laravel-data` in this project for type-safe data transfer objects (DTOs).

## Overview

Laravel Data provides a powerful way to work with data in Laravel applications:

- **Type Safety**: Strong typing for data structures
- **Automatic Validation**: Built-in validation rules
- **Transformation**: Easy conversion between models, arrays, and JSON
- **API Resources**: Seamless integration with API responses

## Project Structure

Data Transfer Objects are located in `app/DTOs/`:

```
app/DTOs/
├── ArticleData.php
└── ArticleCategoryData.php
```

## Basic Usage

### Creating Data Objects

#### From Eloquent Model

```php
use App\DTOs\ArticleData;
use App\Models\Article;

$article = Article::with('categories')->find(1);
$articleData = ArticleData::fromModel($article);
```

#### From Array

```php
$data = ArticleData::from([
    'title' => 'My Article',
    'content' => 'Article content...',
    'status' => 1,
]);
```

#### From Request

```php
use App\DTOs\ArticleData;
use Illuminate\Http\Request;

public function store(Request $request)
{
    // Automatic validation and type conversion
    $articleData = ArticleData::from($request->all());

    // Access typed properties
    echo $articleData->title;
    echo $articleData->published_at->format('Y-m-d');
}
```

### Converting to Different Formats

#### To Array

```php
$array = $articleData->toArray();
```

#### To JSON

```php
$json = $articleData->toJson();
// Or
return response()->json($articleData);
```

#### To Collection

```php
$collection = ArticleData::collection($articles);
```

## Example: Using in Controllers

### API Controller Example

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\DTOs\ArticleData;
use App\Http\Controllers\Api\BaseApiController;
use App\Models\Article;
use App\Repositories\ArticleRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ArticleController extends BaseApiController
{
    public function __construct(
        protected ArticleRepository $articleRepository
    ) {}

    /**
     * Get article by slug
     */
    public function show(string $slug): JsonResponse
    {
        $article = $this->articleRepository
            ->findActiveBySlug($slug, now());

        if (! $article) {
            return $this->error('Article not found', 404);
        }

        // Load relationships if needed
        $article->load('categories');

        // Convert to Data object
        $articleData = ArticleData::fromModel($article);

        return $this->success($articleData);
    }

    /**
     * List articles
     */
    public function index(Request $request): JsonResponse
    {
        $articles = $this->articleRepository
            ->getPublishedArticles($request->all())
            ->with('categories')
            ->get();

        // Convert collection to Data collection
        $articlesData = ArticleData::collection($articles);

        return $this->success($articlesData);
    }

    /**
     * Create article
     */
    public function store(Request $request): JsonResponse
    {
        // Validate and create Data object from request
        $articleData = ArticleData::from($request->all());

        // Create article from Data object
        $article = Article::create($articleData->toArray());

        return $this->success(
            ArticleData::fromModel($article),
            'Article created successfully'
        );
    }

    /**
     * Update article
     */
    public function update(Request $request, Article $article): JsonResponse
    {
        // Validate and create Data object
        $articleData = ArticleData::from($request->all());

        // Update article
        $article->update($articleData->toArray());

        return $this->success(
            ArticleData::fromModel($article->fresh()),
            'Article updated successfully'
        );
    }
}
```

## Example: Using in Services

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\DTOs\ArticleData;
use App\Models\Article;
use App\Repositories\ArticleRepository;

final readonly class ArticleService
{
    public function __construct(
        protected ArticleRepository $articleRepository
    ) {}

    /**
     * Get article as Data object
     */
    public function getArticleData(int $id): ?ArticleData
    {
        $article = $this->articleRepository->find($id);

        if (! $article) {
            return null;
        }

        $article->load('categories');
        return ArticleData::fromModel($article);
    }

    /**
     * Get published articles as Data collection
     */
    public function getPublishedArticlesData(array $filters = []): DataCollection
    {
        $articles = $this->articleRepository
            ->getPublishedArticles($filters)
            ->with('categories')
            ->get();

        return ArticleData::collection($articles);
    }
}
```

## Validation

Laravel Data automatically validates data when creating objects. Add validation attributes:

```php
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Email;

final class ArticleData extends Data
{
    public function __construct(
        #[Required]
        #[Max(255)]
        public string $title,

        #[Required]
        public string $content,

        // ... other properties
    ) {}
}
```

## Custom Transformation

### Mapping Input/Output Names

```php
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapOutputName;

final class ArticleData extends Data
{
    public function __construct(
        #[MapInputName('article_title')]
        #[MapOutputName('title')]
        public string $title,
    ) {}
}
```

### Custom Casts

```php
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

final class ArticleData extends Data
{
    public function __construct(
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d')]
        public Carbon $published_at,
    ) {}
}
```

## Best Practices

### 1. Always Use Type Hints

```php
// ✅ Good
public function getArticle(int $id): ?ArticleData

// ❌ Bad
public function getArticle($id)
```

### 2. Load Relationships Before Converting

```php
// ✅ Good
$article->load('categories');
$data = ArticleData::fromModel($article);

// ❌ Bad - categories will be null
$data = ArticleData::fromModel($article);
$article->load('categories');
```

### 3. Use Data Objects in API Responses

```php
// ✅ Good - Type-safe and validated
public function index(): JsonResponse
{
    $articles = ArticleData::collection($this->getArticles());
    return $this->success($articles);
}
```

### 4. Validate Early

```php
// ✅ Good - Validation happens at creation
$articleData = ArticleData::from($request->all());

// ❌ Bad - Manual validation
$request->validate([...]);
$articleData = new ArticleData(...);
```

## Common Patterns

### Pattern 1: API Resource Replacement

Instead of Laravel API Resources, use Data objects:

```php
// Before (API Resource)
return new ArticleResource($article);

// After (Data Object)
return ArticleData::fromModel($article);
```

### Pattern 2: Service Layer Data Transfer

```php
// Service returns Data object
public function getArticleData(int $id): ?ArticleData
{
    $article = $this->repository->find($id);
    return $article ? ArticleData::fromModel($article) : null;
}

// Controller uses Data object
public function show(int $id): JsonResponse
{
    $articleData = $this->articleService->getArticleData($id);

    if (! $articleData) {
        return $this->error('Not found', 404);
    }

    return $this->success($articleData);
}
```

### Pattern 3: Request Validation

```php
public function store(Request $request): JsonResponse
{
    try {
        $articleData = ArticleData::from($request->all());
    } catch (ValidationException $e) {
        return $this->failValidation($e->errors());
    }

    // Use validated data
    $article = Article::create($articleData->toArray());
    return $this->success(ArticleData::fromModel($article));
}
```

## Advanced Features

### Nested Data Objects

```php
// ArticleData includes ArticleCategoryData
$articleData = ArticleData::fromModel($article);
$categories = $articleData->categories; // DataCollection of ArticleCategoryData
```

### Collections

```php
use Spatie\LaravelData\DataCollection;

$articles = Article::all();
$articlesData = ArticleData::collection($articles);

// Iterate
foreach ($articlesData as $articleData) {
    echo $articleData->title;
}
```

### Conditional Properties

```php
public function __construct(
    public string $title,
    public ?string $description = null,
    // Only include if condition is met
) {}

// Usage
$data = ArticleData::from([
    'title' => 'Article',
    'description' => 'Optional description',
]);
```

## References

- [Spatie Laravel Data Documentation](https://spatie.be/docs/laravel-data)
- [Project DTOs Directory](../../app/DTOs)
