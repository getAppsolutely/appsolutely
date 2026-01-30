# [Feature Name]

**Short, explicit name:** [One-line description of what this feature does and who it’s for.]

## Table of Contents

<!-- Optional: add/remove sections to match the feature -->

- [Problem Statement](#problem-statement)
- [Goals](#goals)
- [Non-Goals](#non-goals)
- [Overview](#overview)
- [User Flow](#user-flow)
- [System Flow](#system-flow)
- [Data Changes](#data-changes)
- [Quick Start](#quick-start)
- [Core Concepts](#core-concepts)
- [Use Cases](#use-cases)
- [Key Paths](#key-paths)
- [Performance](#performance)
- [Security](#security)
- [Backward Compatibility](#backward-compatibility)
- [Failure Scenarios](#failure-scenarios)
- [Testing](#testing)
- [Troubleshooting](#troubleshooting)
- [References](#references)

---

## Problem Statement

[Why this feature exists: business reason, pain point, or opportunity. What problem are we solving?]

---

## Goals

[What this feature MUST do. Bullet list of required outcomes.]

- …
- …

---

## Non-Goals

[What this feature explicitly will NOT do. Scope boundaries to avoid misunderstanding.]

- …
- …

---

## Overview

[Brief summary: what the feature is and main capabilities. Use bullet lists for supported formats, options, or constraints.]

**Key capabilities:**

- …
- …

---

## User Flow

[End-to-end flow from the user’s perspective.]

### Trigger

[What initiates the feature: user action, schedule, webhook, etc.]

### Processing

[Steps the user or system goes through; main decisions and states.]

### Output

[What the user sees or receives; side effects visible to the user.]

---

## System Flow

[How the system implements the feature: which layers and components are involved.]

### Controllers involved

[HTTP/API controllers; entry points and their responsibilities.]

### Services involved

[Service classes and interfaces; main business logic.]

### Events / Jobs

[Events dispatched; queued jobs; listeners and their roles.]

---

## Data Changes

[Database impact of this feature. Omit if no schema changes.]

### Tables

[New or modified tables.]

### Columns

[New or modified columns, with types and purpose.]

### Migrations

[Migration file(s); order if multiple.]

### Constraints

[Indexes, foreign keys, unique constraints, enums.]

---

## Quick Start

[Optional. Minimal steps or commands to use the feature. Omit if not applicable.]

```bash
# Example command or code
```

**Default behavior:** [What happens by default; any important assumptions.]

---

## Core Concepts

[Explain the main ideas, terminology, and how the feature is structured. Use subsections (###) as needed.]

### [Concept 1]

…

### [Concept 2]

…

---

## Use Cases

[Typical scenarios: who uses it, when, and for what. Bullet list or short paragraphs.]

---

## Key Paths

| Purpose   | Location |
| --------- | -------- |
| [Purpose] | [Path]   |
| [Purpose] | [Path]   |

---

## Performance

[Optional. Caching, queries, N+1, payload size, limits. Omit if not relevant.]

---

## Security

[Optional. Input validation, auth, permissions, sensitive data. Omit if not relevant.]

---

## Backward Compatibility

[Optional. How this feature behaves with existing data, APIs, or clients; breaking changes and migration path. Omit if not relevant.]

---

## Failure Scenarios

[What can go wrong and how the system should behave.]

### What can fail

[List failure modes: invalid input, timeouts, missing data, etc.]

### How it should fail

[Expected behavior on failure: error codes, messages, logging, user feedback, retries.]

---

## Testing

[Optional. How to run tests for this feature; where tests live. Omit if covered elsewhere.]

---

## Troubleshooting

[Optional. Common issues and fixes. Omit if not needed.]

---

## References

- [Related doc or ADR]
- [External link if relevant]
