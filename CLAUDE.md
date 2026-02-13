# Budget Guy

Laravel 11 + Vue 3 + Inertia.js envelope budgeting app. Mobile-first.

## Stack
- **Backend:** Laravel, Eloquent, Inertia
- **Frontend:** Vue 3 (Composition API), Tailwind CSS
- **Font:** Figtree (sans), monospace for currency amounts
- **Build:** Vite (`npm run dev` / `npm run build`)

## Critical Rules
- **Always use the `Button` component** (`@/Components/Base/Button.vue`) — never inline button styles
- **Always use semantic color tokens** — never hardcode hex colors
- **Check `@/Components/` before creating new components** — reuse what exists
- **Read relevant `docs/features/*.md`** before changing feature business logic
- **Mobile-first** — this is primarily a mobile app

## Design System (CSS Variable Tokens)

All colors resolve through CSS variables in `tailwind.config.js`. Never use raw hex values.

**Surfaces (5-layer hierarchy):** `bg-bg`, `bg-surface`, `bg-surface-elevated`, `bg-surface-overlay`, `bg-surface-inset`, `bg-surface-header`

**Financial semantics:** `text-income` / `text-expense` / `text-transfer` (map to success/danger/info vars)

**Text:** `text-body`, `text-muted`, `text-subtle`, `text-inverse`

**Primary/Actions:** `bg-primary`, `bg-primary-hover`

**Focus:** Use `focus-glow` / `focus-glow-sm` utility classes, not `focus:ring-*`

**Borders:** `border-border`, `border-border-strong`

**Theming:** Accent colors (green/blue/orange) and background modes (slate/cream) controlled via `useTheme.js` composable. CSS classes on `<body>`: `.accent-green`, `.accent-blue`, `.accent-orange`, `.bg-mode-slate`, `.bg-mode-cream`. Persisted in localStorage.

**Button variants:** `primary`, `secondary`, `danger`, `ghost`, `outline`, `income`, `expense`, `transfer`. Sizes: `sm`, `md`, `lg`. Hover inverts bg/text with 2px border.

## Key Docs (read on-demand, not upfront)
- Design system details: `docs/technical/design-system.md`
- Feature docs: `docs/features/*.md` (budget-view, transactions, accounts, plan-projections, recurring, categories, sharing)
- Frontend patterns: `docs/technical/frontend-patterns.md`
- DB schema: `docs/technical/database-relationships.md`

## Project Structure
- `app/Http/Controllers/` — Inertia controllers
- `app/Models/` — 11 Eloquent models (User, Budget, Account, CategoryGroup, Category, MonthlyBudget, Transaction, SplitTransaction, Payee, RecurringTransaction, Invite)
- `resources/js/Pages/` — Vue pages (Budget/, Transactions/, Plan/, Settings/, Auth/, Onboarding/)
- `resources/js/Components/Base/` — Button, BottomSheet, FAB, Modal, Toggle, FilterChip
- `resources/js/Components/Form/` — TextField, AmountField, DateField, PickerField, SegmentedControl, etc.
- `resources/js/Components/Domain/` — AccountCard, AvailableToBudget, FAB
- `resources/js/Composables/` — useTheme.js (accent color + background mode switching)
