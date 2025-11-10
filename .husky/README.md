# Husky Git Hooks

This directory contains Git hooks managed by [Husky](https://typicode.github.io/husky/).

## What is Husky?

Husky makes it easy to share Git hooks with your team. All hooks are:

- ✅ Tracked in version control
- ✅ Automatically installed when running `npm install`
- ✅ Consistent across all team members

## Current Hooks

### `pre-commit`

Runs before every commit to automatically format and lint your code.

**What it does:**

1. ✅ Validates that required tools (npx, lint-staged) are available
2. ✅ Automatically installs dependencies if missing
3. ✅ Runs `lint-staged` which checks only staged files (fast!)
4. ✅ For PHP files: Laravel Pint formatting
5. ✅ For Blade templates: Laravel Pint formatting
6. ✅ For SCSS files: Prettier formatting + Stylelint fixes
7. ✅ For TS/JS files: Prettier formatting + ESLint fixes
8. ✅ For other files (CSS, JSON, MD, HTML, Vue): Prettier formatting
9. ✅ Provides clear error messages and suggestions

**Configuration:** See `lint-staged` section in `package.json`

### `pre-push`

Runs before pushing to remote to ensure code quality.

**What it does:**

1. ✅ Checks if there are commits to push (skips if nothing to push)
2. ✅ TypeScript type checking (all projects)
3. ✅ Linting (ESLint + Stylelint)
4. ✅ Blade template validation (108+ files) - can be skipped with `SKIP_BLADE_CHECK=1`
5. ✅ Frontend tests (Vitest) - can be skipped with `SKIP_FRONTEND_TESTS=1`
6. ✅ PHP tests (optional, can be skipped with `SKIP_PHP_TESTS=1`)
7. ✅ Colored output for better readability
8. ✅ Clear error messages with suggestions

**Features:**

- **Smart skipping**: Only runs checks if there are commits to push
- **Blade template validation**: Validates syntax of all Blade templates (108+ files)
- **Frontend test validation**: Runs Vitest tests to catch broken frontend code
- **Optional PHP tests**: Set `SKIP_PHP_TESTS=1` to skip PHP tests during pre-push
- **Optional frontend tests**: Set `SKIP_FRONTEND_TESTS=1` to skip frontend tests during pre-push
- **Optional Blade validation**: Set `SKIP_BLADE_CHECK=1` to skip Blade template validation
- **Better error messages**: Shows exactly what failed and how to fix it
- **Progress indicators**: Clear visual feedback during checks

## Setup for Team Members

When you clone this repo and run `npm install`, Husky automatically:

1. Installs Git hooks from `.husky/` to `.git/hooks/`
2. Makes them executable
3. Ready to use!

No manual setup needed! 🎉

## Testing the Hook

```bash
# Make a change to any tracked file
echo "// test" >> themes/june/sass/_variables.scss

# Stage it
git add themes/june/sass/_variables.scss

# Try to commit
git commit -m "test: husky hook"

# You should see:
# ✔ Preparing lint-staged...
# ✔ Running tasks for staged files...
# ✔ Prettier formatting...
# ✔ Stylelint fixes...
# ✔ Applying modifications...
# ✔ Cleaning up temporary files...
```

## Bypassing Hooks (Emergency Only)

⚠️ **Not recommended**, but if absolutely necessary:

```bash
# Skip pre-commit hook
git commit --no-verify -m "emergency fix"

# Skip pre-push hook
git push --no-verify
```

## Pre-push Hook Options

### Skip Blade Template Validation

If Blade validation is slow or you want to skip it during pre-push:

```bash
# Skip Blade validation for this push
SKIP_BLADE_CHECK=1 git push

# Or export it for the session
export SKIP_BLADE_CHECK=1
git push
```

### Skip Frontend Tests

If frontend tests are slow or you want to skip them during pre-push:

```bash
# Skip frontend tests for this push
SKIP_FRONTEND_TESTS=1 git push

# Or export it for the session
export SKIP_FRONTEND_TESTS=1
git push
```

### Skip PHP Tests

If PHP tests are slow or you want to skip them during pre-push:

```bash
# Skip PHP tests for this push
SKIP_PHP_TESTS=1 git push

# Or export it for the session
export SKIP_PHP_TESTS=1
git push
```

### Skip Multiple Checks

```bash
# Skip Blade validation and frontend tests
SKIP_BLADE_CHECK=1 SKIP_FRONTEND_TESTS=1 git push

# Skip all optional checks
SKIP_BLADE_CHECK=1 SKIP_FRONTEND_TESTS=1 SKIP_PHP_TESTS=1 git push
```

## Troubleshooting

### Hook not running?

```bash
# Reinstall hooks
npm run prepare
```

### lint-staged not found?

```bash
# Reinstall dependencies
npm install
```

### Want to run checks manually?

```bash
# Run lint-staged manually
npx lint-staged

# Or run formatters/linters directly
npm run format:scss
npm run lint:scss:fix
./vendor/bin/pint
```

## Configuration Files

- **Husky setup**: `package.json` → `"prepare": "husky"`
- **lint-staged config**: `package.json` → `"lint-staged"`
- **Pre-commit hook**: `.husky/pre-commit`
- **Pre-push hook**: `.husky/pre-push`
- **Commit message hook**: `.husky/commit-msg` (validates conventional commits)
- **Prettier config**: `.prettierrc.json` (if exists)
- **Stylelint config**: `.stylelintrc.json` (if exists)
- **Pint config**: `pint.json`

## Benefits

✅ **Consistency** - Same code quality for everyone  
✅ **Speed** - Only checks staged files  
✅ **Automatic** - No manual formatting needed  
✅ **Team-friendly** - Shared via Git  
✅ **Modern** - Industry best practice

---

For more info: https://typicode.github.io/husky/
