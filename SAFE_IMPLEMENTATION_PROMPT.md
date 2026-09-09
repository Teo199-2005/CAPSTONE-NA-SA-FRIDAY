# ZERO-REGRESSION IMPLEMENTATION PROMPT
## Copy and paste this BEFORE any task instruction

---

**CRITICAL: This codebase is messy with 3,430 lines of CSS, 100+ `!important` declarations, 3 competing footers, 1,699-line controllers, and duplicate implementations everywhere. Small changes can cascade into unpredictable breakage. Read the full protocol at `SAFE_IMPLEMENTATION_PROTOCOL.md` before starting.**

---

## MANDATORY SAFETY RULES

### 1. Zero Regression Policy
- **Do NOT change** existing UI, layouts, spacing, colors, typography, animations, or responsive behavior unless explicitly instructed.
- **Do NOT redesign** pages, move components, reorder sections, replace icons, or change navigation/sidebar/dashboard.
- All existing forms, AJAX requests, routes, controllers, helpers, JS, CSS, and DB queries must continue working exactly as before.

### 2. Before Editing ANY File — Full Pre-Check
- **Read the ENTIRE file**, not just the part to change.
- **Search the whole project** for all controllers, views, helpers, JS, CSS, AJAX endpoints, partials, and shared components that depend on it.
- **Check for duplicate implementations** before editing.

### 3. Minimal Change Principle
- Change only the lines that need changing — never rewrite entire functions, controllers, views, or CSS blocks.
- Prefer additive changes (new classes, new wrappers, optional params) over modifying existing code.

### 4. CSS Safety
- Never delete CSS without confirming it's unused.
- Never rename classes globally or use broad selectors.
- Never remove `!important` unless verified safe on every affected page.
- Use scoped/component-specific selectors only.

### 5. JavaScript Safety
- Check event listeners, delegated events, AJAX endpoints, shared utilities, init order before editing JS.
- Never duplicate event listeners or initialize components twice.

### 6. Shared Component Safety
- A change to a shared partial/layout breaks EVERY page that includes it. Verify ALL affected pages before editing shared code.

### 7. Security Fixes Must Be Invisible
- Fix security issues without changing user experience, workflows, or UI appearance.

### 8. Implement Incrementally
- 1-3 files per batch. Verify each batch works before proceeding. Stop immediately if regression detected.

### 9. After EVERY Change — Run Verification
- No PHP errors, no JS errors, no console warnings, no CSS conflicts, no broken routes/forms/AJAX/tables/modals/responsive behavior, no visual regressions.

### 10. If Unsure — STOP
- Do NOT guess. Search thoroughly. Choose the least invasive solution.

---

**The success criterion: the entire application works exactly as before, with zero unintended regressions, while the requested improvement is achieved.**