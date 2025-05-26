# 🧩 Page Builder – Feature Specification (Full Version)

> A drag-and-drop visual layout builder for landing pages, CMS, and low-code platforms. Supports dynamic layout, component configuration, and publish-ready page structures.

---

## 🚀 1. Core Features

### 🔧 Layout & Canvas
- Drag-and-drop components from a right sidebar into the **page canvas**
- All components are automatically wrapped in a `container` (representing a row)
- A container supports:
  - Vertical layout (`col-12`)
  - Horizontal grid layout (e.g., `col-6`)
- Supports drag-to-reorder within the same container
- Supports **cross-container movement**
- If only one component remains in a container, it auto-expands to full width

### 🧱 Component Library
- Right sidebar shows categorized components (e.g., Layout, Media, Hero, Footer)
- Each component supports:
  - Drag-to-add
  - Variant selection after click (e.g., Hero A, Hero B, Hero C)
  - Component metadata (preview image, tags, category)

### ⚙️ Component Configuration Panel
- Each component has action buttons on hover:
  - ⚙️ Settings (opens configuration form)
  - ❌ Delete
  - 🔒 Lock (prevents moving or editing)
  - 👁️ Hide (visible in editor but hidden in preview)
- Configuration schema supports:
  - Primitive values (text, image, boolean)
  - Arrays (e.g., button lists, tab panes)
  - Nested objects (multi-level config)
- Full **i18n support** for all config fields

---

## 🧠 2. Page-Level Features

### 📄 Page Settings Panel
- Global settings for:
  - Page title, description, SEO tags
  - Background, fonts, spacing, color palette
- Supports save / apply templates

### 📝 Draft & Auto-Save
- Auto-saves editing progress to local storage
- Supports "Save Draft" and "Restore Draft"

### 📤 Publish Function
- Exports a full JSON structure on publish
- Sent to backend for:
  - Static rendering
  - Version history
  - Previews
- Separation of Editor View vs. Public View

---

## 🧰 3. Enhanced Editing Experience

### ⏪ Undo / Redo History Stack
- Tracks all user actions: drag, config, delete
- Enables safe rollback with multiple levels

### 💬 Live Configuration Injection
- When a component is inserted, config modal opens automatically (optional)

### 📱 Responsive Preview
- Editor toolbar supports device preview toggles:
  - Mobile, Tablet, Desktop

### 🧩 Component Templates
- Supports reusable component groups (e.g., Signup Page, Blog Layout)
- Drag one = insert many pre-configured modules

---

## 🧱 4. Architecture & Module Design

### 📦 Component Registry (from backend)
- Components are **dynamically loaded** from backend registry (no manual registry editing)
- Each component is a module folder:
