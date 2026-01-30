# AI Code Review Checklist

Use this checklist when reviewing code (diffs, PRs, or patches). It reflects project rules in `AGENTS.md`, `.cursor/rules/`, and `docs/README.architecture.md`. Tick or verify each item that applies to the changed code.

---

## Scope

- [ ] **Review only what changed** — Do not request or suggest refactors of unrelated code unless the user asks.
- [ ] **No silent renames of public APIs** — If the change renames something public, confirm it is intentional and documented.

---

## Architecture

- [ ] **Controllers are thin** — Business logic lives in Services or Actions; controllers only orchestrate and return responses.
- [ ] **Repository pattern** — DB access goes through Repositories; no direct Model usage in controllers or services for querying/updating.
- [ ] **Service layer** — Complex logic lives in Services; public services have an interface in `app/Services/Contracts/` and are bound in `AppServiceProvider`.
- [ ] **Request flow** — HTTP → Controller → Service → Repository; controllers do not call other controllers or touch models directly.
- [ ] **Dependency injection** — Controllers use constructor or method injection; prefer **readonly** dependencies where applicable.
- [ ] **Correct base classes** — Page controllers extend `BaseController`; API extend `BaseApiController`; Admin page extend `AdminBaseController`; Admin API extend `AdminBaseApiController`.
- [ ] **Blade / views** — No PHP business logic in views; only presentation (loops, conditionals, helpers). Logic in Services, Livewire, or View Components.
- [ ] **Observers** — Side effects (e.g. cache clear, event dispatch) in Observers; Observers registered in `AppServiceProvider::boot()` with `Model::observe()`.
- [ ] **No `artisan make:*` for app code** — Migrations, models, controllers, etc. are copied and adapted from existing code, not generated.

---

## Laravel Specific

- [ ] **Middleware** — Custom middleware registered in `bootstrap/app.php` (no `Kernel.php` in Laravel 12); aliases used consistently.
- [ ] **Config / env** — New config lives in `config/` or `app/Config/`; env vars documented in `.env.example` where needed.
- [ ] **Service providers** — Bindings and boot logic in the appropriate provider (e.g. `AppServiceProvider`); no duplicate registrations.
- [ ] **Jobs** — Queued jobs are idempotent where possible; safe to retry.
- [ ] **Migrations / factories / seeders** — Follow existing naming and structure; copy from existing files; no `artisan make:*` for these.
- [ ] **Routes** — Web/API routes in `routes/`; required files included from `web.php` / `api.php`; rate limiters used where appropriate.

---

## Data & Models

- [ ] **Models are not service containers** — No fat models; no cross-module access without going through a Service or Repository.
- [ ] **Enums for fixed-value columns** — Status/type/role columns use PHP enums in `app/Enums/` with `label()` and `toArray()`; cast in model `$casts`.
- [ ] **Repository traits** — Repositories reuse `app/Repositories/Traits/` (e.g. Reference, Status, ActiveTreeList) when the model has matching scopes.
- [ ] **Model traits** — Models reuse `app/Models/Traits/` (e.g. ScopeStatus, Sluggable, ClearsResponseCache) where behavior matches.
- [ ] **Nested set** — If hierarchy is involved, model extends `NestedSetModel` and repository uses `ActiveTreeList` where appropriate.

---

## API & HTTP

- [ ] **Form Requests** — User input is validated via Form Requests (or equivalent); no trusting raw request data in controllers.
- [ ] **API responses** — API controllers use `ApiResponseTrait` (success, error, failValidation, etc.); JSON shape: `status`, `code`, `message`, `data`/`errors`. No `JsonResource`/`ApiResource`.
- [ ] **Exceptions** — Custom exceptions extend `BaseException` (e.g. `BaseBusinessException`, `BaseNotFoundException`); userMessage vs technicalMessage convention respected.

---

## Security

- [ ] **Input validation** — All external input validated; no blind trust of user or client data.
- [ ] **No leaking internals** — No stack traces, sensitive config, or internal paths exposed to frontend or API responses.
- [ ] **Auth / permissions** — Authorization (e.g. Policies, `Gate::forUser()`) used where required; no missing checks for sensitive operations.

---

## Safety

- [ ] **Error handling** — Expected failures are caught and handled; no uncaught exceptions in normal user/API flows.
- [ ] **Graceful failure** — Failures return appropriate responses (e.g. error(), failServer()); user sees a clear outcome, not raw errors.
- [ ] **Data integrity** — Critical operations use transactions where needed; no partial writes that leave data inconsistent.
- [ ] **Safe defaults** — New config, flags, or options have safe defaults; dangerous behavior is opt-in, not default.

---

## Performance

- [ ] **No N+1 queries** — Eager loading or single-query patterns used where applicable; no obvious N+1 in loops.
- [ ] **Caching** — Response cache, query cache, or job caching used appropriately; routes that must not be cached use `doNotCacheResponse`.
- [ ] **Payloads** — API and responses do not return unnecessarily large or redundant data.

---

## Style & Maintainability

- [ ] **Strict types** — `declare(strict_types=1);` in PHP files; explicit return types and parameter types.
- [ ] **Imports** — `use` statements ordered alphabetically; no unused imports.
- [ ] **Naming & style** — Follows existing naming and structure; PSR-12 (PHP); project conventions for TS/SCSS.
- [ ] **DRY** — No obvious duplication that should be extracted; reuse of existing helpers/repos/services where applicable.
- [ ] **Clarity over cleverness** — Readable, explicit code preferred; no unnecessary abstractions.
- [ ] **Consistency** — Matches patterns and style of the surrounding codebase; no one-off styles.
- [ ] **Complex logic** — Non-obvious logic is commented or documented so it can be maintained later.

---

## i18n & User-Facing Text

- [ ] **Translated strings** — User-visible text uses `__t()` / `__tv()` (or equivalent); no hardcoded strings for UI unless marked as temporary.
- [ ] **Language files** — When new strings are added, all supported language files (`lang/en/`, `lang/zh_CN/`, `lang/zh_TW/`) are updated.

---

## Front-End (if changed)

- [ ] **Semantic HTML** — Use of `<button>`, `<a>`, `<form>`, etc. where appropriate; no clickable `<div>`/`<span>` for actions.
- [ ] **Accessibility** — ARIA and labels where needed; focus and keyboard considered.
- [ ] **Styles** — External stylesheets; class-based styling; no inline styles for layout/theme.
- [ ] **TypeScript** — Typed where applicable; follows `docs/typescript-conventions.md` if present.

---

## Tests

- [ ] **Coverage** — New or changed behavior has corresponding tests (or a justified omission / TODO).
- [ ] **Existing tests** — Change does not break existing tests; test intent is preserved when updating tests.
- [ ] **PHP** — PHPUnit tests in `tests/Feature/`, `tests/Unit/`, or `tests/Integration/` as appropriate; run with `./vendor/bin/phpunit` or `npm run test`.
- [ ] **Front-end** — Vitest tests in `tests/frontend/` for TS/JS when applicable; run with `npm run test`.

---

## Documentation

- [ ] **Feature docs** — New behavior has a new feature document (from `docs/feature-template.md`); existing feature docs are not rewritten when behavior changes (see AGENTS.md).
- [ ] **ADRs** — Significant architectural changes are reflected in or added to `docs/adr/` where appropriate.
- [ ] **Docs location** — Project documentation lives under `docs/`; no new docs in unrelated places.

---

## Dependencies & Config

- [ ] **No new packages without justification** — New Composer/npm dependencies are necessary and reason is documented (in commit or PR).
- [ ] **Config / env** — New config keys or env vars are documented (e.g. in `.env.example` or config comments) where relevant.

---

## Forbidden (blockers)

- [ ] **No direct Model usage in controllers/services for DB** — Use Repositories only.
- [ ] **No business logic in Blade views** — Only presentation.
- [ ] **No silent refactors of unrelated code** — Stay in scope.
- [ ] **No new global helpers** — Use existing `app/Helpers/` or scoped helpers.
- [ ] **No removal or breaking change of public APIs** without explicit intent and documentation.

---

## Summary

- **Pass** — All applicable items checked; no blockers.
- **Request changes** — List specific items failed and where (file/line or area).
- **Suggest improvements** — Optional, non-blocking notes (e.g. "consider eager loading here") without demanding out-of-scope refactors.

When in doubt, refer to `AGENTS.md`, `docs/README.architecture.md`, and `docs/adr/`.
