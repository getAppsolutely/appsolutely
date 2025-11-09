# ADR 006: Model Observers for Side Effects

**Status:** Accepted  
**Date:** 2025  
**Deciders:** Architecture Team

## Context

We need to handle model lifecycle events (create, update, delete) that trigger side effects like:

- Cache invalidation
- Sitemap regeneration
- Search index updates
- Event dispatching

We want to keep models clean and separate these concerns from the model itself.

## Decision

We will use **Model Observers** to handle all side effects triggered by model lifecycle events. Business logic stays in services, but observers handle cross-cutting concerns like caching and indexing.

### Implementation Details

1. **Observer Classes**: Create observer classes in `app/Observers/`
2. **Register in Service Provider**: Observers registered in `AppServiceProvider::boot()`
3. **Side Effects Only**: Observers handle side effects, not business logic
4. **No Business Logic in Models**: Models remain data containers
5. **Cache Management**: Observers handle cache clearing on model changes

### Example

```php
// Observer
final class PageObserver
{
    public function saved(Page $page): void
    {
        // Clear sitemap cache
        Cache::forget('sitemap');

        // Clear page cache
        Cache::tags(['pages'])->flush();
    }

    public function deleted(Page $page): void
    {
        Cache::forget('sitemap');
        Cache::tags(['pages'])->flush();
    }
}

// Registration
Page::observe(PageObserver::class);

// Model stays clean
class Page extends Model
{
    // No boot() method with side effects
    // Business logic in PageService
}
```

## Consequences

### Positive

- ✅ **Separation of Concerns**: Models stay focused on data
- ✅ **Testability**: Observers can be tested independently
- ✅ **Reusability**: Same observer logic applies to multiple models
- ✅ **Maintainability**: Side effects centralized and easy to find
- ✅ **Laravel Native**: Uses Laravel's built-in observer pattern

### Negative

- ⚠️ **Additional Classes**: More files to maintain
- ⚠️ **Registration Required**: Must remember to register observers
- ⚠️ **Hidden Behavior**: Side effects not immediately visible in model code

### Mitigations

- Document observers in model classes with comments
- Keep observer registration centralized in service provider
- Use clear naming conventions (ModelNameObserver)

## References

- [Laravel Observers](https://laravel.com/docs/eloquent#observers)
- Example Observers: `app/Observers/PageObserver.php`, `app/Observers/ProductObserver.php`
