# CSCS Tap n Track — Safe Implementation & Zero-Regression Protocol (MANDATORY)

> **Purpose:** This document defines the non-negotiable rules for modifying this codebase. Every change must be made with extreme care to avoid breaking the existing (functional but messy) system. Read this ENTIRE document before making ANY changes.

---

## ⚠️ Primary Rule

**The existing system is already functional. Your primary objective is to improve it without breaking, redesigning, removing, or altering any existing behavior unless explicitly instructed.**

Treat this project as a production enterprise application where regressions are unacceptable.

---

## 🚫 Zero Regression Policy

Every change must satisfy ALL of the following:

- **UI Lock:** Existing UI must remain visually identical unless a specific improvement is requested. No layout shifts, spacing changes, color/typography changes, animation changes, or responsive breakpoint changes.
- **Function Lock:** Existing forms, AJAX requests, routes, controllers, helpers, JavaScript, CSS, and database queries must continue working exactly as before unless intentionally improved.
- **Scope Lock:** Changes must NOT affect unrelated pages, shared components, or hidden dependencies.

---

## 📋 Before Editing Any File — Mandatory Pre-Check

Before modifying any file:

1. **Read the ENTIRE file** — not just the part you want to change.
2. **Understand ALL dependencies** — search the whole project for:
   - Controllers, views, helpers, JavaScript, CSS, AJAX endpoints using this file
   - Partial views including it
   - Shared components depending on it
3. **Check for duplicate implementations** — this codebase has multiple competing versions of the same thing (e.g., 3 footer implementations).
4. **Determine if the file is reused elsewhere** — a change to a shared partial/layout breaks EVERY page that includes it.

**Never assume a file is isolated.**

---

## 🎨 Preserve Existing UI — DO NOT Change Unless Instructed

**DO NOT:**
- Redesign pages, move components, reorder sections
- Change spacing, typography, colors, icons, buttons, cards, tables
- Change dashboard arrangement, sidebar, navigation, responsive breakpoints
- Change animations, transitions, or visual effects
- Replace icon libraries or button styles

**DO:**
- Only improve issues that are directly requested in the task
- Make additive changes (adding new classes, new wrappers, new optional features)
- Extend rather than rewrite

---

## ✂️ Minimal Change Principle

**Always make the smallest possible change.**

**Prefer:** extending code, adding helper functions, adding reusable components, creating wrappers, adding optional parameters.

**Avoid:** rewriting working code, replacing entire functions/controllers/views/CSS blocks/JavaScript modules.

If only three lines need changing, only change those three lines.

---

## 🔄 Preserve Backward Compatibility

Every improvement must remain compatible with existing:
Controllers • Models • Helpers • Views • Routes • APIs • AJAX calls • Database schema • Session handling • Authentication • Cookies • Permissions

**Never introduce breaking changes.**

---

## 🧩 Shared Component Safety

Before modifying partials, helpers, layouts, base templates, CSS utilities, or JavaScript utilities:

- **Search the entire project** — determine every location where they are used.
- If multiple pages depend on them, **verify all affected pages**.
- **Never introduce page-specific behavior into shared code** without feature flags or optional configuration.

---

## 🎯 CSS Safety Rules

**Never:**
- Delete CSS without confirming it is unused
- Rename classes globally
- Increase selector specificity unnecessarily
- Introduce broad selectors (e.g., bare `div { }`)
- Use global resets
- Change existing spacing variables
- Remove `!important` unless verified safe on every affected page
- Modify shared utility classes without checking every usage

**Always prefer:** scoped selectors, component-specific overrides, new utility classes, isolated improvements.

---

## ⚡ JavaScript Safety Rules

Before editing JS, check: event listeners, delegated events, AJAX endpoints, shared utility functions, initialization order, GSAP dependencies, modal interactions, dynamic content, responsive behavior.

**Never:** duplicate event listeners, initialize the same component twice, introduce memory leaks.

---

## 🏗️ Controller Safety

Large controllers (especially the 1,699-line Admin/Dashboard) must **NOT** be rewritten all at once.

Instead: refactor incrementally. Preserve method signatures, routes, responses, redirects, flash messages, JSON formats.

Split functionality only when safe.

---

## 🗄️ Database Safety

**Never:** rename columns, delete fields, modify schema, change query results unless explicitly instructed.

Parameterize queries without changing returned data.

---

## 🔒 Security Fixes

Security improvements must preserve existing workflows, authentication, permissions, and user sessions. Replace insecure implementations with secure equivalents **without changing the user experience**.

---

## 🚀 Performance Improvements

Optimize behind the scenes. Never remove functionality, animations, visual effects, or change UI behavior for the sake of performance.

---

## ♿ Accessibility Improvements

Accessibility updates should be **additive**: ARIA labels, keyboard navigation, focus management, semantic HTML, screen reader support. These must NEVER alter visual appearance.

---

## 📱 Responsive Safety

After EVERY UI change, verify on: Desktop, Laptop, Tablet, Mobile, Small mobile, Large monitor.

Check for: No overflow, no clipping, no wrapping issues, no layout shifts, no hidden buttons, no broken tables.

---

## ✅ Regression Checklist — Run After EVERY Change

- [ ] No PHP errors / warnings
- [ ] No JavaScript errors / console warnings
- [ ] No CSS conflicts / unintended overrides
- [ ] No broken routes
- [ ] No broken forms (submission, validation, file upload)
- [ ] No broken AJAX requests
- [ ] No broken pagination, search, filters
- [ ] No broken tables, modals, animations
- [ ] No broken responsive layouts
- [ ] No broken sidebar, navigation, authentication, permissions
- [ ] No broken sessions, cookies, notifications
- [ ] No broken landing page, admin pages, CHILDPRO pages, GAD pages, reports
- [ ] No visual regressions (compare before/after screenshots if possible)

---

## 📦 Incremental Implementation Strategy

For ALL improvements:

1. **Analyze first** — read all affected files completely
2. **Identify all affected files** — trace every dependency
3. **Explain the implementation plan** before coding
4. **Apply changes in small logical batches** (1-3 files at a time)
5. **Verify each batch** before proceeding to the next
6. **Stop immediately** if a regression is detected — roll back, diagnose, fix
7. **Never combine unrelated refactors** into one update

---

## ❓ If Unsure — STOP

If there is any uncertainty about how a change may affect existing functionality:

- **Do NOT guess.**
- **Search the codebase thoroughly.**
- **Trace dependencies.**
- **Verify every usage.**
- **Choose the least invasive solution.**

**Conservative, well-tested changes are always preferred over aggressive refactoring.**

---

## 🏁 Final Verification

Before considering any task complete:

✓ Existing UI remains visually unchanged except for the requested enhancement
✓ Existing functionality behaves exactly as before
✓ No unrelated pages were affected
✓ No shared components were unintentionally modified
✓ No CSS or JavaScript regressions were introduced
✓ The application is more secure, maintainable, and performant while remaining fully backward compatible

**The success criterion is not simply that the requested feature works — it is that the entire application continues to work exactly as before, with zero unintended regressions.**

---

## 📌 Critical Context: This Codebase

This project has:
- **3,430 lines of CSS** in a single file (`app.css`) with massive duplication
- **100+ `!important` declarations** creating a specificity war
- **Multiple conflicting CSS definitions** for the same components
- **1,699-line Admin/Dashboard controller** violating single responsibility
- **3 competing footer implementations**
- **20+ selectors defined 2-5 times each**
- **No automated front-end tests**
- **Exposed debug/auth-fix endpoints** (being fixed)
- **No CSRF tokens** in AJAX requests (being added)
- **Duplicate HTML IDs** (e.g., `transparencyDropdown` used twice)

**The codebase is messy. Proceed with extreme caution. Small changes can have cascading, unpredictable effects.**