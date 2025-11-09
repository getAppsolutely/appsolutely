# ADR 001: Repository Pattern for Data Access

**Status:** Accepted  
**Date:** 2025  
**Deciders:** Architecture Team

## Context

We need a consistent way to access data from the database that:

- Separates data access logic from business logic
- Makes the codebase testable (easy to mock data access)
- Provides a single point of change for database queries
- Follows Laravel best practices while maintaining flexibility

## Decision

We will use the **Repository Pattern** for all data access operations. All database queries must go through repository classes, never directly through Eloquent models in services or controllers.

### Implementation Details

1. **Base Repository**: All repositories extend `App\Repositories\BaseRepository`
2. **Model Method**: Each repository must implement `model()` method returning the model class name
3. **Domain Methods**: Repositories expose domain-specific methods (e.g., `findPageBySlug()`, `findByReference()`)
4. **Traits**: Reusable query patterns are implemented as traits (e.g., `Reference`, `Status`)
5. **No Direct Model Access**: Services and controllers must use repositories, never `Model::query()` or `Model::find()`

### Example Structure

```php
// Repository
final class PageRepository extends BaseRepository
{
    use Reference;
    use Status;

    public function model(): string
    {
        return Page::class;
    }

    public function findPageBySlug(string $slug, Carbon $datetime): ?Page
    {
        return $this->model->newQuery()
            ->slug($slug)
            ->status()
            ->published($datetime)
            ->first();
    }
}

// Service uses repository
final readonly class PageService implements PageServiceInterface
{
    public function __construct(
        protected PageRepository $pageRepository
    ) {}

    public function findPublishedPage(string $slug): ?Page
    {
        return $this->pageRepository->findPageBySlug($slug, now());
    }
}
```

## Consequences

### Positive

- ✅ **Testability**: Easy to mock repositories in unit tests
- ✅ **Maintainability**: Database changes isolated to repositories
- ✅ **Consistency**: All data access follows the same pattern
- ✅ **Flexibility**: Can swap implementations without changing business logic
- ✅ **Query Reusability**: Common queries encapsulated in repository methods

### Negative

- ⚠️ **Additional Layer**: More files and classes to maintain
- ⚠️ **Learning Curve**: New developers must understand the pattern
- ⚠️ **Boilerplate**: Each repository requires a class file

### Mitigations

- Provide clear examples in documentation
- Use traits to reduce boilerplate for common patterns
- Enforce pattern through code reviews

## References

- [Laravel Repository Pattern](https://laravel.com/docs/eloquent)
- Base Repository: `app/Repositories/BaseRepository.php`
- Example Repository: `app/Repositories/PageRepository.php`
