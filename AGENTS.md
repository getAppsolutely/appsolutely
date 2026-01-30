# AI Agent Instructions

This file is the **entry point for AI agents** (Cursor, Copilot, CLI tools, etc.) working on this codebase. Read it first, then follow the referenced docs and rules.

---

## Before You Code

1. **Read the rules**
    - Cursor: `.cursor/rules/` (especially `project.mdc`, `senior.mdc`, `laravel.mdc`, `front-end.mdc`).
    - Task-specific rules live in `.cursor/rules/project-rules/`.

2. **Understand the architecture**
    - `docs/README.architecture.md` — high-level structure and where things live.
    - `docs/adr/` — architecture decisions (repository pattern, services, controllers, etc.).
    - `docs/development-workflow.md` — commits, hooks, CI.

3. **Product context (if present)**
    - If `PRD.md` exists in the root, skim it to understand goals and scope before changing features.

---

## Development discipline

- **Strict TDD (Test-Driven Development)**  
  Write tests first; implement only what is needed to make them pass. Once implementation is complete, **run the test suite** (e.g. `./vendor/bin/phpunit`, `npm run test` — full suite: `npm run test`; see `docs/development-workflow.md` for all scripts).

- **Think three times, then solve**  
  Before proposing or implementing a solution:
    1. **Search current architecture** — Check `docs/README.architecture.md`, `docs/adr/`, and project rules for conventions and constraints.
    2. **Search the codebase** — Find existing patterns, similar features, and reusable logic; extend or reuse instead of inventing new approaches.
    3. **Propose a solid solution** — Only then implement: simple, maintainable, and elegant code that fits the project.

- **Code goals**  
  Aim for code that is **simple** (easy to read and reason about), **maintainable** (clear structure, minimal duplication), and **elegant** (fits existing patterns and avoids unnecessary complexity).

- **Follow best practices**  
  Always adhere to best practices for the stack in use: framework conventions, language idioms, security, accessibility, and project rules (`.cursor/rules/`, `docs/`). Do not cut corners for convenience.

- **Consider performance**  
  When coding a solution, take **performance** into account: efficient queries (avoid N+1), appropriate use of caching, minimal payloads, and front-end load where relevant. Prefer readable, maintainable code that is also performant.

- **Consider UX**  
  Take **user experience** into account when implementing: e.g. **lazy loading** (images, below-the-fold content, heavy components), loading states and feedback, perceived performance, and accessibility. Design for smooth, responsive interactions where it matters.

- **Verify before you code**  
  When using APIs, libraries, or framework features:
    1. **Check the internet** — Look up official docs or current references (e.g. Laravel, PHP, JS/TS, packages) to confirm correct usage, parameters, and return types.
    2. **Check the codebase** — Search for existing functions, helpers, or methods that already do what you need; confirm their **signature** (name, parameters, return type) and use them instead of reimplementing or guessing.

- **Large features: small steps, small commits**  
  For a big feature, work **step by step** instead of changing or adding many files at once:
    1. **Break the feature into small tasks** — Each task should be one logical change (e.g. add migration, add repository, add service, wire controller, add tests).
    2. **Keep each step small** — Prefer **no more than ~10 files** updated or added per step/commit (except when a single command generates more, e.g. `artisan make:`).
    3. **Minimal, focused commits** — One clear purpose per commit; use **Conventional Commits** (e.g. `feat(scope): subject`). See `docs/development-workflow.md` for format and types. Split large features into multiple commits or PRs rather than one huge change.

- **Wrong fix: revert first, then redo**  
  If the user says the fix is not correct: **strictly revert your update** (undo the changes completely). Then **redo** with the correct fix. Do not layer more changes on top of the wrong fix — that makes the codebase messy and hard to review. We always want to keep the **right** fix, not a pile of patches.

- **If you spot issues in the current build: ask**  
  When you identify problems in existing code — e.g. **performance** issues, **pattern misalignment** with architecture or conventions, or code that **isn’t best practice** — **ask the user** before changing it. Do not silently refactor or “fix” out of scope; confirm whether they want you to address it and how.

---

## Documentation

- **All docs live in `docs/`**  
  Project documentation belongs in the `docs/` folder (the only exception is the conversation with the user — that is not stored in `docs/`). Do not create or move project docs elsewhere.

- **Keep docs up-to-date when work is finished**  
  When implementation, build, or fixes are **really complete**, update any related docs under `docs/` (e.g. `README.architecture.md`, ADRs in `docs/adr/`, feature guides like `block-system.md`, `theme-development-guide.md`, `typescript-conventions.md`, `development-workflow.md`) so that documentation stays accurate and in sync with the codebase.

---

## Non-Negotiables

- **Do not use `php artisan make:model`, `make:controller`, `make:migration`,` etc.**  
  Generated code does not match our architecture. Copy and adapt from existing Migrations, Models, Repositories, Services, Controllers.

- **Repository pattern for DB** — use Repositories, not Models, in controllers/services.

- **Service classes for business logic** — keep controllers thin.

- **Do not guess.** If something is unclear, ask the user before implementing.

- **Follow existing patterns** — same naming, structure, and style as the rest of the codebase.

- **DRY (Don't Repeat Yourself)** — If similar code blocks appear, extract a function/method/helper and reuse it. Search the codebase before adding new logic.

- **Enums for fixed-value columns** — When adding or changing table columns that represent a fixed set of values (e.g. status, type), consider using a PHP enum so schema and code stay in sync.

---

## Key Paths

| Purpose                      | Location                                                                                                                                                                                                                                                                                              |
| ---------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Laravel app (in Docker)      | `/var/www/appsolutely/site` — run artisan/composer inside **laradock-workspace** container                                                                                                                                                                                                            |
| Project rules                | `.cursor/rules/`                                                                                                                                                                                                                                                                                      |
| Architecture summary         | `docs/README.architecture.md`                                                                                                                                                                                                                                                                         |
| ADRs                         | `docs/adr/`                                                                                                                                                                                                                                                                                           |
| Observers                    | `app/Observers/` — side effects only; register in `AppServiceProvider::boot()` with `Model::observe()`                                                                                                                                                                                                |
| DTOs (Spatie Data)           | `app/DTOs/` — see `app/DTOs/README.md`                                                                                                                                                                                                                                                                |
| Enums                        | `app/Enums/` — backed enums, `label()`, `toArray()`                                                                                                                                                                                                                                                   |
| Exceptions                   | `app/Exceptions/` — extend BaseException / BaseBusinessException / BaseNotFoundException                                                                                                                                                                                                              |
| API responses                | `app/Traits/ApiResponseTrait` on `BaseApiController`; `success()`, `error()`, `failValidation()`, `failAuth()`, `failForbidden()`, `failServer()`, `flattenJson()`, `throwBusinessError()`; JSON: `status`, `code`, `message`, `data`/`errors`                                                        |
| Service binding              | `AppServiceProvider` — bind interfaces in `app/Services/Contracts/` to implementations in `app/Services/`                                                                                                                                                                                             |
| Internal services            | Services without interface (e.g. `FormFieldFormatterService`, `NotificationSenderService`, `SitemapBuilderService`) — used only inside app; no contract in `Contracts/`                                                                                                                               |
| Events / Listeners / Jobs    | `app/Events/`, `app/Listeners/`, `app/Jobs/` — observers dispatch events; listeners may `ShouldQueue`                                                                                                                                                                                                 |
| Actions (auth/team)          | `app/Actions/` — Fortify/Jetstream                                                                                                                                                                                                                                                                    |
| Livewire                     | `app/Livewire/`                                                                                                                                                                                                                                                                                       |
| Console commands             | `app/Console/Commands/`                                                                                                                                                                                                                                                                               |
| Helpers / i18n               | `app/Helpers/helpers.php`, `app/Helpers/`; `lang/` — use `__t()`, `__tv()`; update all language files when adding strings                                                                                                                                                                             |
| Blade directives             | Registered in `AppServiceProvider::boot()`: `@t`, `@tv`, `@renderBlock`, `@title`, `@keywords`, `@description`; helpers: `page_meta($page, $key)`                                                                                                                                                     |
| View Components              | `app/View/Components/` — e.g. AppLayout, GuestLayout                                                                                                                                                                                                                                                  |
| View namespaces              | `page-builder` → `resource_path('page-builder')`; registered in `AppServiceProvider::boot()`                                                                                                                                                                                                          |
| Form Requests                | `app/Http/Requests/` — validate user input via Form Requests                                                                                                                                                                                                                                          |
| Middleware                   | `app/Http/Middleware/` — register in `bootstrap/app.php` (no `app/Http/Kernel.php` in Laravel 12)                                                                                                                                                                                                     |
| Rate limiters                | `AppServiceProvider::configureRateLimiting()` — `api`, `api:authenticated`, `form-submission`, `admin-api`, `password-reset`, `email-verification`, `web`                                                                                                                                             |
| Routes                       | `routes/web.php` (requires `web/*.php`, `auth.php`); `routes/api.php` (requires `api/*.php`); `routes/breadcrumbs.php`; `routes/fallback.php` loaded last; optional `routes/cache/*.php`                                                                                                              |
| Route macro                  | `Route::macro('localized', ...)` in AppServiceProvider for optional Laravel Localization                                                                                                                                                                                                              |
| Breadcrumbs                  | `routes/breadcrumbs.php` (Diglactic); config in `config/breadcrumbs.php`                                                                                                                                                                                                                              |
| Translation                  | `TranslationRepository`, `TranslationService` — singletons in AppServiceProvider; drivers in `app/Services/Translation/` (TranslatorInterface, DeepSeekTranslator, OpenAITranslator)                                                                                                                  |
| Admin (Dcat)                 | `app/Admin/` — Controllers, Actions/Grid, Extensions/Grid, Forms (Models/_, Fields/_, NestedSetForm, ProductSkuForm), Metrics/, bootstrap, routes                                                                                                                                                     |
| App config / Constants       | `app/Config/` (e.g. BasicConfig, MailConfig), `app/Constants/` (e.g. BasicConstant)                                                                                                                                                                                                                   |
| Facades / Policies / Logging | `app/Facades/`, `app/Policies/`, `app/Logging/` — authorization via Policies (e.g. `TeamPolicy`) and `Gate::forUser($user)->authorize(...)` in Fortify/Jetstream actions                                                                                                                              |
| Providers                    | `AppServiceProvider`, `EventServiceProvider`, `ThemeServiceProvider`, `TranslationServiceProvider`, `FortifyServiceProvider`, `JetstreamServiceProvider`                                                                                                                                              |
| Factories / Seeders          | `database/factories/`, `database/seeders/` — copy from existing; no artisan make                                                                                                                                                                                                                      |
| Key packages                 | Prettus (repos), Spatie Data (DTOs), kalnoy/nestedset, Dcat Admin, qirolab/laravel-themer, spatie/responsecache, Diglactic Breadcrumbs, Mcamara Laravel Localization                                                                                                                                  |
| Block system                 | `app/Services/BlockRendererService` (safe block render); base Livewire block `app/Livewire/GeneralBlock`; theme `manifest.json` (templates: label, component, view, displayOptions, queryOptions, styles); `config('appsolutely.blocks')` maps block class → repositories; see `docs/block-system.md` |
| Testing layout               | `tests/Feature/` (HTTP/feature), `tests/Integration/` (workflows), `tests/Unit/` by layer (Listeners/, Models/, Repositories/, Services/); frontend: `tests/frontend/` (Vitest: components/, services/)                                                                                               |
| Controller base classes      | Page: `BaseController`; API: `BaseApiController`; Admin page: `AdminBaseController`; Admin API: `AdminBaseApiController`. See `.cursor/rules/project.mdc` → Controller Inheritance Rules.                                                                                                             |

**Special folders:** Do not modify `Tabler/`. `Applise/` is an internal Laravel package. Repositories extend Prettus `BaseRepository`; reuse `app/Repositories/Traits/` and `app/Models/Traits/`.

**Model traits** (full set in `app/Models/Traits/`): ScopeStatus, ScopeReference, Sluggable, ClearsResponseCache, HasFilesOfType, HasMarkdownContent, HasMissingIds, HasMonetaryFields, LocalizesDateTime, ScopePublished, UnsetsUnderscoreAttributes — use when model has matching behaviour. **Reference vs slug:** `reference` = unique human-readable identifier (admin/API), auto-generated via ScopeReference (title/name/summary → slug, uniquified); `slug` = URL path (Sluggable trait, optional parent scope). **Nested set:** Models extend `app/Models/NestedSetModel` (uses NodeTrait); Menu, ArticleCategory, ProductCategory; repository trait `ActiveTreeList` (`getActiveList()`, `getTree()`), uses `NestedSetModel::formatTreeArray()`.

---

## Additional Patterns

- **Controller dependency injection** — Prefer **constructor injection** with **readonly** dependencies (e.g. `PageController` and `GeneralPageServiceInterface`). Controllers are final; use method or constructor injection for services.
- **Response cache** — Custom **CacheProfile** (`app/Http/Middleware/CacheProfile.php`) extends Spatie’s; excludes Livewire (`X-Livewire`), admin domain/prefix, authenticated users. Use **`doNotCacheResponse`** middleware on routes that must never be cached. Models use **ClearsResponseCache** to clear on change.
- **Route restriction (feature toggles)** — **RestrictRoutePrefixes** middleware + **RouteRestrictionService**; `config('appsolutely.features.disabled')` (comma-separated) lists URL prefixes that return 404.
- **404 handling** — In `bootstrap/app.php`: JSON requests get JSON 404; web requests use theme resolution and themed `errors.404` when available.
- **No custom Validation Rule classes** — No `app/Rules/`; validation is in Form Requests or inline (e.g. `Rule::unique(...)`).
- **No API Resource classes** — APIs return arrays/DTOs via `ApiResponseTrait`; no `JsonResource`/`ApiResource`.
- **Middleware aliases** — In `bootstrap/app.php`: `localize`, `localizationRedirect`, `localeSessionRedirect`, `localeCookieRedirect`, `localeViewPath`, `theme`, `doNotCacheResponse`, `throttle.form`.
- **Admin file upload route** — Admin files upload route is rebound in `AppServiceProvider::boot()` by matching URI to `Admin\Controllers\Api\FileController@upload`.

---

## Task Layering

When implementing a feature, split work by layer and follow existing examples:

- **Infra / config** — env, Docker, routes, middleware.
- **Data** — migrations, models, repositories.
- **Logic** — services, DTOs.
- **API / HTTP** — controllers, form requests, API responses (via ApiResponseTrait; no JsonResource).
- **UI** — views, frontend (TypeScript, themes).

One layer at a time, mirror current patterns.

---

## Lint & Format

Generated or edited code must pass:

- **PHP:** Pint (`pint.json`).
- **TS/JS:** ESLint + Prettier (`eslint.config.js`, `.prettierrc.json`).
- **SCSS:** Stylelint + Prettier.

**Hooks:** Pre-commit runs lint-staged (Pint, Prettier, ESLint, Stylelint on staged files). Pre-push runs type-check and full lint. See `docs/development-workflow.md` for details and troubleshooting.

---

## Summary

- Use **AGENTS.md** (this file) as the entry point.
- Use **docs/README.architecture.md** for structure and pointers.
- Use **.cursor/rules/** for conventions and patterns.
- Use **docs/adr/** for architectural decisions.
- When in doubt, **ask;** do not invent new patterns.
