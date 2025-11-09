# ADR 005: Dependency Injection via Constructor

**Status:** Accepted  
**Date:** 2025  
**Deciders:** Architecture Team

## Context

We need a consistent way to manage dependencies that:

- Makes code testable (easy to inject mocks)
- Reduces coupling between classes
- Follows SOLID principles (Dependency Inversion)
- Avoids service locator anti-pattern
- Works seamlessly with Laravel's IoC container

## Decision

We will use **Constructor Injection** for all dependencies. All classes receive their dependencies through constructor parameters, never through service locator pattern (`app()`, `resolve()`, etc.).

### Implementation Details

1. **Constructor Injection**: All dependencies passed via constructor
2. **Type Hints**: Dependencies must be type-hinted (prefer interfaces)
3. **Service Provider Registration**: Services bound to interfaces in `AppServiceProvider`
4. **No Service Locator**: Never use `app()`, `resolve()`, or facades for dependencies
5. **Alphabetical Organization**: Service bindings organized alphabetically in service provider

### Example

```php
// ✅ Good: Constructor injection
final readonly class PageService implements PageServiceInterface
{
    public function __construct(
        protected PageRepository $pageRepository,
        protected CacheInterface $cache
    ) {}
}

// ❌ Bad: Service locator
final readonly class PageService implements PageServiceInterface
{
    public function findPage(string $slug): ?Page
    {
        $repo = app(PageRepository::class); // ❌ Don't do this
        return $repo->findBySlug($slug);
    }
}

// Service Registration
$this->app->bind(PageServiceInterface::class, PageService::class);
```

## Consequences

### Positive

- ✅ **Testability**: Easy to inject mocks in tests
- ✅ **Explicit Dependencies**: Clear what a class needs
- ✅ **SOLID Compliance**: Follows Dependency Inversion Principle
- ✅ **IDE Support**: Better autocomplete and refactoring
- ✅ **Type Safety**: Type hints catch errors early

### Negative

- ⚠️ **Constructor Size**: Classes with many dependencies have large constructors
- ⚠️ **No Lazy Loading**: All dependencies instantiated even if unused
- ⚠️ **Registration Overhead**: Must register all services

### Mitigations

- Use service composition to reduce constructor size
- Consider splitting large services into smaller ones
- Document service dependencies clearly

## References

- [Laravel Service Container](https://laravel.com/docs/container)
- Service Registration: `app/Providers/AppServiceProvider.php`
