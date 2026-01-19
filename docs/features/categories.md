# Categories

## Overview

Categories are the "envelopes" in envelope budgeting. Money is allocated to categories, and spending is tracked against those allocations. Categories are organized into groups for easy management.

## Key Concepts

- **Category Group**: Container for related categories (e.g., "Everyday Expenses")
- **Category**: Individual budget category (e.g., "Groceries")
- **Icon**: Emoji representing the category
- **Default Amount**: Baseline budget for projections and quick setup
- **Hidden**: Category not shown in main views but still usable

## Code Locations

### Controllers
- `app/Http/Controllers/CategoryController.php` - CRUD categories
- `app/Http/Controllers/CategoryGroupController.php` - CRUD groups

### Pages
- `resources/js/Pages/Settings/Categories/Index.vue` - Manage categories

### Models
- `app/Models/Category.php`
- `app/Models/CategoryGroup.php`

## User Workflows

### View Categories
1. Go to Settings → Categories
2. See all groups with their categories
3. Each category shows: icon, name, default amount

### Add Category Group
1. Go to Settings → Categories
2. Tap "+ Add Category Group"
3. Enter group name (e.g., "Bills")
4. Save

### Add Category
1. Go to Settings → Categories
2. Tap "+" on a group, or tap group menu → "Add Category"
3. Enter name (e.g., "Electric")
4. Pick an emoji icon
5. Set default amount (optional)
6. Save

### Edit Category
1. Tap on a category
2. Edit name, icon, default amount
3. Can move to different group
4. Can hide category
5. Save

### Reorder Categories
1. Use drag handles to reorder within a group
2. Can drag category to a different group
3. Order persists via `sort_order` field

### Hide Category
1. Edit the category
2. Toggle "Hidden"
3. Category won't appear in budget view or pickers
4. Existing transactions preserved
5. Can unhide later

### Delete Category
1. Edit the category
2. Tap "Delete"
3. Only allowed if no transactions use this category
4. Otherwise, must reassign transactions first or hide instead

## Default Amounts

The `default_amount` field serves multiple purposes:

1. **Projections**: Starting point when planning future months
2. **Apply Defaults**: Quick fill budget with category defaults
3. **Reference**: Shows expected amount when budgeting

Setting good defaults makes monthly budgeting faster.

## Category Structure

### Typical Groups
- **Income**: Paycheck, Side Income, Gifts
- **Everyday Expenses**: Groceries, Gas, Restaurants
- **Bills**: Rent, Utilities, Insurance, Subscriptions
- **Savings**: Emergency Fund, Vacation, Big Purchases
- **Personal**: Clothing, Entertainment, Hobbies

### Income Categories
Income transactions also need categories. Common pattern:
- Group: "Income"
- Categories: "Paycheck", "Side Hustle", "Gifts", etc.

## Design Decisions

### Why Groups Are Required
**Decision**: Every category must belong to a group

**Reasoning**:
- Consistent organization
- Budget view groups by category group
- Easier to manage related categories
- Can collapse/expand groups in UI

### Why Emoji Icons
**Decision**: Categories use emoji for icons

**Reasoning**:
- Universal and colorful
- No icon library needed
- Users can pick from device emoji keyboard
- Fun and personal

### Why Hide Instead of Delete
**Decision**: Categories with transactions can't be deleted, only hidden

**Reasoning**:
- Preserves historical data integrity
- Transaction references remain valid
- Can unhide if needed
- Standard data preservation practice

## Related Features

- [Budget View](budget-view.md) - Categories are budgeted here
- [Plan/Projections](plan-projections.md) - Default amounts used for projections
- [Transactions](transactions.md) - Transactions are assigned to categories
