# Budget Guy - Architecture & Design

## 1. System Architecture

### 1.1 Architecture Pattern
**Modern Monolithic MVC with SPA Frontend**

```
┌─────────────────────────────────────────┐
│         Presentation Layer              │
│    (Vue 3 Composition API + Vite)       │
│         Tailwind CSS Styling            │
└─────────────────────────────────────────┘
                   ↕ Inertia.js Protocol
┌─────────────────────────────────────────┐
│         Application Layer               │
│      (Laravel 11 Controllers)           │
│     - Inertia Responses                 │
│     - Request Validation                │
│     - Authorization (Policies)          │
└─────────────────────────────────────────┘
                   ↕ Eloquent ORM
┌─────────────────────────────────────────┐
│         Data Layer                      │
│      (MySQL Database)                   │
│     - Migrations                        │
│     - Models & Relationships            │
└─────────────────────────────────────────┘
```

### 1.2 Technology Stack

**Backend**:
- Laravel 11 (PHP 8.2+)
- MySQL
- Eloquent ORM
- Laravel Breeze (Authentication)

**Frontend**:
- Vue 3 (Composition API)
- Vite (Build tool)
- Tailwind CSS
- Inertia.js (SPA bridge)

**Planned Mobile**:
- Capacitor (wraps Vue app for iOS)

**Why Inertia.js?**
- Server-side routing (simpler than REST API)
- No API endpoints needed
- Automatic CSRF protection
- SPA-like experience without complexity

---

## 2. Core Design Patterns

### 2.1 Controller Pattern

Controllers handle request logic and return Inertia responses:

```php
// Controller
public function index()
{
    return Inertia::render('Budget/Index', [
        'categories' => $this->getCategories(),
        'monthlyBudgets' => $this->getMonthlyBudgets(),
    ]);
}
```

### 2.2 Policy-Based Authorization

All authorization uses Laravel Policies:

```php
// Check permission
$this->authorize('update', $budget);
```

### 2.3 Form Handling

Forms use Inertia's form helper:

```vue
<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    amount: '',
    payee_id: null,
    category_id: null,
});

const submit = () => {
    form.post(route('transactions.store'));
};
</script>
```

---

## 3. Database Design

### 3.1 Entity Relationship Overview

```
User ─┬─ Budgets (many:many via budget_users)
      └─ current_budget_id (active budget)

Budget ─┬─ Accounts (1:many)
        ├─ CategoryGroups (1:many)
        ├─ Transactions (1:many)
        ├─ Payees (1:many)
        ├─ RecurringTransactions (1:many)
        └─ Invites (1:many)

CategoryGroup ─── Categories (1:many)

Category ─── MonthlyBudgets (1:many, per month)

Transaction ─┬─ Account (many:1)
             ├─ Category (many:1, nullable)
             ├─ Payee (many:1, nullable)
             ├─ SplitTransactions (1:many)
             └─ transfer_pair_id (self-referential for transfers)
```

**Details**: See [technical/database-relationships.md](technical/database-relationships.md)

### 3.2 Key Design Decisions

#### Budgets Are Containers
**Decision**: All financial data belongs to a Budget, not directly to a User

**Why**:
- Enables multi-user sharing
- Clean data isolation
- Users can have multiple budgets

#### Transfers Are Paired Transactions
**Decision**: Transfers create two linked transactions via `transfer_pair_id`

**Why**:
- Each account shows its own transaction
- Balances stay correct automatically
- Editing one updates both

#### Monthly Budgets Are Separate Records
**Decision**: `monthly_budgets` table stores per-category, per-month allocations

**Why**:
- Clean historical data
- Easy to query specific months
- Supports "copy last month" feature

---

## 4. Frontend Architecture

### 4.1 Vue 3 Composition API

All components use Composition API with `<script setup>`:

```vue
<script setup>
import { ref, computed } from 'vue';

const amount = ref(0);
const formatted = computed(() => `$${amount.value.toFixed(2)}`);
</script>
```

### 4.2 Inertia.js Integration

Pages are Vue components rendered by Inertia:

```php
// Controller
return Inertia::render('Transactions/Index', [
    'transactions' => $transactions,
]);
```

```vue
<!-- Vue component -->
<script setup>
defineProps({
    transactions: Array,
});
</script>
```

### 4.3 Mobile-First Design

All UI is mobile-first (max ~428px):

- Bottom navigation for primary actions
- FAB for quick "Add Transaction"
- Bottom sheets instead of modals
- Touch-friendly tap targets

**Details**: See [technical/frontend-patterns.md](technical/frontend-patterns.md)

---

## 5. Key Architectural Decisions

### 5.1 Envelope Budgeting Model

**Decision**: YNAB-style zero-based budgeting

**How It Works**:
- Money is assigned to categories (envelopes)
- Spending comes from category balances
- "Available to Budget" = unassigned money
- Move money between categories as needed

### 5.2 Plan vs Budget Separation

**Decision**: Projections (Plan) are separate from actual budgets

**Why**:
- Plan future months without affecting current
- Compare projections to actual
- Projections persist until changed

**How It Works**:
- Plan tab: Edit projected amounts for future months
- Budget tab: Manage current month's actual allocations
- "Apply Projections" copies projections to budget

### 5.3 Transaction Types

**Decision**: Three distinct types with different behaviors

| Type | Color | Has Category | Has Payee | Creates |
|------|-------|--------------|-----------|---------|
| Expense | Red | Yes | Yes | Single transaction |
| Income | Green | Yes | Yes | Single transaction |
| Transfer | Blue | No | No | Two linked transactions |

### 5.4 Split Transactions

**Decision**: One transaction can span multiple categories

**How It Works**:
- Parent `Transaction` record with total amount
- Multiple `SplitTransaction` records for category splits
- Splits must sum to total amount
- UI shows "3 Categories" when split

---

## 6. Project Structure

### 6.1 Laravel Structure

```
app/
├── Http/
│   ├── Controllers/      # Inertia controllers
│   ├── Requests/         # Form validation
│   └── Middleware/       # Auth, etc.
├── Models/               # Eloquent models (11)
└── Policies/             # Authorization rules

database/
├── migrations/           # Database schema (14)
└── factories/            # Model factories

routes/
└── web.php               # All routes (Inertia)
```

### 6.2 Vue Structure

```
resources/js/
├── Pages/                # Inertia page components
│   ├── Budget/           # Budget view, category detail
│   ├── Plan/             # Projections
│   ├── Transactions/     # CRUD
│   ├── Settings/         # All settings
│   ├── Onboarding/       # Welcome, setup
│   └── Auth/             # Login, register
├── Components/
│   ├── Base/             # Button, BottomSheet, FAB
│   ├── Form/             # TextField, AmountField, etc.
│   └── Domain/           # AccountCard, AvailableToBudget
└── Layouts/
    ├── AppLayout.vue     # Main app with bottom nav
    └── GuestLayout.vue   # Auth pages
```

### 6.3 Request Flow

```
Request
  ↓
Route (web.php)
  ↓
Controller (validate, authorize, query)
  ↓
Inertia::render()
  ↓
Vue Page (receives props)
  ↓
Response (HTML with JSON props)
```

---

## 7. Navigation Structure

### 7.1 Bottom Navigation

```
Budget | Transactions | Accounts | Plan
```

- **Budget**: Default landing page, monthly envelope view
- **Transactions**: List and manage all transactions
- **Accounts**: Account balances for reconciliation
- **Plan**: Projection mode for future planning

### 7.2 Settings Access

Settings gear icon in header → Settings hub with:
- Accounts management
- Categories management
- Payees management
- Recurring transactions
- Sharing (invites)
- Export data
- Profile

---

## 8. Security

### 8.1 Built-in Laravel Security
- CSRF protection (automatic with Inertia)
- SQL injection prevention (Eloquent ORM)
- XSS protection (Vue escaping)
- Password hashing (bcrypt)
- Mass assignment protection

### 8.2 Authorization Strategy
- Budget-level access control
- Users can only access budgets they own or are invited to
- All controllers check budget membership

---

## 9. Documentation Map

| Topic | Document |
|-------|----------|
| Quick Start | [INSTRUCTIONS.md](INSTRUCTIONS.md) |
| Database Schema | [technical/database-relationships.md](technical/database-relationships.md) |
| Frontend Patterns | [technical/frontend-patterns.md](technical/frontend-patterns.md) |
| Design System | [technical/design-system.md](technical/design-system.md) |

| Feature | Document |
|---------|----------|
| Budget View | [features/budget-view.md](features/budget-view.md) |
| Plan/Projections | [features/plan-projections.md](features/plan-projections.md) |
| Transactions | [features/transactions.md](features/transactions.md) |
| Accounts | [features/accounts.md](features/accounts.md) |
| Categories | [features/categories.md](features/categories.md) |
| Recurring | [features/recurring.md](features/recurring.md) |
| Sharing | [features/sharing.md](features/sharing.md) |

---

**Last Updated**: January 2026
