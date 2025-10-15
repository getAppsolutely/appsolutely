# Appsolutely

A Laravel 12 application with TypeScript-based frontend, featuring a modular theme system and page builder.

## Features

### CMS management

### Product management

### Order management

### Payment Gateway

### Api ready

### Well-organised structure

### TypeScript Frontend
- Fully migrated to TypeScript with strict type checking
- Multiple theme support (Default, June, Tabler)
- Custom page builder with TypeScript
- Pre-commit hooks for type safety

## Tech Stack

- **Backend**: Laravel 12, PHP 8.4
- **Frontend**: TypeScript, Vite
- **UI Libraries**: Bootstrap 5, Tailwind CSS
- **Build Tools**: Vite, TSC
- **Package Manager**: Composer (PHP), npm (JavaScript/TypeScript)

## Installation

General Laravel Installation
```bash
composer install

php artisan migrate && php artisan db:seed

php artisan db:seed --class=AdminCoreSeeder
```

Install hooks for local environment.
```
composer install-hooks
```

## Frontend Development

### TypeScript Setup

This project uses TypeScript for all frontend code. See [TypeScript Conventions](docs/typescript-conventions.md) for detailed guidelines.

### Build Commands

```bash
# Development
npm run dev                      # Start main dev server
npm run dev:page-builder         # Start page builder dev server

# Type Checking
npm run type-check               # Check main app
npm run type-check:themes        # Check themes
npm run type-check:pagebuilder   # Check page builder
npm run type-check:all           # Check everything

# Building
npm run build                    # Build main app
npm run build:themes [theme]     # Build specific theme (default, june)
npm run build:page-builder       # Build page builder
npm run build:all                # Build everything
```

### Theme Development

Each theme is a self-contained TypeScript project:

```
themes/
├── default/               # Minimal default theme
│   ├── js/
│   │   ├── app.ts        # Entry point
│   │   └── bootstrap.ts  # Axios, CSRF setup
│   └── vite.config.ts
├── june/                  # Full-featured theme
│   ├── js/
│   │   ├── app.ts
│   │   ├── bootstrap.ts
│   │   ├── assets.ts
│   │   ├── types/
│   │   └── components/   # 8 component files
│   └── vite.config.ts
└── tabler/                # Template-only theme (no JS)
```

### Pre-commit Hooks

TypeScript type checking runs automatically on commit. If type errors are found, the commit will be blocked.

## Documentation

- [TypeScript Conventions](docs/typescript-conventions.md) - TypeScript best practices and patterns
- [TypeScript Migration Plan](docs/typescript-migration-plan.md) - Complete migration history
- [Block System](docs/block-system.md) - Page builder block system
- [Page Block Schema](docs/page-block-schema.md) - Block schema documentation

## Todo
- Order management
- Front-end: public site (homepage, articles, products and categories, checkout) and member center
- Payment management
- Batch deletion for product SKUs
- Create articles and products/product skus before inserting images
- Guest checkout
