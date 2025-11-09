# ADR 007: Base Controller Hierarchy

**Status:** Accepted  
**Date:** 2025  
**Deciders:** Architecture Team

## Context

We have different types of controllers (web, API, admin) that need:

- Shared functionality (response formatting, authentication)
- Consistent structure across controller types
- Clear separation between different controller contexts
- Reusable base functionality

## Decision

We will implement a **Base Controller Hierarchy** with separate base classes for each controller type, providing shared functionality while maintaining clear boundaries.

### Implementation Details

1. **Base Controllers**: Each controller type has its own base class
2. **Inheritance Structure**:
    - `BaseController` → Web page controllers
    - `BaseApiController` → API controllers
    - `AdminBaseController` → Admin page controllers
    - `AdminBaseApiController` → Admin API controllers
3. **Final Classes**: All concrete controllers are `final` to prevent inheritance
4. **Shared Traits**: Common functionality in traits if needed
5. **Type-Specific Logic**: Each base class handles its context (JSON responses, views, etc.)

### Structure

```
app/Http/Controllers/
├── BaseController.php              # Web page controllers
├── Api/
│   └── BaseApiController.php       # API controllers
└── ...

app/Admin/Controllers/
├── AdminBaseController.php         # Admin page controllers
└── Api/
    └── AdminBaseApiController.php  # Admin API controllers
```

### Example

```php
// Base API Controller
abstract class BaseApiController extends Controller
{
    use ApiResponseTrait;

    // Shared API functionality
}

// Concrete API Controller
final class UserApiController extends BaseApiController
{
    public function __construct(
        private readonly UserService $userService
    ) {}

    public function index(): JsonResponse
    {
        $users = $this->userService->getAll();
        return $this->success($users);
    }
}
```

## Consequences

### Positive

- ✅ **Code Reuse**: Shared functionality in base classes
- ✅ **Consistency**: All controllers of same type follow same pattern
- ✅ **Maintainability**: Changes to base class affect all children
- ✅ **Clear Boundaries**: Different contexts clearly separated
- ✅ **Type Safety**: Base classes provide type-specific methods

### Negative

- ⚠️ **Inheritance Complexity**: Multiple base classes to understand
- ⚠️ **Tight Coupling**: Controllers coupled to base class implementation
- ⚠️ **Potential Overuse**: Risk of putting too much in base classes

### Mitigations

- Keep base classes focused and minimal
- Use composition (traits) for optional functionality
- Document base class responsibilities clearly

## References

- Base Controllers: `app/Http/Controllers/BaseController.php`, `app/Http/Controllers/Api/BaseApiController.php`
- Admin Base Controllers: `app/Admin/Controllers/AdminBaseController.php`, `app/Admin/Controllers/Api/AdminBaseApiController.php`
