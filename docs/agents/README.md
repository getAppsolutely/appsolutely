## Agent Roles

Agents are split by role. Each agent has a dedicated doc in this directory with full responsibilities, focus, and forbidden actions.

### Workflow

1. **Architect** → produces `docs/feature/*.md` (feature design docs). Naming: `feature-name.extension.md` (extension), `feature-name.v2.md` (version), `feature-name.experiment.md` (experiment).
2. **Human review** — approve or adjust the design
3. **Implementer** → implements code from the approved feature doc
4. **Reviewer** → reviews code against [Reviewer-checklist.md](Reviewer-checklist.md)
5. **Refactorer** (optional) — improves code only when requested; small, scoped refactors

| Agent           | Doc                              | Summary                                                                                                             |
| --------------- | -------------------------------- | ------------------------------------------------------------------------------------------------------------------- |
| **Architect**   | [Architect.md](Architect.md)     | Produce feature design docs; define boundaries & data flow; reject bad ideas early. Does not write production code. |
| **Implementer** | [Implementer.md](Implementer.md) | Implement strictly from feature docs; follow `AGENTS.md`. Does not change scope or refactor unrelated modules.      |
| **Reviewer**    | [Reviewer.md](Reviewer.md)       | Review code against checklist; identify risk and maintainability, not style bikeshedding.                           |
| **Refactorer**  | [Refactorer.md](Refactorer.md)   | Improve code only with approval; small, scoped refactors. Does not do large rewrites.                               |

---

- [Architect](Architect.md)
- [Implementer](Implementer.md)
- [Reviewer](Reviewer.md)
- [Refactorer](Refactorer.md)
