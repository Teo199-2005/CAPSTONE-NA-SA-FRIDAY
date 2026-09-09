# Frontend & Mobile Responsiveness Audit Prompt

## Instructions
Use this comprehensive prompt to audit the entire web application. Rate each category from 1-100 and provide detailed suggestions for improvement.

---

## APPLICATION OVERVIEW
Application Type: Based on CodeIgniter PHP framework
Current Working Directory: c:\Users\Cocotantan\Downloads\public_html (3)\public_html (4)\public_html
Main Entry Points: public/index.php, app/Views/*.php

---

## AUDIT CATEGORIES & RATING FRAMEWORK

### CATEGORY 1: FRONTEND DESIGN & UI/UX
**Rating: ___/100**

Audit Items:
- [ ] Visual design quality and aesthetics
- [ ] Color scheme and typography consistency
- [ ] Layout organization and spacing
- [ ] Component reusability
- [ ] Design system consistency across views
- [ ] User flow intuitiveness

**Target Files to Review:**
- app/Views/*.php (all view files)
- public/css/auth-mobile.css
- public/css/app.css
- public/css/admin-table-enhancements.css
- app/Views/layout.php
- app/Views/dashboard_layout.php
- app/Views/landing.php

**Questions to Answer:**
1. Is the design visually appealing and professional?
2. Are there consistent design patterns across all pages?
3. Is the UI intuitive for users (students, teachers, admins)?
4. Are there any visual inconsistencies or design debt?

---

### CATEGORY 2: MOBILE RESPONSIVENESS - VIEWPORT & LAYOUT
**Rating: ___/100**

Audit Items:
- [ ] Proper viewport meta tags implementation
- [ ] Responsive viewport units (vw, vh, vmin, vmax)
- [ ] Flexible grid systems
- [ ] Mobile-first CSS approach
- [ ] Breakpoint consistency (mobile: 320-768px, tablet: 768-1024px, desktop: 1024px+)
- [ ] Device rotation handling
- [ ] Responsive images and media

**Target Files to Review:**
- app/Views/layout.php (meta tags)
- public/css/*.css (media queries)
- All view files for hardcoded widths

**Specific Tests Required:**
```javascript
// Test Viewport
- Check: <meta name="viewport" content="width=device-width, initial-scale=1.0">
- Test: Rotate device on mobile, verify layout adapts
- Test: Resize browser window from 320px to 1920px
- Test: Check for horizontal scroll on mobile

// Check for Common Issues:
- [ ] No fixed-width containers (≥1200px) that break mobile
- [ ] No absolute positioning without mobile fallbacks
- [ ] No min-width breaking mobile browsers
- [ ] Touch targets ≥ 48x48px (Apple HIG) or ≥ 44x44px (Material Design)
```

**Questions to Answer:**
1. Do all pages render correctly on 320px width?
2. Are there any horizontal scroll issues?
3. Do touch targets meet accessibility standards?
4. How many breakpoints are used, and are they consistent?

---

### CATEGORY 3: MOBILE RESPONSIVENESS - NAVIGATION
**Rating: ___/100**

Audit Items:
- [ ] Hamburger menu implementation
- [ ] Bottom navigation bar (for mobile apps)
- [ ] Dropdown menus work on touch devices
- [ ] Breadcrumb navigation simplified for mobile
- [ ] Tab bar for main sections
- [ ] Swipe gestures support

**Target Files to Review:**
- app/Views/*_layout.php (navigation components)
- app/Helpers/portal_nav_helper.php
- All dashboard views
- app/Views/teacher/* and app/Views/student/*

**Specific Tests Required:**
- [ ] Test hamburger menu opens and closes on mobile
- [ ] Test dropdown menus work with touch (hover vs click)
- [ ] Test navigation items are reachable on small screens
- [ ] Test quick links are accessible
- [ ] Verify active page highlighting on mobile nav

**Questions to Answer:**
1. Is there a mobile-specific navigation pattern?
2. Can users reach all main features on mobile?
3. Are navigation items properly sized for touch?
4. Is breadcrumb navigation obtrusive on mobile?

---

### CATEGORY 4: MOBILE RESPONSIVENESS - FORMS & INPUTS
**Rating: ___/100**

Audit Items:
- [ ] Form fields size and spacing
- [ ] Input type optimizations (email, number, tel)
- [ ] Keyboard type triggers correctly
- [ ] Label positioning (floating labels vs top labels)
- [ ] Form validation messages visible on mobile
- [ ] Date/time pickers mobile-native
- [ ] Select dropdowns mobile-friendly
- [ ] Multi-step forms adapt to mobile

**Target Files to Review:**
- app/Views/auth/*.php (login, register forms)
- app/Views/auth/forgot_password.php
- app/Views/auth/reset_password*.php
- app/Views/teacher/sned_grade_entry.php
- app/Views/teacher/students.php
- app/Views/admin/*.php (settings, users, etc.)
- Any view with forms

**Specific Tests Required:**
```javascript
// Form Fields:
- Test: Input fields expand when focused on mobile
- Test: Zoom doesn't occur on input focus (font-size ≥16px)
- Test: Labels don't overlap with values
- Test: Error messages are readable and not cut off
- Test: Submit button easily accessible

// Native Inputs:
- Test: Email input triggers email keyboard
- Test: Phone input triggers numeric keyboard
- Test: Date inputs use native date picker
- Test: Select elements work on iOS Safari & Android Chrome
```

**Questions to Answer:**
1. Are all form inputs at least 44px tall?
2. Do input fields prevent accidental zoom (16px font minimum)?
3. Are validation errors clearly visible on small screens?
4. Is tab order logical on mobile?

---

### CATEGORY 5: MOBILE RESPONSIVENESS - DATA TABLES
**Rating: ___/100**

Audit Items:
- [ ] Card view transformation for tables
- [ ] Horizontal scroll with sticky first column
- [ ] Column priority (hide less important columns on mobile)
- [ ] Sorting and filtering mobile-friendly
- [ ] Row actions properly spaced
- [ ] Bulk actions simplified for mobile

**Target Files to Review:**
- app/Views/admin/students.php
- app/Views/admin/teachers.php
- app/Views/admin/users.php
- app/Views/teacher/students.php
- app/Views/teacher/sections.php
- public/js/admin-table-enhancements.js
- public/css/admin-table-enhancements.css

**Specific Tests Required:**
- [ ] Test tables on 320px screen width
- [ ] Test card view toggle functionality
- [ ] Test column visibility on different breakpoints
- [ ] Test sorting controls accessibility
- [ ] Test filter inputs on mobile
- [ ] Test row action buttons

**Questions to Answer:**
1. Do tables transform to card view on mobile?
2. Can users access all columns on mobile devices?
3. Are table actions reachable without horizontal scrolling?
4. Is data still readable on small screens?

---

### CATEGORY 6: MOBILE RESPONSIVENESS - TYPOGRAPHY & TEXT
**Rating: ___/100**

Audit Items:
- [ ] Relative font sizing (em, rem, vw)
- [ ] Minimum font size for readability (≥14px)
- [ ] Line height adequate for mobile (≥1.5)
- [ ] Heading hierarchy maintained
- [ ] Text doesn't overflow containers
- [ ] Long URLs/text break correctly
- [ ] Font loading performance

**Target Files to Review:**
- public/css/*.css (font-size declarations)
- HTML in all view files
- Content in dashboard and report card views

**Specific Tests Required:**
```javascript
- Test: Text at 320px width doesn't overflow
- Test: Increase browser zoom to 200%, verify layout
- Test: Font sizes scale appropriately
- Test: Line length stays within 45-75 characters
- Test: Text is readable without zooming
```

**Questions to Answer:**
1. Is base font size ≥14px?
2. Do headings scale proportionally?
3. Is there adequate contrast (WCAG AA: 4.5:1 minimum)?
4. Does text reflow properly?

---

### CATEGORY 7: MOBILE RESPONSIVENESS - IMAGES & MEDIA
**Rating: ___/100**

Audit Items:
- [ ] Responsive images (max-width: 100%)
- [ ] Image optimization (WebP, compressed files)
- [ ] Lazy loading implementation
- [ ] Proper alt text
- [ ] Video responsiveness
- [ ] Icon fonts loaded efficiently
- [ ] No broken images on mobile

**Target Files to Review:**
- public/uploads/ (verify user uploads)
- app/Views/landing.php and landing_preview.php
- app/Views/partials/landing_hero.php
- Any view with images

**Specific Tests Required:**
```bash
# Image Analysis:
- Check image sizes in public/ folder
- Verify all images have alt attributes
- Test images in slow 3G connection
- Check for layout shifts when images load
- Test gallery/carousel components on touch
```

**Questions to Answer:**
1. Are images optimized for mobile bandwidth?
2. Do images scale proportionally?
3. Are there any layout shifts caused by images?
4. Is lazy loading implemented?

---

### CATEGORY 8: MOBILE RESPONSIVENESS - INTERACTIVE ELEMENTS
**Rating: ___/100**

Audit Items:
- [ ] Touch target size (minimum 44x44px)
- [ ] Button spacing
- [ ] Hover states converted to active states
- [ ] Touch feedback indicators
- [ ] Swipe functionality
- [ ] Pinch-to-zoom support (where appropriate)
- [ ] Long-press functionality
- [ ] Gesture support for common actions

**Target Files to Review:**
- All buttons in:
  - app/Views/auth/*.php
  - app/Views/dashboard*.php
  - app/Views/teacher/*.php
  - app/Views/student/*.php
  - app/Views/admin/*.php

**Specific Tests Required:**
```javascript
Touch Testing:
- Test: All buttons easily tappable without precision
- Test: Buttons don't overlap on small screens
- Test: Active states visible on touch
- Test: No accidental double-tap zoom
- Test: Success/error feedback visible
```

**Questions to Answer:**
1. Are all interactive elements ≥44x44px?
2. Are buttons spaced to prevent misfires?
3. Is there visual feedback on touch?
4. Are common gestures supported?

---

### CATEGORY 9: MOBILE RESPONSIVENESS - PERFORMANCE
**Rating: ___/100**

Audit Items:
- [ ] Page load time on mobile (3G: <3s)
- [ ] First Contentful Paint (FCP: <1.8s)
- [ ] Largest Contentful Paint (LCP: <2.5s)
- [ ] Cumulative Layout Shift (CLS: <0.1)
- [ ] First Input Delay (FID: <100ms)
- [ ] JavaScript bundle size (<200KB initial)
- [ ] CSS optimization (critical CSS, minified)
- [ ] Image optimization complete
- [ ] Font loading strategy

**Testing Methods:**
```bash
# Tools to Use:
1. Chrome DevTools Lighthouse (Mobile throttling)
2. WebPageTest.org (select mobile device)
3. Chrome DevTools Network tab (Slow 3G throttling)
4. PageSpeed Insights

# Metrics to Record:
- Performance Score: ___/100
- FCP: ___s
- LCP: ___s
- CLS: ___
- FID: ___ms
- TTI: ___s
- Total Page Size: ___KB
- Number of Requests: ___
```

**Questions to Answer:**
1. Does the site load in under 3 seconds on 3G?
2. Are there rendering-blocking resources?
3. Is code splitting implemented?
4. Are assets cached effectively?

---

### CATEGORY 10: MOBILE RESPONSIVENESS - CROSS-BROWSER COMPATIBILITY
**Rating: ___/100**

Test on Browsers:
- [ ] Chrome Mobile (Android) - Latest
- [ ] Safari Mobile (iOS) - Latest
- [ ] Samsung Internet (Android)
- [ ] Firefox Mobile (Android)
- [ ] Opera Mobile (Android)
- [ ] UC Browser (popular in Asia)

**Specific Tests Required:**
```javascript
Compatibility Testing:
- CSS Grid: Test fallbacks for older Safari
- Flexbox: Test gaps and wrapping
- CSS Variables: Provide fallbacks if used
- JavaScript ES6+: Polyfills for older browsers
- Touch Events: Both touch and mouse events supported
- Viewport: Test on various mobile resolutions
```

**Questions to Answer:**
1. Does the site work on iOS Safari (most restrictive)?
2. Are there browser-specific bugs?
3. Do CSS features have proper fallbacks?
4. Is JavaScript compatible with target browsers?

---

### CATEGORY 11: MOBILE RESPONSIVENESS - BREAKPOINT ANALYSIS
**Rating: ___/100**

Analyze each major breakpoint:

**Breakpoint 1: Mobile Small (320px - 480px)**
- Landing page: WORKS / BROKEN / NOT TESTED
- Login/Register: WORKS / BROKEN / NOT TESTED
- Dashboard: WORKS / BROKEN / NOT TESTED
- Grade Entry: WORKS / BROKEN / NOT TESTED
- Table Views: WORKS / BROKEN / NOT TESTED
- Forms: WORKS / BROKEN / NOT TESTED

**Breakpoint 2: Mobile Large (481px - 768px)**
- Landing page: WORKS / BROKEN / NOT TESTED
- Login/Register: WORKS / BROKEN / NOT TESTED
- Dashboard: WORKS / BROKEN / NOT TESTED
- Grade Entry: WORKS / BROKEN / NOT TESTED
- Table Views: WORKS / BROKEN / NOT TESTED
- Forms: WORKS / BROKEN / NOT TESTED

**Breakpoint 3: Tablet (769px - 1024px)**
- Landing page: WORKS / BROKEN / NOT TESTED
- Login/Register: WORKS / BROKEN / NOT TESTED
- Dashboard: WORKS / BROKEN / NOT TESTED
- Grade Entry: WORKS / BROKEN / NOT TESTED
- Table Views: WORKS / BROKEN / NOT TESTED
- Forms: WORKS / BROKEN / NOT TESTED

**Breakpoint 4: Desktop (1025px+)**
- Status: FULLY OPTIMIZED / RESPONSIVE / STATIC

**Questions to Answer:**
1. At which breakpoint does each major feature break?
2. Are there missing breakpoints between mobile and desktop?
3. Do all features work at all tested breakpoints?
4. Which features need mobile-specific implementations?

---

### CATEGORY 12: FEATURE COMPLETENESS & WORKING STATUS
**Rating: ___/100**

Create a comprehensive inventory:

**Landing Page Features:**
- Hero section: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Feature showcase: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- About section: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Contact form: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Image carousel: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED

**Authentication Features:**
- Login: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Registration: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Forgot Password: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Reset Password: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Pending Approval: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED

**Student Features:**
- Dashboard: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Profile: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Analytics: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Grades View: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED

**Teacher Features:**
- Dashboard: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Grade Entry (SNED): WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Report Cards: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Student Management: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Section Management: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Profile: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED

**Admin Features:**
- Dashboard: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Student Management: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Teacher Management: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Section Management: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Announcements: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Settings: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED
- Landing Page Editor: WORKS / PARTIAL / BROKEN / NOT MOBILE / NOT IMPLEMENTED

**Not Yet Implemented Features:**
- [ ] List features that are planned but not built
- [ ] List mobile-specific features that are planned

**Questions to Answer:**
1. What percentage of features work on mobile?
2. Which features are desktop-only?
3. What features are not yet implemented at all?
4. What critical features are missing for mobile users?

---

### CATEGORY 13: ACCESSIBILITY (a11y)
**Rating: ___/100**

Audit Items:
- [ ] Semantic HTML (proper heading hierarchy)
- [ ] ARIA labels for interactive elements
- [ ] Alt text for all images
- [ ] Form labels properly associated
- [ ] Color contrast (WCAG AA: 4.5:1)
- [ ] Keyboard navigation support
- [ ] Screen reader compatibility
- [ ] Focus indicators visible
- [ ] Skip navigation links
- [ ] Form error announcements

**Testing Methods:**
```javascript
# Automated Testing:
- Use axe DevTools browser extension
- Run Lighthouse Accessibility audit
- Use WAVE browser extension

# Manual Testing:
- Test keyboard navigation (Tab, Shift+Tab, Enter, Escape)
- Test with screen reader (NVDA, JAWS, VoiceOver)
- Test with zoom at 200%
- Test with grayscale mode
```

**Questions to Answer:**
1. Does the site pass WCAG 2.1 AA standards?
2. Can users navigate without a mouse?
3. Are color-blind users accommodated?
4. Is content readable when zoomed to 200%?

---

### CATEGORY 14: CODE QUALITY & MAINTAINABILITY
**Rating: ___/100**

Audit Items:
- [ ] CSS Organization (BEM, SMACSS, or similar)
- [ ] CSS specificities (no !important overuse)
- [ ] Duplicate code elimination
- [ ] Commented sections where complex
- [ ] Consistent naming conventions
- [ ] Unused CSS/JS removal
- [ ] Responsive images (srcset, sizes)
- [ ] Proper use of CSS custom properties

**Target Files to Review:**
- public/css/*.css
- public/js/*.js
- app/Views/layout.php

**Questions to Answer:**
1. Is CSS well-organized and maintainable?
2. Are there any !important declarations overused?
3. Is there dead code (unused CSS/JS)?
4. Are naming conventions consistent?

---

### CATEGORY 15: SECURITY (Frontend)
**Rating: ___/100**

Audit Items:
- [ ] XSS Prevention (output escaping)
- [ ] CSRF tokens on forms
- [ ] Secure headers (CSP, X-Frame-Options)
- [ ] HTTPS enforcement
- [ ] Input sanitization
- [ ] No sensitive data in localStorage
- [ ] Secure cookie settings
- [ ] Password field autocomplete disabled

**Target Files to Review:**
- app/Views/layout.php (headers)
- All forms for CSRF tokens
- JavaScript for localStorage usage

**Questions to Answer:**
1. Are user inputs properly escaped?
2. Are CSRF tokens present on all forms?
3. Is HTTPS enforced?
4. Are there any security headers missing?

---

## MOBILE-SPECIFIC BUGS & ISSUES

### Critical Issues (Must Fix)
1. [ ] Horizontal scroll on page: ___
2. [ ] Text overflow/clipping: ___
3. [ ] Touch targets too small: ___
4. [ ] Forms broken on mobile: ___
5. [ ] Navigation inaccessible: ___
6. [ ] Images not loading: ___
7. [ ] JavaScript errors on mobile: ___
8. [ ] Performance unacceptable: ___

### Medium Priority Issues
1. [ ] Layout shifts on scroll: ___
2. [ ] Dropdown menus not working: ___
3. [ ] Date pickers broken: ___
4. [ ] Modal dialogs off-screen: ___
5. [ ] Tables unreadable: ___
6. [ ] Charts/graphs broken: ___
7. [ ] File upload issues: ___
8. [ ] Form validation unclear: ___

### Low Priority Issues
1. [ ] Minor spacing issues: ___
2. [ ] Non-critical animations: ___
3. [ ] Edge case scenarios: ___
4. [ ] Visual polish: ___

---

## IMPLEMENTATION ROADMAP

### Phase 1: Critical Fixes (Week 1-2)
**Must Fix Before Launch:**
1. Fix horizontal scroll issues: Estimated effort ___
2. Ensure touch targets ≥44px: Estimated effort ___
3. Fix broken forms on mobile: Estimated effort ___
4. Fix navigation accessibility: Estimated effort ___
5. Implement responsive images: Estimated effort ___

### Phase 2: Responsiveness Improvements (Week 3-4)
**Mobile Optimization:**
1. Implement card view for tables: Estimated effort ___
2. Optimize typography for mobile: Estimated effort ___
3. Mobile-specific navigation: Estimated effort ___
4. Form field optimizations: Estimated effort ___
5. Performance optimizations: Estimated effort ___

### Phase 3: Enhanced Mobile Features (Week 5-6)
**Better Mobile UX:**
1. Add bottom navigation bar: Estimated effort ___
2. Implement swipe gestures: Estimated effort ___
3. Add pull-to-refresh: Estimated effort ___
4. PWA features (offline support): Estimated effort ___
5. Mobile-specific features: Estimated effort ___

### Phase 4: Testing & Polish (Week 7-8)
**Quality Assurance:**
1. Cross-browser testing: Estimated effort ___
2. Device testing (real devices): Estimated effort ___
3. Accessibility audit: Estimated effort ___
4. Performance benchmarking: Estimated effort ___
5. User testing (if available): Estimated effort ___

---

## FINAL SCORECARD

| Category | Score (1-100) | Weight | Weighted Score |
|----------|----------------|--------|----------------|
| Design & UI/UX | ___/100 | 15% | ___ |
| Mobile Viewport & Layout | ___/100 | 15% | ___ |
| Mobile Navigation | ___/100 | 10% | ___ |
| Mobile Forms & Inputs | ___/100 | 10% | ___ |
| Mobile Data Tables | ___/100 | 10% | ___ |
| Mobile Typography | ___/100 | 5% | ___ |
| Mobile Images & Media | ___/100 | 5% | ___ |
| Mobile Interactive Elements | ___/100 | 10% | ___ |
| Mobile Performance | ___/100 | 10% | ___ |
| Browser Compatibility | ___/100 | 5% | ___ |
| Feature Completeness | ___/100 | 5% | ___ |
| Accessibility | ___/100 | 5% | ___ |
| Code Quality | ___/100 | 3% | ___ |
| Security (Frontend) | ___/100 | 2% | ___ |
| **TOTAL** | | 100% | **___/100** |

---

## OVERALL ASSESSMENT

### Grade: ___/100
- 90-100: Excellent - Production Ready
- 80-89: Good - Minor improvements needed
- 70-79: Fair - Significant improvements required
- 60-69: Poor - Major rework needed
- <60: Critical - Complete redesign recommended

### Executive Summary
[Provide 2-3 paragraph summary of overall state, critical issues, and recommended actions]

### Priority Recommendations
1. [Most critical fix]
2. [Second most critical fix]
3. [Third most critical fix]

### Mobile-Launch Readiness
- Ready: ___/100
- Estimated time to mobile-ready: [X weeks]

---

## APPENDIX: DETAILED FINDINGS

### Specific Page-by-Page Analysis
[Attach detailed findings for each major page]

### Device-Specific Issues
[Note issues on specific devices if tested]

### Browser-Specific Issues
[Note issues in specific browsers if tested]

---

## NOTES
- All ratings should be objective based on WCAG 2.1, Material Design, and Apple HIG guidelines
- "WORKS" means fully functional on mobile devices
- "PARTIAL" means partially working or has significant UX issues
- "BROKEN" means non-functional on mobile
- "NOT MOBILE" means not tested or not applicable
- "NOT IMPLEMENTED" means feature doesn't exist yet

---

## USAGE INSTRUCTIONS
1. Complete each category systematically
2. Test on actual mobile devices when possible
3. Use browser DevTools mobile emulation as baseline
4. Document all findings with screenshots
5. Prioritize fixes by user impact
6. Re-audit after implementing fixes