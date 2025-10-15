# 🚀 Quick Reference - Development Workflow

## ⚡ Most Used Commands

```bash
# Before committing
npm run validate              # Check everything
npm run format:all            # Format and fix all

# Development
npm run dev:all              # Start all dev servers
composer run dev             # Start Laravel + Queue + Vite

# Linting (manual)
npm run lint:fix             # Fix JS/TS
npm run lint:scss:fix        # Fix SCSS
./vendor/bin/pint            # Fix PHP

# Testing
npm run test                 # All tests
./vendor/bin/phpunit         # PHP tests only
```

## 📝 Commit Message Cheatsheet

```bash
feat(scope): add new feature
fix(scope): fix bug
docs: update documentation
style: formatting changes
refactor: code restructuring
perf: performance improvements
test: add/update tests
build: dependency updates
ci: CI/CD changes
chore: maintenance tasks
```

## 🎯 Pre-commit Checklist

- [ ] Code is formatted
- [ ] No linting errors
- [ ] TypeScript types are correct
- [ ] Tests pass (if applicable)
- [ ] Commit message follows convention

## 🔥 Quick Fixes

```bash
# Reinstall hooks
npm run prepare

# Fix all formatting issues
npm run format:all

# Bypass hooks (emergency only!)
git commit --no-verify
git push --no-verify
```

## 📊 File-specific Commands

```bash
# Format specific file
prettier --write path/to/file.ts
./vendor/bin/pint path/to/File.php

# Lint specific file
eslint path/to/file.ts --fix
stylelint path/to/file.scss --fix
```

## 🛑 What NOT to Do

- ❌ Don't bypass hooks regularly
- ❌ Don't commit without meaningful messages
- ❌ Don't push failing tests
- ❌ Don't ignore linting errors
- ❌ Don't commit `node_modules` or `vendor`

## ✅ Best Practices

- ✅ Run `npm run validate` before commits
- ✅ Write clear commit messages
- ✅ Keep commits atomic and focused
- ✅ Run tests before pushing
- ✅ Fix linting issues immediately
