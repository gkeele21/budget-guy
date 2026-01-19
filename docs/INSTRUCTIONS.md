# Budget Guy - Getting Started Instructions

> **Read this first!** This document contains everything you need to know before making changes to the Budget Guy codebase.

## 1. Codebase Overview

**Budget Guy** is a YNAB-inspired envelope budgeting app built with Laravel (backend) and Vue 3/Inertia (frontend).

### Key Features
- **Budget View:** Monthly envelope budgeting with category groups
- **Plan (Projections):** Plan future months before they start
- **Transactions:** Expense, income, and transfer tracking with splits
- **Accounts:** Bank account management for reconciliation
- **Recurring:** Automated recurring transactions
- **Sharing:** Multi-user budget sharing with invites

### Documentation Structure

- **Core Docs:** `design.md` (architecture), `requirements.md` (features)
- **Feature Docs:** `features/` folder - detailed business logic for each feature
- **Technical Docs:** `technical/` folder - implementation details and patterns
- **Original Spec:** `specs/budget-app-build-guide.md` - original build guide

### Quick Navigation

**For UI/Design Work:**
- Design System: `docs/technical/design-system.md`
- Component Showcase: `docs/dev/component-showcase.html`
- Design Mockups: `docs/dev/design-mockups.html`

**For Feature Work:**
- Budget View: `docs/features/budget-view.md`
- Transactions: `docs/features/transactions.md`
- Accounts: `docs/features/accounts.md`
- Plan/Projections: `docs/features/plan-projections.md`

**For Technical Implementation:**
- Database: `docs/technical/database-relationships.md`
- Frontend Patterns: `docs/technical/frontend-patterns.md`

## 2. Design System

### Color Palette

Budget Guy uses a fresh, mobile-first design with semantic colors for financial operations.

#### Primary Colors

**Brand Green** - Primary actions, brand accents
- `primary` (#7ED957): Primary buttons, FAB, brand elements
- `primary-light` (#8bd93e): Gradients
- `primary-bg` (#edfce0): Light backgrounds, highlights
- `primary-hover` (#6bc94a): Hover states

**Brand Blue** - Secondary actions
- `secondary` (#2196F3): Secondary buttons, links
- `secondary-hover` (#1976D2): Hover states

#### Financial Semantic Colors

**Income** (`income` / #2E7D32) - Money coming in
- Use for: Income transactions, positive amounts, success states

**Expense** (`expense` / #E5533D) - Money going out
- Use for: Expense transactions, negative amounts, overspent categories

**Transfer** (`transfer` / #2196F3) - Money moving between accounts
- Use for: Transfer transactions, neutral movements

#### Surface & Text Colors

- `surface` (#ffffff): Card backgrounds
- `surface-secondary` (#f5f5f5): Page backgrounds
- `body` (#1F2933): Primary text
- `subtle` (#888888): Secondary text, labels
- `inverse` (#ffffff): Text on dark backgrounds

### Button Component (ALWAYS USE THIS!)

The application uses a unified `Button` component. **Never use inline button styles** - always use the Button component.

**Import:**
```vue
import Button from '@/Components/Base/Button.vue';
```

**Basic Usage:**
```vue
<!-- Regular button -->
<Button variant="primary" size="md">Save</Button>

<!-- Link button -->
<Button :href="route('transactions.create')" variant="primary">
    Add Transaction
</Button>

<!-- Full width -->
<Button variant="primary" :fullWidth="true">Submit</Button>

<!-- With loading state -->
<Button :loading="form.processing" variant="primary">Saving...</Button>
```

**Button Variants:**
- `primary`: Main CTAs, form submissions (green with dark text)
- `secondary`: Alternative actions (blue with white text)
- `danger`: Delete, destructive actions (red with white text)
- `ghost`: Subtle actions (transparent with blue text)
- `outline`: Alternative secondary (bordered blue)
- `income`: Income-specific actions (dark green)
- `expense`: Expense-specific actions (red)
- `transfer`: Transfer-specific actions (blue)

**Button Sizes:**
- `sm`: Compact buttons
- `md` (default): Standard size
- `lg`: Prominent CTAs

### Form Components

All form fields are in `@/Components/Form/`:

| Component | Usage |
|-----------|-------|
| `TextField` | Basic text input with label |
| `AmountField` | Currency input with $ prefix, colored by type |
| `DateField` | Date picker |
| `PickerField` | Select from bottom sheet |
| `AutocompleteField` | Search/autocomplete input |
| `SegmentedControl` | Toggle between options (expense/income/transfer) |
| `ToggleField` | Boolean switch |
| `Checkbox` | Checkbox with label |
| `FormRow` | Container for form fields |

### Layout Structure

**AppLayout** - Main authenticated layout with:
- Sticky header with settings gear
- Bottom navigation: Budget | Transactions | Accounts | Plan
- FAB for quick "Add Transaction"

**Navigation Order:**
```
Budget | Transactions | Accounts | Plan
```

### Typography

**Font Families:**
- `font-sans` (IBM Plex Sans): All UI text
- `font-mono` (IBM Plex Mono): Currency amounts

**Common Patterns:**
- Page titles: `text-xl font-semibold text-body`
- Card titles: `text-lg font-medium text-body`
- Labels: `text-sm text-subtle`
- Amounts: `font-mono font-semibold` + color class

## 3. Key Development Principles

### Before You Code

1. **Read relevant docs FIRST** - Check feature docs to understand business logic
2. **Check existing patterns** - Look at similar features for consistency
3. **Use the design system** - Always use Button component and semantic colors
4. **Never create custom components** when one exists - check `resources/js/Components/`

### When You Code

1. **Follow existing patterns** - Check `docs/technical/frontend-patterns.md`
2. **Security first** - Watch for XSS, SQL injection, OWASP top 10
3. **Prefer editing over creating** - Always edit existing files rather than create new ones
4. **Mobile-first** - This is primarily a mobile app

### After You Code

1. **Update documentation** - If you change business logic, update feature docs
2. **Check the design** - Visit your changes to verify design system compliance

## 4. Project Structure

### Laravel (Backend)

```
app/
├── Http/Controllers/     # Inertia controllers
├── Models/               # Eloquent models (11 total)
└── Policies/             # Authorization

database/
├── migrations/           # Database schema (14 migrations)
└── factories/            # Model factories
```

### Vue (Frontend)

```
resources/js/
├── Pages/                # Inertia page components
│   ├── Budget/           # Budget view, category detail
│   ├── Transactions/     # Transaction CRUD
│   ├── Plan/             # Projections
│   ├── Settings/         # All settings pages
│   ├── Onboarding/       # Welcome, setup flow
│   └── Auth/             # Login, register, etc.
├── Components/
│   ├── Base/             # Button, BottomSheet, FAB
│   ├── Form/             # All form inputs
│   └── Domain/           # AccountCard, AvailableToBudget
└── Layouts/
    ├── AppLayout.vue     # Main app with bottom nav
    ├── AuthenticatedLayout.vue
    └── GuestLayout.vue
```

### Key Models

| Model | Purpose |
|-------|---------|
| `User` | Authentication, budget membership |
| `Budget` | Container for all budget data |
| `Account` | Bank accounts (checking, savings, credit, cash) |
| `CategoryGroup` | Groups of categories |
| `Category` | Budget categories with icons and defaults |
| `MonthlyBudget` | Per-category monthly allocations |
| `Transaction` | Individual transactions |
| `SplitTransaction` | Multi-category splits |
| `Payee` | Vendors with default categories |
| `RecurringTransaction` | Automated transactions |
| `Invite` | Budget sharing invites |

## 5. Quick Reference Checklist

Before submitting any code, verify:

- [ ] Read relevant feature documentation
- [ ] Used Button component (never inline button styles)
- [ ] Used semantic color names (income/expense/transfer, not arbitrary colors)
- [ ] Followed existing patterns from similar features
- [ ] Updated documentation if business logic changed
- [ ] Tested on mobile viewport

## Resources

- **Component Showcase:** `docs/dev/component-showcase.html`
- **Design Mockups:** `docs/dev/design-mockups.html`
- **Original Spec:** `docs/specs/budget-app-build-guide.md`
- **Tailwind Config:** `tailwind.config.js`
- **Component Library:** `resources/js/Components/`

---

**Questions or unclear docs?** Ask the user or check similar implementations in the codebase.
