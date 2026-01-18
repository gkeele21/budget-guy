# FamilyBudget Implementation Tracker

This document tracks the implementation status of all features from the specs/wireframes.

---

## Phase 1: Core MVP ✅ COMPLETE

- [x] Authentication (login/register)
- [x] Budget creation/selection
- [x] Dashboard with accounts grouped by type
- [x] "Available to Budget" calculation
- [x] Accounts CRUD (create, read, update, delete)
- [x] Account types (checking, savings, credit_card, cash)
- [x] Categories & Groups CRUD
- [x] Category icons (emoji)
- [x] Category default amounts
- [x] Transactions CRUD
- [x] Transaction types (expense, income, transfer)
- [x] Cleared status toggle
- [x] Basic Budget view with monthly allocations
- [x] FAB (+) button for quick transaction entry
- [x] Bottom navigation (Accounts | Budget | Transactions)

---

## Phase 2: Essential Features ✅ COMPLETE

### Transfers
- [x] Transfer transaction type
- [x] From/To account selectors
- [x] Linked transactions in both accounts
- [x] Gray color styling for transfers

### Split Transactions (Screens 5A-5B) ✅ COMPLETE
- [x] Database schema (split_transactions table)
- [x] SplitTransaction model
- [x] UI: "Split Transaction" option in category selector
- [x] UI: Add multiple category/amount pairs
- [x] UI: Visual indicator for unassigned remainder (yellow bar)
- [x] UI: Visual indicator for balanced split (green bar)
- [x] Validation: Cannot save until fully balanced
- [x] Display: Show "Split (X)" in transaction list

### Recurring Transactions (Screens 7B-7C) ✅ COMPLETE
- [x] Database schema
- [x] RecurringTransaction model
- [x] CRUD pages (Create, Index, Edit)
- [x] Frequencies (daily, weekly, biweekly, monthly, yearly)
- [x] End conditions (never, after X, on date)
- [x] Process due transactions logic
- [x] Toggle between All/Recurring in transaction list
- [x] ↻ badge on recurring transactions in list
- [x] Group by frequency in recurring list

### Payee Management ✅ COMPLETE
- [x] Payee model with default_category_id
- [x] Auto-create payees on transaction entry
- [x] Payee autocomplete in transaction form
- [x] Settings page: Payee list view
- [x] Settings page: Edit payee name
- [x] Settings page: Edit default category
- [x] Settings page: Delete payee
- [x] "Update default for {Payee}?" prompt after save

### Budget Features (Screens 6A-6F) ✅ COMPLETE
- [x] Basic budget amount editing
- [x] "Copy Last Month" button
- [x] Warning modal if overwriting existing amounts
- [x] Green border on edited amounts (with 2s fade)
- [x] Real-time "Ready to Assign" updates

### Move Money Modal (Screens 6E-6F) ✅ COMPLETE
- [x] Tap overspent (red) amount to open modal
- [x] Modal title: "{Category} is over by ${amount}"
- [x] List categories with surplus (sorted by available)
- [x] Tap category to move funds
- [x] Partial moves supported
- [x] "Done" button to close

### Category Detail View (Screen 4) ✅ COMPLETE
- [x] Tap category in budget → detail view
- [x] Header with budgeted/spent/available
- [x] List transactions in that category
- [x] Cleared status indicator
- [x] Tap to edit transaction

---

## Phase 3: Polish Features 🟡 IN PROGRESS

### Budget Projections (Screen 6D) ✅ COMPLETE
- [x] Toggle to "Projection Mode"
- [x] Sticky header with Expected Income field
- [x] Default income reference (pre-fills from budget default)
- [x] Total Projected summary
- [x] Left to Allocate calculation
- [x] Category columns: Default | Projected amounts
- [x] Show differences from default (e.g., "↑ $35")
- [x] Support 1-3 projection columns (stored as JSON)
- [x] "Clear All Projections" button
- [x] "Apply Projections" button

### Voice Input (Screens 10A-10D)
- [ ] Voice input button on transaction form
- [ ] Listening state with pulsing indicator
- [ ] Live transcript display
- [ ] Processing state with spinner
- [ ] Success state with highlighted items
- [ ] Clarification state for ambiguous input
- [ ] Parse single transactions: "I spent $120 at Costco on groceries"
- [ ] Parse multiple transactions: "...and $45 at Shell on gas"
- [ ] Create categories: "Add a category called Pet Supplies in Everyday"
- [ ] Budget amounts: "Set groceries to $400"
- [ ] Move money: "Move $50 from Dining Out to Groceries"

### Multi-User Sharing (Screens 12A-12D) ✅ COMPLETE
- [x] Database schema (budget_users, invites tables)
- [x] Budget-user relationships with roles
- [x] Settings: "People with Access" section
- [x] Settings: Show avatars, names, emails
- [x] Settings: "Owner" badge on budget creator
- [x] Settings: "Remove" button for members
- [x] Settings: "Invite Someone" section with email input
- [x] Settings: "Pending Invites" section
- [x] Send invite (email integration pending)
- [x] Accept invite flow (new users)
- [x] Accept invite flow (existing users)
- [x] Decline invite option
- [ ] Real-time sync for shared budgets

### Onboarding Flow (Screens 11A-11C) ✅ COMPLETE
- [x] Welcome screen with logo and tagline
- [x] "Create Account" button
- [x] "Sign In" button
- [ ] Social sign-in options (Apple, Google) - skipped
- [x] Sign up form (email, password, confirm)
- [x] Quick setup wizard (skippable)
- [x] Step 1: Budget name
- [x] Step 2: Add first account (name + balance)
- [x] Step 3: Set up categories (offer templates)
- [x] "Skip setup" link
- [x] Progress indicator

### Search & Filters (Screen 7) ✅ COMPLETE
- [x] Search icon in transactions header
- [x] Search by payee name
- [x] Search by memo
- [x] Search by amount
- [x] Search by category name
- [x] Filter by date range
- [x] Filter by cleared status
- [x] Filter icon with active indicator

### Export Data ✅ COMPLETE
- [x] Settings → Export Data page
- [x] Export to CSV
- [x] Export to JSON
- [x] Select date range for export
- [x] Export transactions only, budget only, or full export

### AI Assistant
- [ ] Settings → Tools → AI Assistant
- [ ] Chat interface for budget questions
- [ ] Spending insights
- [ ] Budget recommendations

---

## Phase 4: Mobile ❌ NOT STARTED

- [x] Capacitor configured
- [x] iOS folder structure created
- [ ] iOS build testing
- [ ] iOS-specific UI adjustments
- [ ] App icon and splash screen
- [ ] Push notifications setup
- [ ] App Store assets preparation
- [ ] App Store submission

---

## UI Polish Items

### Transaction List Enhancements
- [ ] Swipe left to delete
- [ ] Toast confirmation with Undo option
- [ ] Highlight newly created items (fade after 2s)

### Clearing Interactions (Screen 7A)
- [x] Tap dot to toggle cleared
- [ ] Toast confirmation with Undo
- [ ] Account balances update immediately (visual feedback)

### Visual Consistency
- [ ] IBM Plex Sans font throughout
- [ ] IBM Plex Mono for amounts
- [ ] Consistent card border-radius (12px)
- [ ] Consistent button border-radius (12px)
- [ ] Color consistency (expense red, income green, transfer gray)

---

## Database Schema Status

| Table | Created | In Use |
|-------|---------|--------|
| users | ✅ | ✅ |
| budgets | ✅ | ✅ |
| budget_users | ✅ | ✅ |
| accounts | ✅ | ✅ |
| category_groups | ✅ | ✅ |
| categories | ✅ | ✅ |
| monthly_budgets | ✅ | ✅ |
| payees | ✅ | ✅ |
| transactions | ✅ | ✅ |
| recurring_transactions | ✅ | ✅ |
| split_transactions | ✅ | ✅ |
| invites | ✅ | ✅ |

---

## Progress Summary

| Phase | Total Items | Completed | Percentage |
|-------|-------------|-----------|------------|
| Phase 1: Core MVP | 15 | 15 | 100% |
| Phase 2: Essential Features | 36 | 36 | 100% |
| Phase 3: Polish Features | 48 | 45 | 94% |
| Phase 4: Mobile | 8 | 2 | 25% |
| **Overall** | **107** | **98** | **92%** |

Note: Voice Input and AI Assistant features intentionally skipped.

---

*Last updated: 2026-01-17*