# Plan (Projections)

## Overview

The Plan tab allows users to project and plan their budget for future months before they arrive. This is separate from the actual monthly budget, letting users experiment with numbers without affecting the current month.

## Key Concepts

- **Projections**: Planned amounts for future months (stored in `categories.projections` JSON)
- **Expected Income**: Planned income used to calculate "Left to Allocate"
- **Default Amount**: Baseline amount set per category (used as starting point)
- **Apply Projections**: Copy projected amounts to actual monthly budget

## Code Locations

### Controllers
- `app/Http/Controllers/PlanController.php` - View and manage projections
- `app/Http/Controllers/BudgetController.php` - Apply projections to monthly budget

### Pages
- `resources/js/Pages/Plan/Index.vue` - Projection planning interface

### Models
- `app/Models/Category.php` - `projections` JSON field stores projected amounts
- `app/Models/Budget.php` - `default_monthly_income` for expected income

## User Workflows

### View Projections
1. Navigate to Plan tab
2. See expected income at top (sticky)
3. See summary: Total Projected | Left to Allocate
4. Category list shows Default | Projected columns

### Edit Expected Income
1. Tap expected income field
2. Enter anticipated income for the month
3. "Left to Allocate" updates in real-time
4. Shows hint: "Default: $X · Last month: $Y"

### Set Category Projections
1. Tap a category's projected amount
2. Enter planned amount
3. First edit auto-fills from category default
4. Total Projected and Left to Allocate update live

### Apply Projections to Budget
1. Tap "Apply Projections" button
2. If budget month has existing values, show confirmation
3. Projected amounts become the actual monthly budget
4. Navigate to Budget tab to see applied amounts

### Save Projections as Defaults
1. Tap "Save as Defaults" from the menu
2. Updates each category's default amount to match its current projection
3. Useful when projections have been refined and should become the new baseline

### Clear All Projections
1. Tap "Clear All Projections"
2. Resets all projections to category defaults
3. Useful for starting fresh

## Data Storage

### Category Projections (JSON)

Stored in `categories.projections` as JSON:

```json
{
    "2026-02": 400.00,
    "2026-03": 425.00,
    "2026-04": 450.00
}
```

### Budget Default Income

Stored in `budgets.default_monthly_income` as decimal.

## Design Decisions

### Why Plan is Separate from Budget
**Decision**: Plan tab is separate from Budget tab

**Reasoning**:
- Users can experiment without affecting current month
- Clear separation between "planning" and "living with budget"
- Projections persist until explicitly changed
- Supports planning multiple months ahead

### Why Projections Are Per-Category JSON
**Decision**: Store projections in category JSON, not separate table

**Reasoning**:
- Simpler data model
- Projections are temporary planning data
- Once applied, they become monthly_budgets records
- Don't need historical projection data

### Why Show "Left to Allocate"
**Decision**: Show Expected Income - Total Projected

**Reasoning**:
- Zero-based budgeting principle
- Users should assign every dollar
- Visual feedback helps balance the budget
- Negative value indicates over-allocation

## UI/UX Notes

### Sticky Header
The expected income and summary stats stay visible while scrolling the category list. This lets users see the impact of changes as they scroll.

### Totals Row
A totals row at the bottom of the category list shows the sum of all projected amounts in a highlighted blue row. This helps users see the total at a glance alongside the per-category breakdown.

### Default vs Projected Columns
Showing both columns lets users see how their projection differs from their baseline (default amount).

## Related Features

- [Budget View](budget-view.md) - Where projections are applied
- [Categories](categories.md) - Default amounts set here
