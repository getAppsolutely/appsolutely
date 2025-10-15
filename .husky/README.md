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

1. Runs `lint-staged` which checks only staged files (fast!)
2. For PHP files: Laravel Pint formatting
3. For SCSS files: Prettier formatting + Stylelint fixes
4. For other files (CSS, TS, JS, etc.): Prettier formatting

**Configuration:** See `lint-staged` section in `package.json`

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
git commit --no-verify -m "emergency fix"
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
- **Prettier config**: `.prettierrc.json`
- **Stylelint config**: `.stylelintrc.json`
- **Pint config**: Laravel default

## Benefits

✅ **Consistency** - Same code quality for everyone  
✅ **Speed** - Only checks staged files  
✅ **Automatic** - No manual formatting needed  
✅ **Team-friendly** - Shared via Git  
✅ **Modern** - Industry best practice

---

For more info: https://typicode.github.io/husky/
