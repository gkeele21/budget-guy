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

## Design System

All colors use semantic CSS variable tokens defined in `tailwind.config.js` — never hardcode hex values. See `docs/technical/design-system.md` for full token reference and `docs/technical/color-palette.html` for visual swatches.

**Button variants:** `primary`, `secondary`, `danger`, `ghost`, `outline`, `income`, `expense`, `transfer`. Sizes: `sm`, `md`, `lg`. Hover inverts bg/text with 2px border.

**Focus:** Use `focus-glow` / `focus-glow-sm` utility classes, not `focus:ring-*`.

## Key Docs (read on-demand, not upfront)
- Design system details: `docs/technical/design-system.md`
- Feature docs: `docs/features/*.md` (budget-view, transactions, accounts, plan-projections, recurring, categories, sharing)
- Frontend patterns: `docs/technical/frontend-patterns.md`
- DB schema: `docs/technical/database-relationships.md`
- Color palette: `docs/technical/color-palette.html` (visual swatch reference)

## Documentation Maintenance
When making code changes, update the relevant docs in the same session:
- **New features or changed business logic** → update `docs/features/*.md`
- **New DB tables or schema changes** → update `docs/technical/database-relationships.md`
- **New UI components or patterns** → update `docs/technical/frontend-patterns.md`
- **New design tokens or animations** → update `docs/technical/design-system.md`
- **New controllers, services, or composables** → update the Project Structure section above
Keep docs concise — focus on code locations, user workflows, and design decisions (the "why").

## Architecture

Modern monolith: Laravel handles routing, auth, and data via Inertia.js which renders Vue 3 SPA pages — no separate API layer.

**Request flow:** Route → Controller (validate, authorize, query) → `Inertia::render()` → Vue Page (receives props as JSON)

**Authorization:** Policy-based (`$this->authorize('update', $budget)`). All data scoped to Budget — users access budgets they own or are invited to.

**Data model (key relationships):**
```
User ─── Budgets (many:many via budget_users, current_budget_id)
Budget ─┬─ Accounts, CategoryGroups, Transactions, Payees, RecurringTransactions, Invites
CategoryGroup ─── Categories ─── MonthlyBudgets (per month)
Transaction ─┬─ Account, Category, Payee, SplitTransactions
             └─ transfer_pair_id (self-ref for transfers)
```

## Project Structure
- `app/Http/Controllers/` — Inertia controllers + VoiceTransactionController, VoiceCategoryController
- `app/Services/` — VoiceTransactionService, VoiceCategoryService, Concerns/CallsClaudeApi
- `app/Models/` — 11 Eloquent models (User, Budget, Account, CategoryGroup, Category, MonthlyBudget, Transaction, SplitTransaction, Payee, RecurringTransaction, Invite)
- `resources/js/Pages/` — Vue pages (Budget/, Transactions/, Plan/, Settings/, Auth/, Onboarding/)
- `resources/js/Components/Base/` — Button, BottomSheet, FAB, Modal, Toggle, FilterChip
- `resources/js/Components/Form/` — TextField, AmountField, DateField, PickerField, SegmentedControl, etc.
- `resources/js/Components/Domain/` — AccountCard, AvailableToBudget, FAB, VoiceOverlay, VoiceCategoryOverlay
- `resources/js/Composables/` — useTheme.js (theming), useSpeechRecognition.js (Web Speech API)
- `config/emoji.php` — Consolidated emoji configuration for category icons
- `public/images/Avatar.png` — Budget Guy mascot avatar used in voice overlays
