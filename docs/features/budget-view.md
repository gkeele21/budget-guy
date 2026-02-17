# Budget View

## Overview

The Budget view is the primary screen where users manage their monthly envelope budget. Users allocate money to categories and track spending against those allocations.

## Key Concepts

- **Envelope Budgeting**: Money is assigned to categories (envelopes) before spending
- **Monthly Budgets**: Each category has a budgeted amount per month
- **Available**: Budgeted minus spent for the month
- **Overspent**: When spending exceeds the budgeted amount (shown in red)

## Code Locations

### Controllers
- `app/Http/Controllers/BudgetController.php` - Monthly budget view, copy last month, move money, apply defaults

### Pages
- `resources/js/Pages/Budget/Index.vue` - Main budget view with category grid
- `resources/js/Pages/Budget/CategoryDetail.vue` - Single category with transaction list

## User Workflows

### View Monthly Budget
1. User lands on Budget tab (default)
2. See current month's budget with all category groups
3. Each category shows: icon, name, budgeted amount, available amount
4. Overspent categories highlighted in red

### Navigate Months
1. Use arrows to go to previous/next month
2. Budget data loads for selected month
3. Can view historical months or plan ahead

### Edit Category Budget
1. Tap on a category's budgeted amount
2. Enter new amount
3. Available updates automatically
4. Changes save immediately

### Copy Last Month
1. Tap "Copy Last Month" button
2. If current month has values, show confirmation
3. Copies previous month's budgeted amounts to current month

### Move Money Between Categories
1. Tap an overspent (red) category amount
2. Modal shows categories with available funds
3. Select a category to move money from
4. Amount transfers between categories
5. Neither category's actual budget changes, just the allocation

### Apply Category Defaults
1. Tap "Apply Defaults" option
2. Sets all categories to their default amounts
3. Useful for starting a new month

### Copy Projections to Defaults
1. From the menu, tap "Copy Projections to Defaults"
2. Updates each category's default amount to match the current projection
3. Useful when projections have been fine-tuned and should become the new baseline

### Hidden Categories
- Categories marked as hidden are excluded from the budget view
- Existing transactions for hidden categories are preserved
- Hidden categories can be unhidden via Settings → Categories

### Zero Amount Display
- Categories with $0 budgeted or $0 available display "$0.00" rather than being blank
- $0 amounts are copyable/selectable like any other amount

## Design Decisions

### Why Categories Have Both Budgeted and Available
**Decision**: Show both budgeted amount and available (budgeted - spent)

**Reasoning**:
- Budgeted shows your plan
- Available shows reality after spending
- Users need both to make decisions

### Why Move Money Instead of Edit Budgets
**Decision**: "Move Money" shifts allocation without changing the budget

**Reasoning**:
- Preserves the original budget for reference
- Makes it clear money came from somewhere
- Standard envelope budgeting practice

## Related Features

- [Plan/Projections](plan-projections.md) - Plan budgets before the month
- [Categories](categories.md) - Manage category structure
- [Transactions](transactions.md) - Transactions affect category spending
