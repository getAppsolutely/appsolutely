# 🔄 Development Workflow - Visual Diagram

## 📊 Complete Git Workflow

```
┌─────────────────────────────────────────────────────────────────────┐
│                         Developer Workflow                          │
└─────────────────────────────────────────────────────────────────────┘

1. WRITE CODE
   │
   ├──> Edit files
   ├──> Add features
   └──> Fix bugs

   ↓

2. STAGE CHANGES
   │
   └──> git add .

   ↓

3. COMMIT
   │
   └──> git commit -m "type(scope): message"

   ↓

┌────────────────────────────────────────┐
│   🎣 PRE-COMMIT HOOK (lint-staged)    │
├────────────────────────────────────────┤
│                                        │
│  *.php         → Laravel Pint          │
│  *.scss        → Prettier → Stylelint  │
│  *.{ts,js}     → Prettier → ESLint     │
│  *.{css,json}  → Prettier              │
│                                        │
│  ✅ Auto-fix issues                    │
│  ✅ Re-stage fixed files               │
│                                        │
└────────────────────────────────────────┘

   ↓

┌────────────────────────────────────────┐
│   💬 COMMIT-MSG HOOK                   │
├────────────────────────────────────────┤
│                                        │
│  Validate commit message format:      │
│  type(scope): subject                  │
│                                        │
│  ✅ feat, fix, docs, style...          │
│  ❌ "fixed stuff", "WIP"               │
│                                        │
└────────────────────────────────────────┘

   ↓

   Commit Created ✅

   ↓

4. PUSH TO REMOTE
   │
   └──> git push origin branch-name

   ↓

┌────────────────────────────────────────┐
│   🚀 PRE-PUSH HOOK                     │
├────────────────────────────────────────┤
│                                        │
│  1. TypeScript Type Check              │
│     → npm run type-check:all           │
│                                        │
│  2. ESLint Validation                  │
│     → npm run lint                     │
│                                        │
│  3. Stylelint Validation               │
│     → npm run lint:scss                │
│                                        │
│  ✅ All checks must pass               │
│  ❌ Push rejected if any fail          │
│                                        │
└────────────────────────────────────────┘

   ↓

   Pushed to Remote ✅

   ↓

┌─────────────────────────────────────────────────────────────────┐
│                   🔄 GITHUB ACTIONS CI/CD                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────────────┐    ┌──────────────────────┐          │
│  │   Frontend Job       │    │   Backend Job        │          │
│  ├──────────────────────┤    ├──────────────────────┤          │
│  │                      │    │                      │          │
│  │ 1. Format Check      │    │ 1. PHP 8.2 Setup     │          │
│  │ 2. ESLint            │    │ 2. Pint Check        │          │
│  │ 3. Stylelint         │    │ 3. Run Migrations    │          │
│  │ 4. Type Check        │    │ 4. PHPUnit Tests     │          │
│  │ 5. Build Assets      │    │                      │          │
│  │                      │    │ (Repeat for PHP 8.3) │          │
│  └──────────────────────┘    └──────────────────────┘          │
│           │                           │                         │
│           └───────────┬───────────────┘                         │
│                       ↓                                         │
│              ┌─────────────────┐                                │
│              │ Validation Job  │                                │
│              ├─────────────────┤                                │
│              │ All checks pass │                                │
│              └─────────────────┘                                │
│                       ↓                                         │
│                  ✅ CI Success                                  │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘

   ↓

5. CREATE PULL REQUEST
   │
   ├──> Review CI results
   ├──> Code review by team
   └──> Merge when approved

   ↓

   Merged to main/master ✅
```

---

## 🎯 File Type Processing Flow

```
┌─────────────────────────────────────────────────────────────┐
│                     File Type Handlers                      │
└─────────────────────────────────────────────────────────────┘

PHP Files (*.php)
   ↓
   Laravel Pint
   ↓
   PSR-12 formatted code ✅

─────────────────────────────────────────────────────────────

SCSS Files (*.scss)
   ↓
   Prettier (Format)
   ↓
   Stylelint (Validate + Fix)
   ↓
   Consistent SCSS ✅

─────────────────────────────────────────────────────────────

TypeScript/JavaScript (*.ts, *.js, *.tsx, *.jsx)
   ↓
   Prettier (Format)
   ↓
   ESLint (Validate + Fix)
   ↓
   Type-safe, clean code ✅

─────────────────────────────────────────────────────────────

Other Files (*.css, *.json, *.md, *.html, *.vue)
   ↓
   Prettier (Format only)
   ↓
   Consistently formatted ✅
```

---

## 🔄 Command Execution Flow

```
┌─────────────────────────────────────────────────────────────┐
│                  Command Cheat Sheet Flow                   │
└─────────────────────────────────────────────────────────────┘

Development:
   make dev  →  composer run dev  →  ┌─ php artisan serve
                                     ├─ php artisan queue:listen --queue=notifications,default
                                     ├─ php artisan pail
                                     └─ npm run dev

Testing:
   make test  →  npm run test:js  →  ┌─ Type check (all)
                 +                    ├─ ESLint
                 npm run test:php  →  └─ Stylelint
                                       PHPUnit tests

Linting:
   make lint  →  npm run lint:all  →  ┌─ ESLint
                 +                     └─ Stylelint
                 pint --test           Laravel Pint

Formatting:
   make format  →  npm run format:all  →  ┌─ Prettier (all)
                   +                       ├─ ESLint --fix
                   pint                    ├─ Stylelint --fix
                                          └─ Laravel Pint

Validation:
   make validate  →  ┌─ Format check (Prettier)
                     ├─ Lint all (ESLint + Stylelint)
                     ├─ Type check (TypeScript)
                     └─ Pint test (PHP)
```

---

## 🏗️ Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                     Project Structure                           │
└─────────────────────────────────────────────────────────────────┘

/site
  │
  ├── .husky/                    # Git hooks
  │   ├── pre-commit             # Runs lint-staged
  │   ├── commit-msg             # Validates commit format
  │   └── pre-push               # Runs quality checks
  │
  ├── .github/workflows/         # CI/CD
  │   └── ci.yml                 # GitHub Actions workflow
  │
  ├── .vscode/                   # IDE config
  │   ├── settings.json          # VSCode settings
  │   └── extensions.json        # Recommended extensions
  │
  ├── docs/                      # Documentation
  │   ├── development-workflow.md
  │   ├── quick-reference.md
  │   └── workflow-diagram.md
  │
  ├── scripts/                   # Helper scripts
  │   └── setup-workflow.sh      # Setup automation
  │
  ├── Makefile                   # Command shortcuts
  ├── package.json               # NPM config + scripts
  ├── composer.json              # Composer config
  │
  ├── .editorconfig              # Editor config
  ├── .prettierrc.json           # Prettier config
  ├── .stylelintrc.json          # Stylelint config
  ├── eslint.config.js           # ESLint config
  └── pint.json                  # Laravel Pint config
```

---

## 🎯 Quality Gate System

```
┌─────────────────────────────────────────────────────────────┐
│              Multi-Layer Quality Assurance                  │
└─────────────────────────────────────────────────────────────┘

Layer 1: IDE (Real-time)
   ↓
   ├─ ESLint warnings
   ├─ TypeScript errors
   ├─ Stylelint warnings
   └─ Format on save

Layer 2: Pre-commit (On commit)
   ↓
   ├─ Auto-format all files
   ├─ Auto-fix linting issues
   └─ Re-stage fixed files

Layer 3: Commit-msg (On commit)
   ↓
   └─ Validate message format

Layer 4: Pre-push (On push)
   ↓
   ├─ Type checking
   ├─ Linting validation
   └─ Optional: Tests

Layer 5: CI/CD (On remote)
   ↓
   ├─ Format validation
   ├─ Full linting
   ├─ Type checking
   ├─ Unit tests
   └─ Integration tests

Layer 6: Code Review (Manual)
   ↓
   └─ Human review + approval

        ↓

   Merged ✅
```

---

## 📊 Time Investment vs. Return

```
┌─────────────────────────────────────────────────────────────┐
│                   ROI Analysis                              │
└─────────────────────────────────────────────────────────────┘

Initial Setup:
   ├─ Read docs: 30 min
   ├─ Run setup: 5 min
   └─ Configure IDE: 10 min
   Total: ~45 minutes

Daily Savings (per developer):
   ├─ Auto-formatting: 5 min
   ├─ Fewer PR iterations: 10 min
   └─ Fewer bugs: 5-10 min
   Total: ~20 min/day

Weekly Savings (5 developers):
   └─ 20 min × 5 devs × 5 days = 500 minutes
      = 8.3 hours/week
      = 433 hours/year

Quality Improvements:
   ├─ 100% consistent formatting
   ├─ ~50% fewer formatting-related PRs
   ├─ ~30% fewer runtime bugs (caught by types)
   └─ Better commit history for debugging

Break-even Point:
   └─ Day 1 ✅
```

---

## 🚦 Decision Tree

```
Should I bypass hooks?

   Is it a production emergency?
      ├─ YES → Maybe bypass (fix afterward!)
      └─ NO ↓

   Are the errors trivial?
      ├─ YES → Just fix them (takes <1 min)
      └─ NO ↓

   Are you unsure how to fix?
      ├─ YES → Check docs or ask team
      └─ NO ↓

   Would fixing break something?
      ├─ YES → Investigate why, fix root cause
      └─ NO ↓

   Just feeling lazy?
      └─ YES → Don't bypass! Run:
              make lint-fix
              make format
```

---

## 📚 Quick Command Reference

```
┌─────────────────────────────────────────────────────────────┐
│            Most Used Commands (Frequency Order)             │
└─────────────────────────────────────────────────────────────┘

Daily:
   make dev              # Start development
   make format           # Quick format
   git add . && git commit -m "type: msg"

Before Push:
   make validate         # Full check
   make test             # Run tests

Fixing Issues:
   make lint-fix         # Auto-fix linting
   npm run type-check    # Check types

Setup/Maintenance:
   make setup            # Initial setup
   make hooks            # Reinstall hooks
   make clean            # Clean build artifacts

Help:
   make help             # All commands
```

---

**💡 Pro Tip:** Bookmark this diagram for quick reference during development!
