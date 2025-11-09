# Architecture Decision Records (ADRs)

This directory contains Architecture Decision Records (ADRs) documenting key architectural decisions made for this project.

## What are ADRs?

Architecture Decision Records are documents that capture important architectural decisions made along with their context and consequences. They help:

- Understand why certain decisions were made
- Onboard new team members
- Maintain consistency across the codebase
- Avoid revisiting already-decided questions

## ADR Index

| ADR                                            | Title                                     | Status   |
| ---------------------------------------------- | ----------------------------------------- | -------- |
| [001](001-repository-pattern.md)               | Repository Pattern for Data Access        | Accepted |
| [002](002-service-layer-with-interfaces.md)    | Service Layer with Interface-Based Design | Accepted |
| [003](003-strict-type-declarations.md)         | Strict Type Declarations                  | Accepted |
| [004](004-final-readonly-service-classes.md)   | Final Readonly Service Classes            | Accepted |
| [005](005-dependency-injection-pattern.md)     | Dependency Injection via Constructor      | Accepted |
| [006](006-model-observers-for-side-effects.md) | Model Observers for Side Effects          | Accepted |
| [007](007-base-controller-hierarchy.md)        | Base Controller Hierarchy                 | Accepted |
| [008](008-theme-system-architecture.md)        | Multi-Theme System Architecture           | Accepted |

## Quick Reference

### Data Access

- **Repository Pattern** (ADR 001): All database access through repositories

### Business Logic

- **Service Layer** (ADR 002): Business logic in services with interfaces
- **Dependency Injection** (ADR 005): Constructor injection for all dependencies

### Code Quality

- **Strict Types** (ADR 003): `declare(strict_types=1);` in all files
- **Final Readonly Classes** (ADR 004): Services are `final readonly class`

### Architecture Patterns

- **Model Observers** (ADR 006): Side effects handled in observers
- **Base Controllers** (ADR 007): Hierarchical base controller structure
- **Theme System** (ADR 008): Multi-theme architecture with service layer

## Creating New ADRs

When making a significant architectural decision:

1. Create a new ADR file: `XXX-decision-title.md`
2. Use the template below
3. Number sequentially (009, 010, etc.)
4. Update this README with the new ADR

### ADR Template

```markdown
# ADR XXX: Decision Title

**Status:** Proposed/Accepted/Deprecated  
**Date:** YYYY-MM-DD  
**Deciders:** Team/Individual

## Context

Describe the issue motivating this decision.

## Decision

State the architectural decision.

## Consequences

### Positive

- Benefit 1
- Benefit 2

### Negative

- Drawback 1
- Drawback 2

## References

- Link to related code/docs
```

## References

- [ADR Template](https://adr.github.io/)
- [Documenting Architecture Decisions](https://cognitect.com/blog/2011/11/15/documenting-architecture-decisions)
