# ADR 002: Service Layer with Interface-Based Design

**Status:** Accepted  
**Date:** 2025  
**Deciders:** Architecture Team

## Context

We need to organize business logic in a way that:

- Keeps controllers thin and focused on HTTP concerns
- Makes business logic testable and mockable
- Allows for easy swapping of implementations
- Maintains clear separation between layers
- Follows SOLID principles, especially Dependency Inversion

## Decision

We will implement a **Service Layer** where all business logic resides in service classes, and all services implement interfaces. Controllers delegate to services, never contain business logic.

### Implementation Details

1. **Service Interfaces**: All services have corresponding interfaces in `app/Services/Contracts/`
2. **Interface Binding**: Services are bound to interfaces in `AppServiceProvider`
3. **Dependency Injection**: Services are injected via constructor injection
4. **Final Readonly Classes**: Services are `final readonly class` for immutability
5. **Single Responsibility**: Each service handles one domain area
6. **No Business Logic in Controllers**: Controllers only handle HTTP request/response

### Example Structure

```php
// Interface
interface PageServiceInterface
{
    public function findPublishedPage(string $slug): ?Page;
}

// Service Implementation
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

// Controller (thin)
final class PageController extends BaseController
{
    public function __construct(
        private readonly GeneralPageService $generalPageService
    ) {}

    public function show(Request $request, ?string $slug = null): View
    {
        $page = $this->generalPageService->resolvePageWithCaching($slug);
        return view('page.show', compact('page'));
    }
}

// Service Registration
$this->app->bind(PageServiceInterface::class, PageService::class);
```

## Consequences

### Positive

- ✅ **Testability**: Easy to mock services via interfaces
- ✅ **Flexibility**: Can swap implementations without changing controllers
- ✅ **Maintainability**: Business logic centralized and organized
- ✅ **Reusability**: Services can be used across controllers, jobs, commands
- ✅ **Type Safety**: Interfaces provide clear contracts
- ✅ **SOLID Compliance**: Follows Dependency Inversion Principle

### Negative

- ⚠️ **More Files**: Interface + implementation for each service
- ⚠️ **Additional Abstraction**: More layers to navigate
- ⚠️ **Registration Overhead**: Must register all services in service provider

### Mitigations

- Organize interfaces alphabetically in service provider
- Use clear naming conventions (ServiceInterface suffix)
- Document service responsibilities

## References

- Service Interfaces: `app/Services/Contracts/`
- Service Implementations: `app/Services/`
- Service Registration: `app/Providers/AppServiceProvider.php`
