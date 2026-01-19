# Database Relationships

## Entity Relationship Diagram

```
User
  ├── current_budget_id → Budget (active budget)
  └── budgets (many:many via budget_users)

Budget
  ├── owner_id → User
  ├── accounts (1:many)
  ├── category_groups (1:many)
  ├── transactions (1:many)
  ├── payees (1:many)
  ├── recurring_transactions (1:many)
  └── invites (1:many)

CategoryGroup
  └── categories (1:many)

Category
  ├── group_id → CategoryGroup
  └── monthly_budgets (1:many)

Transaction
  ├── budget_id → Budget
  ├── account_id → Account
  ├── category_id → Category (nullable for transfers)
  ├── payee_id → Payee (nullable)
  ├── transfer_pair_id → Transaction (self-referential)
  ├── recurring_id → RecurringTransaction (nullable)
  └── split_transactions (1:many)

SplitTransaction
  ├── transaction_id → Transaction
  └── category_id → Category

MonthlyBudget
  └── category_id → Category
```

---

## Core Tables

### users

Standard Laravel users table with budget association.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary key |
| name | string | |
| email | string | Unique |
| password | string | Hashed |
| current_budget_id | bigint | FK to budgets, nullable |
| email_verified_at | timestamp | Nullable |
| remember_token | string | |
| created_at | timestamp | |
| updated_at | timestamp | |

### budgets

Container for all budget data.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary key |
| name | string | e.g., "Family Budget" |
| owner_id | bigint | FK to users |
| default_monthly_income | decimal(12,2) | For projections, nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

### budget_users (pivot)

Many-to-many between users and budgets.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary key |
| budget_id | bigint | FK to budgets |
| user_id | bigint | FK to users |
| role | string | 'owner' or 'member' |
| invited_at | timestamp | When invite was sent |
| accepted_at | timestamp | When accepted, nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

### accounts

Bank/card accounts within a budget.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary key |
| budget_id | bigint | FK to budgets |
| name | string | e.g., "Chase Checking" |
| type | string | checking, savings, credit_card, cash |
| starting_balance | decimal(12,2) | Initial balance |
| sort_order | integer | Display order |
| is_closed | boolean | Default false |
| created_at | timestamp | |
| updated_at | timestamp | |

**Note**: Current balance is calculated from `starting_balance + sum(transactions.amount)`

### category_groups

Groupings for categories.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary key |
| budget_id | bigint | FK to budgets |
| name | string | e.g., "Everyday Expenses" |
| sort_order | integer | Display order |
| created_at | timestamp | |
| updated_at | timestamp | |

### categories

Budget categories within groups.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary key |
| group_id | bigint | FK to category_groups |
| name | string | e.g., "Groceries" |
| icon | string | Emoji, nullable |
| default_amount | decimal(12,2) | For projections, nullable |
| projections | json | Future month projections, nullable |
| sort_order | integer | Display order |
| is_hidden | boolean | Default false |
| created_at | timestamp | |
| updated_at | timestamp | |

**Projections JSON format**:
```json
{
    "2026-02": 400.00,
    "2026-03": 425.00
}
```

### monthly_budgets

Per-category budget allocations by month.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary key |
| category_id | bigint | FK to categories |
| month | string | Format: "YYYY-MM" |
| budgeted_amount | decimal(12,2) | |
| created_at | timestamp | |
| updated_at | timestamp | |

**Unique constraint**: `category_id + month`

### transactions

Individual financial transactions.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary key |
| budget_id | bigint | FK to budgets |
| account_id | bigint | FK to accounts |
| category_id | bigint | FK to categories, nullable (transfers) |
| payee_id | bigint | FK to payees, nullable |
| amount | decimal(12,2) | Positive=inflow, negative=outflow |
| type | string | expense, income, transfer |
| date | date | Transaction date |
| cleared | boolean | Default false |
| memo | text | Optional note, nullable |
| transfer_pair_id | bigint | FK to transactions, nullable |
| recurring_id | bigint | FK to recurring_transactions, nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

**Amount conventions**:
- Expense: negative (e.g., -50.00)
- Income: positive (e.g., 2000.00)
- Transfer out: negative in source account
- Transfer in: positive in destination account

### split_transactions

For transactions split across multiple categories.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary key |
| transaction_id | bigint | FK to transactions |
| category_id | bigint | FK to categories |
| amount | decimal(12,2) | Split amount |
| created_at | timestamp | |
| updated_at | timestamp | |

**Note**: Sum of splits must equal parent transaction amount.

### payees

Vendors/recipients for transactions.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary key |
| budget_id | bigint | FK to budgets |
| name | string | e.g., "Costco" |
| default_category_id | bigint | FK to categories, nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

### recurring_transactions

Templates for automated transactions.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary key |
| budget_id | bigint | FK to budgets |
| account_id | bigint | FK to accounts |
| category_id | bigint | FK to categories |
| payee_id | bigint | FK to payees |
| amount | decimal(12,2) | |
| type | string | expense or income |
| frequency | string | daily, weekly, biweekly, monthly, yearly |
| next_date | date | Next occurrence |
| end_date | date | When to stop, nullable |
| end_after_count | integer | Stop after N occurrences, nullable |
| is_active | boolean | Default true |
| created_at | timestamp | |
| updated_at | timestamp | |

### invites

Budget sharing invitations.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint | Primary key |
| budget_id | bigint | FK to budgets |
| email | string | Invitee email |
| invited_by | bigint | FK to users |
| token | string | Unique acceptance token |
| accepted_at | timestamp | When accepted, nullable |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## Common Queries

### Get budget with all related data

```php
Budget::with([
    'accounts',
    'categoryGroups.categories.monthlyBudgets',
    'payees',
])->find($id);
```

### Get transactions for a month

```php
Transaction::where('budget_id', $budgetId)
    ->whereYear('date', $year)
    ->whereMonth('date', $month)
    ->with(['category', 'payee', 'account'])
    ->orderBy('date', 'desc')
    ->get();
```

### Calculate account balance

```php
$balance = $account->starting_balance +
    $account->transactions()->sum('amount');
```

### Get category spending for a month

```php
Transaction::where('category_id', $categoryId)
    ->whereYear('date', $year)
    ->whereMonth('date', $month)
    ->where('type', 'expense')
    ->sum('amount');
```

---

## Key Relationships in Models

### Budget.php
```php
public function accounts() { return $this->hasMany(Account::class); }
public function categoryGroups() { return $this->hasMany(CategoryGroup::class); }
public function transactions() { return $this->hasMany(Transaction::class); }
public function payees() { return $this->hasMany(Payee::class); }
public function users() { return $this->belongsToMany(User::class, 'budget_users'); }
```

### Transaction.php
```php
public function account() { return $this->belongsTo(Account::class); }
public function category() { return $this->belongsTo(Category::class); }
public function payee() { return $this->belongsTo(Payee::class); }
public function splits() { return $this->hasMany(SplitTransaction::class); }
public function transferPair() { return $this->belongsTo(Transaction::class, 'transfer_pair_id'); }
```

### Category.php
```php
public function group() { return $this->belongsTo(CategoryGroup::class, 'group_id'); }
public function monthlyBudgets() { return $this->hasMany(MonthlyBudget::class); }
public function transactions() { return $this->hasMany(Transaction::class); }
```
