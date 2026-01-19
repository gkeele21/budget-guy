# Recurring Transactions

## Overview

Recurring transactions automate the creation of regular expenses and income. Instead of manually entering the same transaction every month, users set up a recurring template and the system creates transactions automatically.

## Key Concepts

- **Frequency**: How often the transaction repeats (daily, weekly, biweekly, monthly, yearly)
- **Next Date**: When the next instance will be created
- **End Condition**: When to stop (never, after X occurrences, on specific date)
- **Active**: Whether the recurring is currently running

## Code Locations

### Controllers
- `app/Http/Controllers/RecurringTransactionController.php` - CRUD, toggle active

### Pages
- `resources/js/Pages/Settings/Recurring/Index.vue` - List recurring transactions
- `resources/js/Pages/Settings/Recurring/Create.vue` - Add new recurring
- `resources/js/Pages/Settings/Recurring/Edit.vue` - Edit existing

### Models
- `app/Models/RecurringTransaction.php`

## User Workflows

### View Recurring Transactions
1. Go to Settings → Recurring
2. See all recurring transactions grouped by frequency
3. Each shows: payee, amount, next date, account + category

### Add Recurring Transaction
1. Go to Settings → Recurring
2. Tap "Add Recurring"
3. Select type (Expense or Income)
4. Enter payee
5. Enter amount
6. Select account
7. Select category
8. Choose frequency (daily/weekly/biweekly/monthly/yearly)
9. Set start/next date
10. Set end condition (Never/After X/On Date)
11. Save

### Edit Recurring Transaction
1. Tap recurring transaction in list
2. Modify any field
3. Future instances use new values
4. Past instances unchanged

### Toggle Active/Inactive
1. Use toggle or menu on recurring transaction
2. Inactive recurring won't create new transactions
3. Can reactivate later

### Delete Recurring Transaction
1. Edit the recurring
2. Tap "Delete"
3. Stops future instances
4. Past transactions remain (they're regular transactions)

## Frequency Options

| Frequency | Creates Transaction |
|-----------|-------------------|
| Daily | Every day |
| Weekly | Same day each week |
| Biweekly | Every 2 weeks |
| Monthly | Same day each month |
| Yearly | Same date each year |

## End Conditions

| Condition | Behavior |
|-----------|----------|
| Never | Runs indefinitely |
| After X | Stops after X occurrences |
| On Date | Stops on specific end date |

## Generated Transactions

When a recurring transaction creates a regular transaction:

1. Transaction created with `recurring_id` pointing to the recurring template
2. Marked in transaction list with ↻ badge
3. Can be edited independently (changes don't affect template)
4. Deleting generated transaction doesn't affect recurring
5. `next_date` on recurring updates to next occurrence

## Variable Amounts

For transactions that vary each time (e.g., utility bills):
- Set approximate amount in recurring
- Edit the generated transaction when actual amount known
- Amount shown with ~ prefix in recurring list to indicate variable

## Design Decisions

### Why Recurring Creates Transactions (Not Auto-Apply)
**Decision**: Recurring creates real transactions that can be edited

**Reasoning**:
- Users can adjust amount if it varies
- Creates audit trail
- Can delete individual instance without breaking recurring
- Clear separation between template and actual transactions

### Why Link With recurring_id
**Decision**: Generated transactions link back to recurring template

**Reasoning**:
- Can identify which transactions came from recurring
- Show ↻ badge in transaction list
- Useful for reports and analysis
- Doesn't prevent editing the transaction

### Why Types Are Limited to Expense/Income
**Decision**: Recurring doesn't support transfers

**Reasoning**:
- Transfers are typically manual and intentional
- Automatic transfers could cause confusion
- If needed, can manually create transfer on recurring dates

## Related Features

- [Transactions](transactions.md) - Recurring creates regular transactions
- [Budget View](budget-view.md) - Recurring transactions affect category spending
