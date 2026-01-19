# Accounts

## Overview

Accounts represent your bank accounts, credit cards, and cash. They track balances and are used for reconciliation with your actual financial accounts.

## Key Concepts

- **Account Types**: Checking, Savings, Credit Card, Cash
- **Starting Balance**: Initial balance when account was added
- **Current Balance**: Starting balance + sum of all transactions
- **Cleared Balance**: Starting balance + sum of cleared transactions
- **Closed Account**: Hidden from main views but history preserved

## Code Locations

### Controllers
- `app/Http/Controllers/AccountController.php` - CRUD, close accounts

### Pages
- `resources/js/Pages/Settings/Accounts/Index.vue` - Manage accounts
- `resources/js/Pages/Settings/Accounts/Edit.vue` - Edit account
- `resources/js/Pages/Dashboard.vue` - Account list (Accounts tab)

### Components
- `resources/js/Components/Domain/AccountCard.vue` - Account display card

### Models
- `app/Models/Account.php`

## Account Types

| Type | Icon | Border Color | Typical Use |
|------|------|--------------|-------------|
| Checking | 🏦 | Blue | Primary spending account |
| Savings | 💰 | Green | Emergency fund, goals |
| Credit Card | 💳 | Red | Credit purchases |
| Cash | 💵 | Green | Physical cash |

## User Workflows

### View Account Balances
1. Navigate to Accounts tab
2. See all accounts grouped by type
3. Each card shows: icon, name, current balance
4. Credit cards may show pending amount
5. Tap account to see its transactions

### Add Account
1. Go to Settings → Accounts
2. Tap "Add Account"
3. Select account type
4. Enter name (e.g., "Chase Checking")
5. Enter starting balance
6. Save

### Edit Account
1. Go to Settings → Accounts
2. Tap account to edit
3. Can change name, type
4. Cannot change starting balance after creation (affects history)

### Close Account
1. Go to Settings → Accounts
2. Edit the account
3. Toggle "Close Account"
4. Account hidden from main views
5. Transaction history preserved
6. Can reopen later if needed

### Reconciliation
1. Navigate to Accounts tab
2. Compare displayed balance with bank statement
3. Balances should match when all transactions entered
4. If mismatch, review recent transactions
5. Mark transactions as cleared when confirmed

## Balance Calculations

### Current Balance
```
starting_balance + sum(all transactions.amount)
```

### Cleared Balance
```
starting_balance + sum(transactions where cleared = true)
```

### Pending
```
current_balance - cleared_balance
```

## Display Patterns

### Account Card
Shows:
- Icon (emoji based on type)
- Account name
- Current balance (colored if credit card is negative)
- Cleared balance (smaller, below)
- Pending amount (red, if applicable)

### Colored Left Border
Each account card has a colored left border indicating type:
- Checking: Blue (`border-secondary`)
- Savings: Green (`border-income`)
- Credit Card: Red (`border-expense`)
- Cash: Green (`border-primary`)

## Design Decisions

### Why Starting Balance Is Immutable
**Decision**: Can't change starting balance after account creation

**Reasoning**:
- All historical transaction math depends on it
- Changing it would alter all past balances
- If wrong, create an adjustment transaction instead

### Why Close Instead of Delete
**Decision**: Accounts can be closed but not deleted

**Reasoning**:
- Preserves transaction history
- Maintains data integrity
- Can reopen if needed
- Standard accounting practice

### Why Accounts Tab Shows Balance Only
**Decision**: Accounts tab shows balances, not management

**Reasoning**:
- Primary use is quick balance check and reconciliation
- Account management (add/edit) is rare after setup
- Keeps the tab focused on its purpose
- Management lives in Settings

## Related Features

- [Transactions](transactions.md) - Transactions belong to accounts
- [Budget View](budget-view.md) - Category spending (different from account balances)
