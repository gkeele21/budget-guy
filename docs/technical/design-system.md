# Design System

Budget Guy uses a mobile-first design with semantic colors for financial operations.

## Color Palette

### Primary (Brand Green)

The main brand color used for primary actions and accents.

| Token | Hex | Usage |
|-------|-----|-------|
| `primary` | #7ED957 | Primary buttons, FAB, brand elements |
| `primary-light` | #8bd93e | Gradient endpoints |
| `primary-bg` | #edfce0 | Light backgrounds, highlights |
| `primary-hover` | #6bc94a | Hover states |

### Secondary (Brand Blue)

Used for secondary actions and links.

| Token | Hex | Usage |
|-------|-----|-------|
| `secondary` | #2196F3 | Secondary buttons, links, transfers |
| `secondary-hover` | #1976D2 | Hover states |

### Financial Semantic Colors

These colors convey meaning for financial operations.

| Token | Hex | Usage |
|-------|-----|-------|
| `income` | #2E7D32 | Income transactions, positive amounts |
| `income-hover` | #256b29 | Hover states |
| `expense` | #E5533D | Expense transactions, negative amounts, overspent |
| `expense-hover` | #d04a35 | Hover states |
| `transfer` | #2196F3 | Transfer transactions (same as secondary) |

### Feedback Colors

Aliases to financial colors for general feedback.

| Token | Hex | Usage |
|-------|-----|-------|
| `success` | #2E7D32 | Success states (same as income) |
| `danger` | #E5533D | Error/destructive (same as expense) |

### Surface Colors

Background colors for cards and pages.

| Token | Hex | Usage |
|-------|-----|-------|
| `surface` | #ffffff | Card backgrounds |
| `surface-secondary` | #f5f5f5 | Page backgrounds |

### Border Colors

| Token | Hex | Usage |
|-------|-----|-------|
| `border` | #e5e7eb | Default borders |
| `border-dark` | #d1d5db | Emphasized borders |

### Text Colors

| Token | Hex | Usage |
|-------|-----|-------|
| `body` | #1F2933 | Primary text |
| `subtle` | #888888 | Secondary text, labels |
| `inverse` | #ffffff | Text on dark backgrounds |

---

## Typography

### Font Families

```css
font-sans: 'IBM Plex Sans'  /* All UI text */
font-mono: 'IBM Plex Mono'  /* Currency amounts */
```

### Common Patterns

| Element | Classes |
|---------|---------|
| Page title | `text-xl font-semibold text-body` |
| Card title | `text-lg font-medium text-body` |
| Section header | `text-sm font-semibold text-subtle uppercase` |
| Body text | `text-sm text-body` |
| Label | `text-sm text-subtle` |
| Currency | `font-mono font-semibold` + color class |

---

## Components

### Button

**Location**: `@/Components/Base/Button.vue`

Always use the Button component for interactive buttons. Never use inline button styles.

```vue
import Button from '@/Components/Base/Button.vue';

<!-- Primary action -->
<Button variant="primary">Save</Button>

<!-- Link button -->
<Button :href="route('transactions.create')" variant="primary">Add</Button>

<!-- Full width -->
<Button variant="primary" :fullWidth="true">Submit</Button>

<!-- Loading state -->
<Button :loading="form.processing">Saving...</Button>
```

**Variants:**
- `primary` - Main CTAs (green with dark text)
- `secondary` - Alternative actions (blue with white text)
- `danger` - Destructive actions (red with white text)
- `ghost` - Subtle actions (transparent with blue text)
- `outline` - Bordered secondary (transparent with blue border)
- `income` - Income-specific (dark green)
- `expense` - Expense-specific (red)
- `transfer` - Transfer-specific (blue)

**Sizes:**
- `sm` - Compact
- `md` - Standard (default)
- `lg` - Prominent

### Form Components

All in `@/Components/Form/`:

| Component | Usage |
|-----------|-------|
| `TextField` | Text input with label |
| `AmountField` | Currency with $ prefix, colored by type |
| `DateField` | Date picker |
| `PickerField` | Select via bottom sheet |
| `AutocompleteField` | Search/autocomplete |
| `SegmentedControl` | Toggle (expense/income/transfer) |
| `ToggleField` | Boolean switch |
| `Checkbox` | Checkbox with label |
| `FormRow` | Form field container |

### BottomSheet

**Location**: `@/Components/Base/BottomSheet.vue`

Used for pickers and modals on mobile.

```vue
<BottomSheet :show="isOpen" title="Select Category" @close="isOpen = false">
    <!-- Content -->
</BottomSheet>
```

### FAB (Floating Action Button)

**Location**: `@/Components/Base/FAB.vue`

Primary action button, typically "Add Transaction".

```vue
<FAB :href="route('transactions.create')" />
```

### AccountCard

**Location**: `@/Components/Domain/AccountCard.vue`

Displays account with balance and type indicator.

---

## Layout

### AppLayout

Main authenticated layout with:
- Sticky header with settings gear icon
- Bottom navigation
- FAB slot

**Bottom Nav Order:**
```
Budget | Transactions | Accounts | Plan
```

### Card Patterns

**Standard Card:**
```vue
<div class="bg-surface rounded-card p-4 shadow-sm">
    <!-- Content -->
</div>
```

**Card with colored border:**
```vue
<div class="bg-surface rounded-card p-4 border-l-4 border-primary">
    <!-- Content -->
</div>
```

---

## Spacing

| Token | Value | Usage |
|-------|-------|-------|
| Card padding | 16px (p-4) | Inside cards |
| Page padding | 16px (p-4) | Page margins |
| Card gap | 12px (gap-3) | Between cards |
| Border radius | 12px (rounded-card) | Cards, buttons |

---

## Financial Amount Display

Always use `font-mono` for currency amounts with appropriate color:

```vue
<!-- Expense (negative) -->
<span class="font-mono font-semibold text-expense">-$82.14</span>

<!-- Income (positive) -->
<span class="font-mono font-semibold text-income">+$2,450.00</span>

<!-- Transfer (neutral) -->
<span class="font-mono font-semibold text-transfer">$500.00</span>

<!-- Available/Balance -->
<span class="font-mono font-semibold text-body">$4,250.00</span>
```

---

## Icons

Budget Guy uses inline SVG icons and emoji for category icons.

**Category Icons:** Emoji (stored in `categories.icon` field)

**UI Icons:** Inline SVG with current color:
```vue
<svg class="w-5 h-5 text-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <!-- path -->
</svg>
```
