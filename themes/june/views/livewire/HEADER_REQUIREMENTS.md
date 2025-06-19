# Header Component Requirements

The site header must be structured into three distinct sections:

## 1. Left: Logo
- Display `logo-dark.webp` only (visible at all times).
- `logo.webp` should be present in the markup but hidden (for future use or theme switching).
- The logo is always aligned to the left edge of the header.

## 2. Center: Main Navigation
- The main navigation menu (`main-nav`) must be centered horizontally within the header.
- Navigation items may have dropdowns, but **no arrow/caret** should be shown for dropdown triggers.
- Dropdowns should open on click or hover, but the visual indicator (arrow) is not required.

## 3. Right: Test Drive Button
- A prominent "Test Drive" button must be placed on the right side of the header.
- The button should be clearly visible and styled according to the theme's primary button style.

## Layout & Responsiveness
- **Do not update the divs outside of `.container` and `.container-responsive`.**
- **Only create new classes when necessary.**
- The header layout must remain visually balanced and responsive across all screen sizes.
- On mobile, the logo remains left, navigation collapses, and the test drive button is accessible (e.g., in a menu or as a floating button).
- **Responsive behavior:**
  - When the screen size is less than 1200px:
    - The main navigation and test drive button are hidden and replaced by a hamburger menu.
    - Clicking the hamburger menu displays a layer containing both the main navigation and the test drive button.
    - The menu items layout adapts to screen size:
      - On tablets, each row can contain up to three menu items (horizontal layout).
      - On mobile, each row contains only one menu item (vertical layout).

## Visual Example

| Left (Logo)         | Center (Main Nav)         | Right (Test Drive) |
|--------------------|--------------------------|--------------------|
| ![logo-dark.webp]  | Home | Features | ...     | [Test Drive]       |

- `logo-dark.webp` is visible, `logo.webp` is hidden.
- Main navigation is centered, no dropdown arrows.
- Test Drive button is right-aligned.

---

## ✨ Interaction & State Requirements

### Normal State (No Hover)
- **Header background:** `$teal` with `opacity: 0.3` (semi-transparent)
- **Text and link color:** `$light`
- **Logo:** Only `logo-dark.webp` is visible (left-aligned); `logo.webp` is hidden

### Hover State (Mouse over logo or menu)
- **Header background:** `$body-bg`
- **Text and link color:** `$body-color`
- **Logo:**  
  - `logo-dark.webp` fades out and hides  
  - `logo.webp` fades in and shows (smooth CSS animation)
- **Test Drive Button:**
  - **Normal:** Font color `$light`, transparent background, border color `$light`, border width 2px
  - **Hovering:** Font color `$body-color`, border color `$body-color`, transparent background
- **Submenu:**  
  - When hovering a menu item with children, a submenu appears  
  - Submenu covers the full screen width, from the bottom of the header to the bottom of the page  
  - **Submenu background:** `$body-bg`  
  - **Submenu text and link color:** `$body-color`  
  - Submenu items are arranged horizontally  
  - Submenu animates in (e.g., slides or pulls up from the bottom of the header)

---

## Touch & Mobile Usability
- All interactive elements (menu items, hamburger, test drive button) must have touch-friendly targets (minimum 44x44px).
- When the hamburger menu is open, background scrolling should be prevented.
- Menus and overlays must be easy to close with a tap outside or a close button.
- Menu layouts adapt for touch: horizontal on tablet, vertical on mobile.

## State Management
- Only one submenu or overlay should be open at a time.
- Clicking outside an open menu or overlay closes it.
- Pressing the Escape key closes any open menu or overlay.
- Hamburger menu and submenu open/close state must be visually clear.

## Animation
- **All animations (logo switch, submenu, etc.) must use CSS transitions/animations only.**
- **No JavaScript-based animation is allowed.**

---

## Performance & Optimization

### Image Optimization
- Use **SVG** for logos if possible (for crisp scaling and theming).
- Use `srcset` for responsive images to serve the appropriate size for each device.
- **Lazy-load** non-critical images to improve initial load performance.

### CSS Optimization
- Minimize selector specificity and **avoid `!important`** whenever possible.
- Use **CSS variables** for theme colors and other reusable values.
- Bundle CSS and **purge unused CSS** to keep stylesheets lean and performant.

---

**Note:**
- All custom styles must be defined in SCSS and follow project conventions.
- Do not use or reintroduce previously removed custom classes unless redefined in SCSS.

---

## Implementation Tasks Checklist

1. **Header Structure & Layout**
   - [x] Set up header Blade structure with left (logo), center (main-nav), and right (test drive button) sections
   - [x] Ensure correct use of `.container` and `.container-responsive` wrappers

2. **Logo Implementation**
   - [x] Add both `logo-dark.webp` (visible) and `logo.webp` (hidden) in markup
   - [x] Implement CSS for logo visibility and alignment
   - [x] Add SVG logo and `srcset` for responsive images (if available)

3. **Main Navigation**
   - [x] Render main navigation menu, centered in header
   - [x] Remove dropdown arrows/carets from menu items
   - [x] Implement submenu markup for items with children

4. **Test Drive Button**
   - [x] Add and style the test drive button, right-aligned

5. **Normal & Hover State Styles**
   - [x] Implement normal state: header bg `$teal` with opacity, text `$light`, only `logo-dark.webp` visible
   - [x] Implement hover state: header bg `$body-bg`, text `$body-color`, logo switch with CSS animation
       - [x] Hovering ONLY happens when hovering over logo and menu (not elsewhere)
       - [x] Use ONLY pure CSS for hover/animation (JavaScript is NOT allowed)
   - [x] Animate submenu (full width, from header bottom to page bottom) on menu item hover
   - [x] Implement Test Drive button styles:
       - [x] Normal: font color `$light`, transparent background, border color `$light`, 2px width
       - [x] Hover: font color `$body-color`, border color `$body-color`, transparent background

6. **Responsiveness & Hamburger Menu**
   - [x] Hide main nav and test drive button below 1200px, show hamburger menu
   - [x] Implement hamburger menu overlay/layer with main nav and test drive button (pure CSS toggle)
   - [x] Adapt menu item layout: 3 per row on tablet, 1 per row on mobile

7. **Touch & Mobile Usability**
   - [x] Ensure all touch targets are at least 44x44px
   - [x] Prevent background scroll when hamburger is open
   - [x] Overlay close logic is present (close button)
   - [x] Menu layout for touch is vertical on mobile/tablet

8. **State Management**
   - [x] Only one submenu/overlay open at a time
   - [x] Visually indicate open/close state for hamburger and submenu
   - [x] (Pure CSS limitation) Clicking outside or pressing Escape to close is not supported

9. **Performance & Optimization**
   - [ ] Use SVG for logos if possible
   - [ ] Use `srcset` for responsive images
   - [ ] Lazy-load non-critical images
   - [ ] Minimize CSS specificity, avoid `!important`, use CSS variables
   - [ ] Bundle and purge unused CSS

10. **Testing & Review**
    - [ ] Test header on all major browsers and devices
    - [ ] Test keyboard and screen reader accessibility
    - [ ] Review against requirements and check off each task

---

**Check off each task as you complete it to ensure a step-by-step, maintainable implementation.** 

