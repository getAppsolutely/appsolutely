# ADR 004: Final Readonly Service Classes

**Status:** Accepted  
**Date:** 2025  
**Deciders:** Architecture Team

## Context

We want to ensure service classes are:

- Immutable after construction (no property mutations)
- Not extended (prevent inheritance issues)
- Clear in their intent (services are stateless operations)
- Following modern PHP 8.2+ features

## Decision

All service classes will be declared as `final readonly class` to enforce immutability and prevent inheritance.

### Implementation Details

1. **Final Keyword**: Services cannot be extended
2. **Readonly Keyword**: Class properties cannot be modified after construction (PHP 8.2+)
3. **Constructor Injection**: All dependencies injected via constructor
4. **No Property Mutations**: Services are stateless operation classes
5. **Protected Properties**: Dependencies stored as `protected readonly` properties

### Example

```php
<?php

declare(strict_types=1);

namespace App\Services;

final readonly class PageService implements PageServiceInterface
{
    public function __construct(
        protected PageRepository $pageRepository,
        protected CacheInterface $cache,
        protected ConnectionInterface $db
    ) {}

    public function findPublishedPage(string $slug): ?Page
    {
        // Properties cannot be modified here
        return $this->pageRepository->findPageBySlug($slug, now());
    }
}
```

## Consequences

### Positive

- ✅ **Immutability**: Services cannot be accidentally modified
- ✅ **Prevents Inheritance**: No unexpected behavior from child classes
- ✅ **Clear Intent**: Makes it obvious services are stateless
- ✅ **Modern PHP**: Uses PHP 8.2+ readonly classes feature
- ✅ **Thread Safety**: Immutable objects are safer in concurrent contexts

### Negative

- ⚠️ **PHP Version Requirement**: Requires PHP 8.2+
- ⚠️ **No Flexibility**: Cannot extend services if needed
- ⚠️ **Learning Curve**: Team must understand readonly semantics

### Mitigations

- Document that services should be composed, not extended
- Use interfaces for polymorphism instead of inheritance
- Provide examples of service composition

## References

- [PHP 8.2 Readonly Classes](https://www.php.net/releases/8.2/en.php#readonly_classes)
- Example: `app/Services/PageService.php`
