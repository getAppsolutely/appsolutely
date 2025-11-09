# ADR 003: Strict Type Declarations

**Status:** Accepted  
**Date:** 2025  
**Deciders:** Architecture Team

## Context

PHP's type system has evolved significantly, and we want to:

- Catch type-related errors at development time, not runtime
- Improve code clarity and self-documentation
- Enable better IDE support and static analysis
- Follow modern PHP best practices (PHP 8.1+)

## Decision

We will use **strict type declarations** (`declare(strict_types=1);`) in all PHP files and require comprehensive type hints throughout the codebase.

### Implementation Details

1. **Strict Types**: Every PHP file must start with `declare(strict_types=1);`
2. **Type Hints**: All method parameters and return types must be explicitly declared
3. **Property Types**: All class properties must have type declarations
4. **Array Types**: Use specific array types where possible (e.g., `array<string, mixed>`, `array<int, string>`)
5. **Union Types**: Use union types (PHP 8.0+) when appropriate
6. **Nullable Types**: Use `?Type` syntax for nullable parameters/returns

### Example

```php
<?php

declare(strict_types=1);

namespace App\Services;

final readonly class PageService implements PageServiceInterface
{
    public function __construct(
        protected PageRepository $pageRepository,
        protected CacheInterface $cache
    ) {}

    public function findPublishedPage(string $slug): ?Page
    {
        return $this->pageRepository->findPageBySlug($slug, now());
    }

    public function updatePage(int $id, array<string, mixed> $data): Page
    {
        // Implementation
    }

    public function getPages(array<int, string> $ids): array
    {
        // Implementation
    }
}
```

## Consequences

### Positive

- ✅ **Early Error Detection**: Type errors caught during development
- ✅ **Better IDE Support**: Autocomplete and refactoring work better
- ✅ **Self-Documenting**: Types clarify what methods expect/return
- ✅ **Static Analysis**: Tools like PHPStan work more effectively
- ✅ **Refactoring Safety**: Type system helps catch breaking changes
- ✅ **Modern PHP**: Aligns with PHP 8.1+ best practices

### Negative

- ⚠️ **Stricter Requirements**: Must be more explicit about types
- ⚠️ **Migration Effort**: Existing code needs type hints added
- ⚠️ **Learning Curve**: Team must understand type system nuances

### Mitigations

- Use PHPStan/Psalm for static analysis
- Provide examples in documentation
- Code review to ensure type hints are added

## References

- [PHP Type Declarations](https://www.php.net/manual/en/language.types.declarations.php)
- [PHP 8.1 Features](https://www.php.net/releases/8.1/en.php)
