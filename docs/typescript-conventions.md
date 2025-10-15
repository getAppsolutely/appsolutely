# TypeScript Conventions & Best Practices

**Version**: 1.0  
**Last Updated**: 2025-10-14

This document outlines the TypeScript conventions and best practices used in this Laravel 12 project.

---

## 📋 Table of Contents

1. [Project Structure](#project-structure)
2. [Configuration](#configuration)
3. [Type Definitions](#type-definitions)
4. [Naming Conventions](#naming-conventions)
5. [Import/Export Patterns](#importexport-patterns)
6. [Common Patterns](#common-patterns)
7. [Build & Development](#build--development)
8. [Testing](#testing)

---

## 🗂️ Project Structure

### TypeScript Files Organization

```
├── types/                          # Global type declarations
│   └── global.d.ts                # Window extensions, global types
├── resources/
│   └── page-builder/
│       └── assets/
│           ├── ts/                # Page builder TypeScript
│           │   ├── app.ts
│           │   ├── components/
│           │   └── services/
│           └── types/             # Page builder specific types
│               └── index.ts
├── themes/
│   ├── default/
│   │   ├── js/                    # Theme TypeScript files
│   │   │   ├── app.ts
│   │   │   └── bootstrap.ts
│   │   └── vite.config.ts
│   └── june/
│       ├── js/
│       │   ├── app.ts
│       │   ├── bootstrap.ts
│       │   ├── assets.ts
│       │   ├── types/
│       │   │   └── index.ts
│       │   └── components/        # Theme components
│       │       ├── header.ts
│       │       ├── features.ts
│       │       └── ...
│       └── vite.config.ts
├── tsconfig.json                  # Base TypeScript config
├── tsconfig.themes.json           # Themes-specific config
├── tsconfig.pagebuilder.json      # Page builder config
└── vite.config.ts                 # Main Vite config
```

---

## ⚙️ Configuration

### TypeScript Compiler Options

We use strict TypeScript settings for maximum type safety:

```json
{
  "compilerOptions": {
    "target": "ES2020",
    "module": "ESNext",
    "lib": ["ES2020", "DOM", "DOM.Iterable"],
    "strict": true,
    "noUnusedLocals": true,
    "noUnusedParameters": true,
    "noImplicitReturns": true,
    "skipLibCheck": true
  }
}
```

### Multiple TypeScript Configs

- **`tsconfig.json`**: Base configuration, shared settings
- **`tsconfig.themes.json`**: Extends base, includes theme TypeScript files
- **`tsconfig.pagebuilder.json`**: Extends base, includes page builder files

---

## 🏷️ Type Definitions

### Global Type Declarations

Global types are defined in `types/global.d.ts`:

```typescript
import type { AxiosStatic } from 'axios';
import type * as BootstrapNamespace from 'bootstrap';
import type { LoDashStatic } from 'lodash';

declare global {
    interface Window {
        axios: AxiosStatic;
        bootstrap: typeof BootstrapNamespace;
        _: LoDashStatic;
        // Add other window extensions here
    }
}

export {};
```

**Key Points**:
- Always use `import type` for type-only imports
- Export empty object `export {}` to make it a module
- Extend `Window` interface for global variables

### Theme-Specific Types

Each theme can have its own types in `themes/[theme]/js/types/index.ts`:

```typescript
/**
 * June Theme Type Definitions
 */

export interface HeaderInstance {
    header: HTMLElement | null;
    navbar: HTMLElement | null;
    init(): void;
    bindEvents(): void;
}

export interface Photo {
    id: string;
    url: string;
    alt: string;
}
```

---

## 🎯 Naming Conventions

### Files

- **TypeScript files**: Use `.ts` extension
- **Vue SFC with TypeScript**: Use `<script setup lang="ts">`
- **Configuration files**: Use `.config.ts` (e.g., `vite.config.ts`)

### Variables & Functions

```typescript
// ✅ Good: camelCase for variables and functions
const userName = 'John';
function getUserData() { }

// ✅ Good: PascalCase for classes and interfaces
class UserService { }
interface UserData { }

// ✅ Good: UPPER_SNAKE_CASE for constants
const MAX_RETRY_COUNT = 3;
const API_BASE_URL = '/api';
```

### Unused Parameters

Prefix unused parameters with `_` to satisfy TypeScript:

```typescript
// ✅ Good: Unused parameters prefixed with underscore
function createForm(container: HTMLElement, _config: any): void {
    // Only using container, not config
    container.innerHTML = '';
}
```

---

## 📦 Import/Export Patterns

### Type-Only Imports

Always use `import type` when importing only types:

```typescript
// ✅ Good: Type-only imports
import type { AxiosStatic } from 'axios';
import type { HeaderInstance } from './types';

// ❌ Bad: Regular import for types
import { HeaderInstance } from './types';
```

### Module Imports

```typescript
// ✅ Good: Import entire module
import axios from 'axios';
import * as bootstrap from 'bootstrap';
import _ from 'lodash';

// ✅ Good: Named imports
import { ref, computed } from 'vue';
```

### Re-exporting Types

```typescript
// types/index.ts
export type { HeaderInstance } from './header';
export type { Photo } from './photo';
export interface CommonConfig {
    // ...
}
```

---

## 🔧 Common Patterns

### DOM Manipulation with Type Safety

```typescript
// ✅ Good: Type-safe DOM queries
const button = document.querySelector<HTMLButtonElement>('.btn-submit');
if (button) {
    button.disabled = true;
}

const meta = document.head.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
if (meta) {
    const token = meta.content;
}

// ✅ Good: NodeList with proper typing
const items = document.querySelectorAll<HTMLElement>('.item');
items.forEach((item: HTMLElement) => {
    item.classList.add('active');
});
```

### Working with Third-Party Libraries

#### Axios Configuration

```typescript
import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

const token = document.head.querySelector<HTMLMetaElement>('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}
```

#### Bootstrap 5 Components

```typescript
import * as bootstrap from 'bootstrap';

window.bootstrap = bootstrap;

// Initialize tooltips
const tooltipTriggerList = Array.from(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
);
tooltipTriggerList.map((tooltipTriggerEl: Element) => {
    return new bootstrap.Tooltip(tooltipTriggerEl as HTMLElement);
});
```

#### Lodash

```typescript
import _ from 'lodash';

// Type assertion for window assignment
window._ = _ as any;

// Usage
const debounced = _.debounce(() => {
    console.log('Debounced!');
}, 300);
```

### Async/Await Patterns

```typescript
// ✅ Good: Proper async/await with error handling
async function loadData(): Promise<void> {
    try {
        const response = await fetch('/api/data');
        const data = await response.json();
        renderData(data);
    } catch (error) {
        console.error('Failed to load data:', error);
    }
}

// ✅ Good: Returning typed promises
async function getData(): Promise<{ html: string; css: string }> {
    const response = await fetch('/api/content');
    return await response.json();
}
```

### Event Handlers

```typescript
// ✅ Good: Typed event handlers
button.addEventListener('click', (event: MouseEvent): void => {
    event.preventDefault();
    handleClick();
});

document.addEventListener('DOMContentLoaded', (): void => {
    initializeApp();
});
```

### Component Classes

```typescript
export class Header {
    private header: HTMLElement | null = null;
    private navbar: HTMLElement | null = null;

    constructor() {
        this.init();
    }

    private init(): void {
        this.header = document.querySelector<HTMLElement>('.header');
        this.bindEvents();
    }

    private bindEvents(): void {
        window.addEventListener('scroll', (): void => {
            this.checkScroll();
        });
    }

    private checkScroll(): void {
        if (!this.header) return;
        
        if (window.scrollY > 100) {
            this.header.classList.add('scrolled');
        } else {
            this.header.classList.remove('scrolled');
        }
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    new Header();
});
```

---

## 🛠️ Build & Development

### NPM Scripts

```bash
# Type checking
npm run type-check              # Check main app
npm run type-check:themes       # Check all themes
npm run type-check:pagebuilder  # Check page builder
npm run type-check:all          # Check everything

# Building
npm run build                   # Build main app
npm run build:themes [theme]    # Build specific theme
npm run build:page-builder      # Build page builder
npm run build:all               # Build everything

# Development
npm run dev                     # Start main dev server
npm run dev:page-builder        # Start page builder dev server
```

### Pre-commit Hooks

TypeScript type checking is enforced via Git pre-commit hook:

```bash
#!/bin/sh
# TypeScript type check on staged files
TS_FILES=$(git diff --cached --name-only --diff-filter=ACM | grep -E '\.(ts|tsx|vue)$')

if [ -n "$TS_FILES" ]; then
    echo "Running TypeScript type check..."
    npm run type-check:all
    if [ $? -ne 0 ]; then
        echo "❌ TypeScript errors found. Please fix before committing."
        exit 1
    fi
fi
```

---

## 🧪 Testing

### Type Checking

Always run type checks before committing:

```bash
# Check for type errors
npm run type-check:all

# Should output with no errors
✓ tsc --noEmit
✓ tsc --project tsconfig.themes.json --noEmit
✓ tsc --project tsconfig.pagebuilder.json --noEmit
```

### Build Testing

Verify builds succeed:

```bash
# Test individual builds
npm run build:themes default
npm run build:themes june
npm run build:page-builder

# Test full production build
npm run build:all
```

### Browser Testing

After building:
1. Load the application in browser
2. Open DevTools console
3. Check for TypeScript-related errors
4. Test all interactive features
5. Verify HMR works in dev mode

---

## 🚫 Common Pitfalls

### 1. Using `any` Type

```typescript
// ❌ Bad: Overusing any
function processData(data: any): any {
    return data.value;
}

// ✅ Good: Proper typing
interface DataObject {
    value: string;
}
function processData(data: DataObject): string {
    return data.value;
}

// ⚠️ Acceptable: When dealing with truly dynamic data
window.someThirdPartyLib = someLib as any;
```

### 2. Not Handling Null/Undefined

```typescript
// ❌ Bad: Assuming element exists
const button = document.querySelector('.btn');
button.addEventListener('click', () => {}); // Error if null

// ✅ Good: Null checking
const button = document.querySelector<HTMLButtonElement>('.btn');
if (button) {
    button.addEventListener('click', () => {});
}
```

### 3. Incorrect Type Assertions

```typescript
// ❌ Bad: Unnecessary casting
const value = "123";
const num = value as number; // Won't work!

// ✅ Good: Proper conversion
const num = parseInt(value, 10);
```

---

## 📚 Additional Resources

### Official Documentation
- [TypeScript Handbook](https://www.typescriptlang.org/docs/)
- [Vite TypeScript Guide](https://vitejs.dev/guide/features.html#typescript)
- [Laravel Vite Plugin](https://laravel.com/docs/11.x/vite)

### Type Definitions
- [@types/node](https://www.npmjs.com/package/@types/node)
- [@types/lodash](https://www.npmjs.com/package/@types/lodash)
- [@types/bootstrap](https://www.npmjs.com/package/@types/bootstrap)

### Project-Specific
- [TypeScript Migration Plan](./typescript-migration-plan.md)
- [Project Rules](.cursor/rules/)

---

## 🤝 Contributing

When adding new TypeScript code:

1. **Follow existing patterns** - Look at similar files for guidance
2. **Add proper types** - Avoid `any` when possible
3. **Run type checks** - Use `npm run type-check:all`
4. **Test thoroughly** - Ensure builds and functionality work
5. **Update documentation** - If you introduce new patterns

---

## 📝 Notes

- This project uses **TypeScript in strict mode**
- All window extensions must be declared in `types/global.d.ts`
- Pre-commit hooks will fail if type errors exist
- Each theme can have its own type definitions
- Always prefer explicit types over implicit `any`

---

**Questions or suggestions?** Update this document or reach out to the team.

