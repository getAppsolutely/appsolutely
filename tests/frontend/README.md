# Frontend Testing

This directory contains frontend tests using Vitest.

## Running Tests

```bash
# Run all frontend tests
npm run test:frontend

# Run tests in watch mode
npm run test:frontend:watch

# Run tests with UI
npm run test:frontend:ui

# Run tests with coverage
npm run test:frontend:coverage
```

## Test Structure

- `setup.ts` - Test environment configuration and global mocks
- `components/` - Component tests
- `services/` - Service tests

## Writing Tests

Tests should follow the same structure as the codebase:

- Use TypeScript
- Follow existing naming conventions
- Use Vitest's testing utilities
- Mock external dependencies

## Path Aliases

The Vitest config includes path aliases for easier imports:

- `@` - Points to `resources/page-builder/assets`
- `@themes` - Points to `themes`
- `@resources` - Points to `resources`

## Example Test

```typescript
import { describe, it, expect, beforeEach } from 'vitest';
import { MyComponent } from '@themes/june/js/components/MyComponent';
import { MyService } from '@resources/page-builder/assets/ts/services/MyService';

describe('MyComponent', () => {
    beforeEach(() => {
        // Setup
    });

    it('should do something', () => {
        expect(true).toBe(true);
    });
});
```
