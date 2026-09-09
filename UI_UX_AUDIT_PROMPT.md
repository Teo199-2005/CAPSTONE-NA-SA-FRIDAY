# COMPREHENSIVE UI/UX AUDIT PROMPT

Use this prompt to conduct a thorough UI/UX audit of the CSCS SMS (School Management System) application.

---

## ROLE & CONTEXT

You are a senior UX/UI designer and frontend architect conducting a comprehensive audit of a school management system built with CodeIgniter 4 + PHP. The system serves three user roles: Administrators, Teachers, and Students.

Audit all views in these directories:
- `app/Views/admin/` - Administrator dashboard and management pages
- `app/Views/teacher/` - Teacher dashboard, grade entry, attendance, reports
- `app/Views/student/` - Student dashboard, grades, schedule, profile
- `app/Views/auth/` - Authentication pages (login, register, forgot password)
- `app/Views/layout.php` & `app/Views/dashboard_layout.php` - Master templates
- `public/css/` & `public/js/` - Stylesheets and scripts

---

## AUDIT FRAMEWORK

### 1. VISUAL DESIGN & BRANDING (Score: /100)

**Consistency:**
- Are colors, fonts, spacing, and typography consistent across all pages?
- Does the design system follow a coherent pattern (buttons, cards, forms, tables)?
- Are Bootstrap/Tailwind classes used consistently or haphazardly?

**Aesthetics:**
- Is the visual hierarchy clear? Can users identify primary vs secondary actions?
- Are cards, shadows, and borders used consistently?
- Does the design feel modern and professional, or dated?

**Branding:**
- Is the school logo/branding prominently and consistently displayed?
- Are color schemes appropriate for an educational institution?
- Is there visual cohesion between public-facing pages and authenticated dashboards?

**Iconography:**
- Are icons from a single library (Bootstrap Icons, Font Awesome, etc.)?
- Do icons accurately represent their functions?
- Are icons sized consistently and given proper spacing?

**Red Flags:**
- Inline styles overriding stylesheets (`style="color: #000 !important"`)
- Mixed CSS frameworks or custom CSS fighting with libraries
- Inconsistent button styles (primary, secondary, success, danger used incorrectly)

---

### 2. USER EXPERIENCE FLOWS (Score: /100)

**Task Completion:**
- Map the primary tasks for each role:
  - **Admin**: Create user → Assign section → Generate report
  - **Teacher**: Take attendance → Enter grades → Generate report card
  - **Student**: View grades → Check schedule → Download report card
- Can users complete these tasks in < 3 clicks where possible?
- Are there dead ends or confusing navigation paths?

**Cognitive Load:**
- Are dashboard pages information-dense without hierarchy?
- Do forms show too many fields at once? Could they use progressive disclosure?
- Are there walls of text without visual breaks (cards, dividers, spacing)?

**Error Prevention:**
- Are destructive actions (delete, remove) confirmed with modals?
- Are forms validated inline before submission?
- Can users undo actions or recover from mistakes?

**Feedback:**
- Do buttons show loading states during async operations?
- Are success/error messages visible and actionable?
- Do empty states provide helpful guidance (not just "No data")?

**Red Flags:**
- Forms with 20+ fields on one page
- No confirmation dialogs for bulk delete operations
- Generic error messages ("Error 500", "Something went wrong")
- Success messages that disappear too quickly
- No indication of save status (unsaved changes warning)

---

### 3. NAVIGATION & INFORMATION ARCHITECTURE (Score: /100)

**Primary Navigation:**
- Is the main nav (sidebar, topbar) consistent across all dashboards?
- Can users understand where they are? (Breadcrumbs, active states)
- Are nested menu items clearly indented/hierarchical?

**Secondary Navigation:**
- Are tabs, filters, and sub-navs visually distinct?
- Do pagination controls look clickable and show current page?
- Are search bars prominent where needed?

**URL & Routing:**
- Are URLs semantic? (`/teacher/grades` vs `/index.php?p=grades`)
- Do back buttons work as expected?
- Is there a logical page hierarchy?

**Mobile Navigation:**
- Does the hamburger menu work on mobile?
- Are nav items large enough to tap (44px minimum)?
- Is there a skip-to-content link for accessibility?

**Red Flags:**
- Sidebar navigation that doesn't collapse on mobile
- Active page indicator missing or subtle
- Deep nesting without breadcrumbs
- "Back" button not available or broken

---

### 4. FORMS & DATA ENTRY (Score: /100)

**Input Design:**
- Are form labels visible above inputs (not placeholders as labels)?
- Do disabled/read-only fields look distinct from editable ones?
- Are required fields marked with asterisks AND red borders?
- Do date pickers, select2, and rich text editors work smoothly?

**Validation:**
- Are validation errors shown NEXT to the field, not just at the top?
- Do error messages explain HOW to fix the problem?
- Are success states visible (green borders, checkmarks)?

**Data Density:**
- Are grade tables, student lists, and reports scannable?
- Do tables have alternating row colors or clear borders?
- Are sticky headers used for long tables?

**Actions:**
- Are primary actions (Save, Submit, Approve) visually prominent?
- Are secondary actions (Cancel, Back) de-emphasized but still visible?
- Are destructive actions (Delete, Reject) colored differently (red)?

**Red Flags:**
- Placeholder text used as the only label
- No inline validation (errors only after submit)
- Tiny input fields for long data (addresses, names)
- Dropdowns without search functionality for 100+ options
- No "select all" checkbox for bulk operations

---

### 5. RESPONSIVE DESIGN (Score: /100)

**Breakpoints:**
- Test at: 320px (iPhone SE), 768px (iPad), 1024px (iPad Pro), 1440px (Desktop)
- Do layouts reflow gracefully or break horribly?
- Are horizontal scrollbars eliminated on mobile?

**Touch Targets:**
- Are all buttons minimum 44x44px on mobile?
- Do table rows have adequate padding?
- Are form inputs not too small to tap accurately?

**Typography:**
- Is body text minimum 16px on mobile to prevent zoom-on-focus?
- Do headings scale down appropriately?
- Is text ever truncated or wrapping awkwardly?

**Tables on Mobile:**
- Do wide tables transform into cards/list view on mobile?
- Are columns hidden or reordered for small screens?
- Can users scroll horizontally if needed, with clear affordance?

**Red Flags:**
- Fixed-width containers breaking mobile layouts
- Text smaller than 14px on mobile
- Buttons too close together (accidental taps)
- Data tables requiring horizontal scroll on mobile without visual cue

---

### 6. ACCESSIBILITY (AUDIT FOR WCAG 2.1 AA) (Score: /100)

**Keyboard Navigation:**
- Can users tab through all interactive elements in logical order?
- Are focus indicators visible and high-contrast?
- Can modals be closed with Escape key?
- Do dropdowns and menus work with arrow keys?

**Screen Readers:**
- Do all images have meaningful alt text?
- Are form inputs associated with labels (`for` attribute)?
- Do ARIA labels exist for icon-only buttons?
- Is the page structure semantic (`<main>`, `<nav>`, `<header>`)?

**Color Contrast:**
- Is text-to-background contrast at least 4.5:1 for normal text?
- Are interactive elements (links, buttons) distinguishable without color alone?
- Do error states use both color AND icons/text?

**Forms:**
- Are error messages linked to inputs via `aria-describedby`?
- Do autocomplete attributes exist where appropriate?
- Are required fields indicated programmatically (`aria-required`)?

**Red Flags:**
- Focus indicators removed via CSS (`outline: none`)
- Color-only differentiation (red/green without icons)
- Missing alt text on functional images
- Poor contrast ratios (light gray on white)
- No skip navigation link

---

### 7. DATA VISUALIZATION (Score: /100)

**Charts & Graphs:**
- Are charts readable with clear labels, legends, and units?
- Do color choices account for color-blind users?
- Are axes labeled with units and scales?
- Do pie charts show percentages AND values?

**Tables:**
- Are totals/averages clearly marked?
- Do sortable columns have sort indicators (arrows)?
- Are filtered/searched results highlighted?

**Report Cards (PDFs):**
- Is the PDF layout clean and printable?
- Are grades easy to scan with proper alignment?
- Is there a digital signature or authentication mark?

**Red Flags:**
- Charts without axis labels
- Rainbow color palettes (hard to distinguish for color-blind users)
- Tiny fonts in data tables
- No data source attribution or "last updated" timestamps

---

### 8. PERFORMANCE PERCEPTION (Score: /100)

**Loading States:**
- Are skeleton loaders used for data tables?
- Do buttons show spinners during form submission?
- Is there a progress indicator for file uploads?

**Optimistic UI:**
- Do delete actions remove items immediately with undo option?
- Do form saves show "Saving..." then "Saved"?
- Do filter changes apply instantly or require "Apply" button?

**Pagination:**
- Are large datasets paginated (not loaded all at once)?
- Does pagination show total count? ("Showing 1-20 of 156")
- Are there "Load More" buttons for infinite scroll scenarios?

**Red Flags:**
- Full page reloads for every filter change
- No skeleton/shimmer loading states
- Spinners missing on async operations
- Users unsure if their action completed

---

### 9. EMPTY & ERROR STATES (Score: /100)

**Empty States:**
- Do empty states have helpful icons/illustrations?
- Do they explain WHY the state is empty and HOW to fix it?
- Do they include a clear call-to-action button?
- Examples: "No students found. [Add Student] or [Import CSV]"

**Error States:**
- Are 404/403/500 pages branded and helpful?
- Do they provide navigation back to safety?
- Is there a way to report errors or retry?

**Success States:**
- Are success messages prominent but not jarring?
- Do they include next steps? ("Student created. [Add another] or [View all students]")

**Red Flags:**
- Empty states with no guidance ("No data")
- Generic error pages with no branding or helpful links
- Success messages that auto-dismiss too quickly
- No distinction between "no results" and "system error"

---

### 10. MICROINTERACTIONS & POLISH (Score: /100)

**Transitions:**
- Do hover states feel responsive (0.2s transitions)?
- Are dropdown menus smooth?
- Do modals fade in/out gracefully?

**Feedback:**
- Do checkboxes/radios have clear checked states?
- Do input borders change color on focus?
- Are there ripple effects or visual feedback on button clicks?

**Animations:**
- Are animations subtle and purposeful (not distracting)?
- Do page transitions feel snappy?
- Are loading skeletons engaging?

**Red Flags:**
- No hover states on interactive elements
- Jarring, slow animations (> 0.5s)
- Missing focus rings on inputs
- Buttons that look identical to static cards

---

## DELIVERABLE FORMAT

For each category above, provide:

### 1. FINDINGS
List specific issues with:
- **Location**: File path + component description
- **Issue**: Clear description of the problem
- **Severity**: Critical / High / Medium / Low
- **Impact**: How it affects users (frustration, confusion, abandonment)

### 2. SCREENSHOTS (if available)
Note what visual problems exist (e.g., "Mobile view: table overflows container, horizontal scroll required")

### 3. RECOMMENDATIONS
For each issue, suggest:
- **Quick Fix**: Immediate improvement (e.g., "Add `esc()` to line 45")
- **Long-term Solution**: Architectural improvement (e.g., "Create reusable table component with responsive card view")
- **Priority**: P0 (blocking), P1 (high), P2 (medium), P3 (low)

### 4. POSITIVE HIGHLIGHTS
Call out what works WELL:
- "The dashboard cards have excellent visual hierarchy"
- "Grade table is scannable with alternating row colors"
- "Mobile responsive navigation works flawlessly"

### 5. PRIORITIZED ACTION ITEMS
Group fixes into:
- **CRITICAL (Do Now)**: Blocks users, data loss, security
- **HIGH (This Sprint)**: Major UX friction, accessibility blockers
- **MEDIUM (Next Sprint)**: Consistency improvements, polish
- **LOW (Backlog)**: Nice-to-haves, minor refinements

---

## OUTPUT TEMPLATE

```markdown
# UI/UX AUDIT REPORT - CSCS SMS

**Auditor**: [AI/Designer Name]
**Date**: [Date]
**Overall Score**: X/100
**Priority**: P0 fixes: X | P1 fixes: X | P2 fixes: X | P3 fixes: X

---

## EXECUTIVE SUMMARY
(2-3 sentences on overall health of the UI/UX)

---

## 1. VISUAL DESIGN & BRANDING (Score: X/100)

### Issues Found
| Severity | Location | Issue | Impact |
|----------|----------|-------|--------|
| High | admin/dashboard.php:23 | Inline styles overriding CSS | Inconsistent appearance |

### Recommendations
- **Quick Fix**: Move inline styles to stylesheet
- **Long-term**: Create utility classes for color overrides
- **Priority**: P1

---

## 2. USER EXPERIENCE FLOWS (Score: X/100)
...

---

## PRIORITIZED ROADMAP

### P0 - CRITICAL (This Week)
1. Fix mobile table overflow (blocks data access)
2. Add form validation inline (prevents errors)
3. Fix missing CSRF on login form (security)

### P1 - HIGH (This Sprint)
1. Redesign grade entry table for mobile (usability)
2. Add breadcrumbs to all pages (navigation)
3. Improve empty states with CTAs (guidance)

### P2 - MEDIUM (Next Sprint)
1. Create reusable button/button CSS classes (consistency)
2. Add hover states to all interactive elements (polish)
3. Standardize spacing scale (harmonization)

### P3 - LOW (Backlog)
1. Add subtle page transition animations (delight)
2. Replace generic icons with branded set (identity)
3. A/B test dashboard card layouts (optimization)

---

## APPENDIX: POSITIVE FINDINGS
- Excellent use of Bootstrap Icons throughout
- Strong color contrast for primary buttons
- Responsive sidebar navigation works well
- Clear visual hierarchy on dashboard tiles