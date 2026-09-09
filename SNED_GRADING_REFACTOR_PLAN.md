# SNED & Grading System Refactoring Plan
## Section-Level Grading Type Selection

**Date:** June 23, 2026
**Status:** ✅ Planned / ❌ Not Started / 🚧 In Progress / ✅ Completed

---

## Overview

Currently the system hardcodes:
- **Grades 0-6 (K-6)** → Numerical grading (subjects with numerical scores)
- **Grade 7 (SNED)** → Non-numerical grading (categories with symbols P/AP/D/B/NO/NA)

**Goal:** When creating a section, the admin chooses the grading type:
- **Numerical** → Add Subjects (Subject Code + Subject Name) — current Grades 1-6 behavior
- **Non-Numerical** → Add Categories (Category Name + Description) + Custom Grading Symbols — current SNED behavior but customizable

This allows ANY grade level to use either grading system.

---

## PHASE 1: Database Changes

### 1.1 Add `grading_type` to `sections` table
- [ ] Create migration: `2026-06-23-000001_AddGradingTypeToSections.php`
- [ ] Add column: `grading_type ENUM('numerical', 'non_numerical') NOT NULL DEFAULT 'numerical'`
- [ ] Update existing Grade 7 (SNED) sections to `'non_numerical'`
- [ ] Update `SectionModel::$allowedFields` to include `grading_type`

### 1.2 Create `section_grading_symbols` table (for Non-Numerical sections)
- [ ] Create migration: `2026-06-23-000002_CreateSectionGradingSymbols.php`
- [ ] Columns:
  - `id` INT AUTO_INCREMENT PRIMARY KEY
  - `section_id` INT (FK to sections)
  - `symbol` VARCHAR(10) (e.g., "P", "AP", "D", etc.)
  - `label` VARCHAR(100) (e.g., "Proficient")
  - `description` TEXT (optional, meaning of the symbol)
  - `display_order` INT
  - `is_active` BOOLEAN DEFAULT TRUE
  - `created_at` / `updated_at`
- [ ] Default seed: P=Proficient, AP=Approaching Proficiency, D=Developing, B=Beginning, NO/NA=Not Observed/Not Applicable

### 1.3 Add `section_id` to existing SNED tables (or restructure)
**Option A (Simpler):** Rename/restructure existing `sned_categories` and `sned_category_fields` to support `section_id`
- [ ] Add `section_id` column to `sned_categories` (allowing NULL for backward compatibility)
- [ ] Create new tables that unify the concept:
  - `section_categories` → replaces `sned_categories` (adds section_id FK)
  - `section_category_fields` → replaces `sned_category_fields`
  - `section_grades` → replaces `sned_grades` (adds grading_type context)

**Option B (Recommended - cleaner):** Create unified grading tables:
- [ ] Migration: `2026-06-23-000003_CreateUnifiedGradingTables.php`
  - `section_categories` (id, section_id, name, description, display_order, is_active)
  - `section_category_fields` (id, category_id, field_name, display_order, is_active)
  - `section_grades` (id, section_id, field_id, student_id, teacher_id, school_year, quarter, grade_value TEXT, remarks TEXT)
- [ ] Migrate existing SNED data to new tables
- [ ] Keep `sned_grades` as legacy or drop after migration

### 1.4 Update `subjects` table (for Numerical sections)
- [ ] No changes needed — subjects already work per-section via `section_subjects` table
- [ ] Just ensure subject management is only shown for numerical sections

### 1.5 Update validation rules
- [ ] `SectionModel::validationRules`: add `grading_type` => `required|in_list[numerical,non_numerical]`

---

## PHASE 2: Backend / Controller Changes

### 2.1 Section Creation / Edit (`Admin\Dashboard.php`)
- [ ] Modify `sections()` method to pass grading type info
- [ ] Modify section creation modal/view to include "Grading Type" radio/select
- [ ] Modify section editing to allow changing grading type (with warning if grades exist)
- [ ] Add `createSection()` AJAX endpoint with grading_type field
- [ ] Add `editSection()` AJAX endpoint with grading_type field

### 2.2 Admin Section Management View
- [ ] Show grading type badge next to section name (e.g., "Numerical" or "Non-Numerical")
- [ ] Show different action buttons based on grading type:
  - **Numerical**: "Manage Subjects" (current behavior)
  - **Non-Numerical**: "Manage Categories" + "Manage Grading Symbols"
- [ ] Update the Subjects/Categories management modals to be context-aware

### 2.3 Non-Numerical: Category Management
- [ ] Reuse/refactor `Admin\SnedManagement.php` to work per-section
- [ ] New endpoints in `Admin\SnedManagement` (or new controller `Admin\SectionCategories`):
  - `getCategories($sectionId)` — get categories for a section
  - `addCategory()` — add category (name, description) for a section
  - `deleteCategory($categoryId)` — soft delete
  - `getFields($categoryId)` — get fields for a category
  - `addField()` — add field to category
  - `deleteField($fieldId)` — soft delete

### 2.4 Non-Numerical: Custom Grading Symbols Management
- [ ] New controller `Admin\GradingSymbols` (or methods in SnedManagement):
  - `getSymbols($sectionId)` — get custom symbols
  - `addSymbol()` — add symbol (symbol, label, description)
  - `editSymbol()` — edit symbol
  - `deleteSymbol($symbolId)` — soft delete
  - `reorderSymbols()` — reorder

### 2.5 Teacher Grading Entry (`Teacher\SnedGrades` → refactor to `Teacher\SectionGrades`)
- [ ] Refactor `SnedGrades.php` to `SectionGrades.php` (or generalize)
- [ ] Detect grading type from section and load appropriate interface:
  - **Non-Numerical**: current SNED-style dropdown with symbols
  - **Numerical**: current numerical grade input
- [ ] `gradeEntry($sectionId, $categoryOrSubjectId)` — load based on grading type
- [ ] `saveGrade()` — save with grading type context
- [ ] Load custom symbols from `section_grading_symbols` table

### 2.6 Teacher Dashboard / Index
- [ ] Refactor `teacher/sned_index.php` to show both grading types
- [ ] For numerical sections: show subjects list with grade entry links
- [ ] For non-numerical sections: show categories list with grade entry links

### 2.7 Report Card Generation
- [ ] Refactor `reportCard()` and `reportCardPdf()` methods
- [ ] Detect grading type from section:
  - **Numerical**: use existing subjects-based report card
  - **Non-Numerical**: use SNED-style categories-based report card with custom symbols
- [ ] PDF generation: pass dynamic symbols/legend instead of hardcoded P/AP/D/B/NO/NA

---

## PHASE 3: View / UI Changes

### 3.1 Section Creation Modal (`admin/sections.php` JavaScript)
- [ ] Add "Grading Type" field to create section form
- [ ] Radio buttons: "Numerical (Subjects with numerical scores)" vs "Non-Numerical (Categories with symbols)"
- [ ] Show/hide relevant helper text based on selection

### 3.2 Section List View (`admin/sections.php`)
- [ ] Add grading type badge column
- [ ] Contextual action buttons based on grading type

### 3.3 Category Management UI (Admin)
- [ ] Create view: `admin/section_categories.php`
- [ ] List categories with fields count
- [ ] Add/edit/delete categories (name + description)
- [ ] Expand to show fields per category
- [ ] Add/edit/delete fields per category

### 3.4 Grading Symbols Management UI (Admin)
- [ ] Create view: `admin/section_grading_symbols.php`
- [ ] Table of symbols with: Symbol, Label, Description, Display Order
- [ ] Add/edit/delete symbols
- [ ] Drag to reorder (optional)

### 3.5 Teacher Grade Entry (Refactored)
- [ ] Refactor `sned_grade_entry.php` to generic `section_grade_entry.php`
- [ ] For non-numerical: show dropdown with dynamic symbols from `section_grading_symbols`
- [ ] For numerical: show numerical score input with auto-computation

### 3.6 Report Card Views (Refactored)
- [ ] Refactor `sned_report_card.php` to generic `section_report_card.php`
- [ ] Refactor `sned_report_card_pdf.php` to generic `section_report_card_pdf.php`
- [ ] Use dynamic legend from section's grading symbols

---

## PHASE 4: Helper / Model Changes

### 4.1 `grade_level_helper.php`
- [ ] Update `is_sned_grade()` to be deprecated — grading type now per-section
- [ ] Add helper: `get_section_grading_type($sectionId)` → 'numerical' | 'non_numerical'

### 4.2 `sned_helper.php`
- [ ] Deprecate or update to work per-section instead of hardcoded grade 7
- [ ] Add helper: `get_section_categories($sectionId)` 
- [ ] Add helper: `get_section_grading_symbols($sectionId)`

### 4.3 New Models
- [ ] Create `SectionCategoryModel` (replaces `SnedCategoryModel`)
- [ ] Create `SectionCategoryFieldModel` (replaces `SnedCategoryFieldModel`)
- [ ] Create `SectionGradeModel` (replaces `SnedGradeModel`)
- [ ] Create `SectionGradingSymbolModel`
- [ ] Update `SectionModel` with grading type methods

### 4.4 Routes (`Routes.php`)
- [ ] Update SNED routes to be section-aware
- [ ] Add routes for category management per section
- [ ] Add routes for grading symbols management
- [ ] Keep backward compatibility routes

---

## PHASE 5: Data Migration

### 5.1 Existing Data Migration
- [ ] Create migration script to:
  - Set `grading_type = 'non_numerical'` for all existing grade 7 sections
  - Set `grading_type = 'numerical'` for all existing grades 0-6 sections
  - Migrate SNED categories/fields/grades to new unified tables with section_id
  - Create default grading symbols for existing non-numerical sections (P/AP/D/B/NO/NA)

### 5.2 Seeder Updates
- [ ] Update `SnedDemoSeeder.php` to use new tables
- [ ] Update demo to show both grading types
- [ ] Add seeding for custom grading symbols

---

## PHASE 6: Testing & Cleanup

### 6.1 Testing
- [ ] Test creating numerical section → subjects management → grade entry → report card
- [ ] Test creating non-numerical section → categories management → custom symbols → grade entry → report card
- [ ] Test PDF generation for both types
- [ ] Test editing section grading type (with and without existing grades)
- [ ] Test backward compatibility with existing SNED data

### 6.2 Cleanup
- [ ] Remove hardcoded grade 7 = SNED assumptions
- [ ] Remove deprecated helper functions
- [ ] Clean up old `sned_categories`, `sned_category_fields`, `sned_grades` tables (or keep as legacy)
- [ ] Update documentation

---

## Key Decisions

| Decision | Choice | Rationale |
|----------|--------|-----------|
| Single table for categories | `section_categories` with `section_id` FK | Allows per-section category customization |
| Custom symbols per section | `section_grading_symbols` table | Enables different symbols for different non-numerical sections |
| Refactor or extend? | Refactor → Unified grading tables | Cleaner architecture, less confusion |
| Grade 7 special casing | Remove | Grading type is now per-section, not per-grade-level |

---

## File Change Summary

### New Files to Create:
| # | File | Purpose |
|---|------|---------|
| 1 | `app/Database/Migrations/2026-06-23-000001_AddGradingTypeToSections.php` | Add grading_type column |
| 2 | `app/Database/Migrations/2026-06-23-000002_CreateSectionGradingSymbols.php` | Custom symbols table |
| 3 | `app/Database/Migrations/2026-06-23-000003_CreateUnifiedGradingTables.php` | New grading tables |
| 4 | `app/Models/SectionCategoryModel.php` | Category management |
| 5 | `app/Models/SectionCategoryFieldModel.php` | Field management |
| 6 | `app/Models/SectionGradeModel.php` | Grade storage |
| 7 | `app/Models/SectionGradingSymbolModel.php` | Symbol management |
| 8 | `app/Controllers/Admin/SectionCategories.php` | Category CRUD |
| 9 | `app/Controllers/Admin/GradingSymbols.php` | Symbol CRUD |
| 10 | `app/Views/admin/section_categories.php` | Category UI |
| 11 | `app/Views/admin/section_grading_symbols.php` | Symbol UI |
| 12 | `SNED_GRADING_REFACTOR_PLAN.md` | This plan |

### Files to Modify:
| # | File | Changes |
|---|------|---------|
| 1 | `app/Models/SectionModel.php` | Add grading_type fields, validation |
| 2 | `app/Controllers/Admin/Dashboard.php` | Section creation/editing with grading type |
| 3 | `app/Views/admin/sections.php` | UI for grading type selection & display |
| 4 | `app/Controllers/Teacher/SnedGrades.php` | Make section-aware, detect grading type |
| 5 | `app/Views/teacher/sned_index.php` | Show both grading types |
| 6 | `app/Views/teacher/sned_grade_entry.php` | Dynamic symbols |
| 7 | `app/Views/teacher/sned_report_card.php` | Dynamic legend |
| 8 | `app/Views/teacher/sned_report_card_pdf.php` | Dynamic legend, custom symbols |
| 9 | `app/Helpers/grade_level_helper.php` | Deprecate is_sned_grade() |
| 10 | `app/Helpers/sned_helper.php` | Update to per-section |
| 11 | `app/Config/Routes.php` | Add new routes |
| 12 | `app/Database/Seeds/SnedDemoSeeder.php` | Update for new structure |

### Files to Deprecate/Remove:
| # | File | Notes |
|---|------|-------|
| 1 | `app/Models/SnedCategoryModel.php` | Keep as legacy, or refactor to extend new model |
| 2 | `app/Models/SnedCategoryFieldModel.php` | Keep as legacy, or refactor |
| 3 | `app/Models/SnedGradeModel.php` | Keep as legacy, or refactor |
| 4 | `app/Controllers/Admin/SnedManagement.php` | Keep as legacy, or refactor |

---

## Current Status
- ✅ Phase 1: Database Changes (migrations + model updates)
- 🚧 Phase 2: Backend / Controller Changes (partial - createSection done)
- 🚧 Phase 3: View / UI Changes (partial - modal + table badge added)
- ❌ Phase 4: Helper / Model Changes
- ❌ Phase 5: Data Migration
- ❌ Phase 6: Testing & Cleanup

### Completed Tasks:
- [x] Migration 1: AddGradingTypeToSections (adds ENUM column, sets existing data)
- [x] Migration 2: CreateSectionGradingSymbols (new table + default symbol seeding)
- [x] Migration 3: MakeSnedTablesSectionAware (adds section_id to sned tables, duplicates categories per section)
- [x] SectionModel: Added grading_type to allowedFields, validation, helper method `isNonNumerical()`
- [x] Dashboard::createSection(): Accepts grading_type, seeds default symbols for non-numerical sections
- [x] sections.php: Added "Grading Type" radio buttons in create section modal
- [x] sections.php: Added Grading Type badge column in section table display
