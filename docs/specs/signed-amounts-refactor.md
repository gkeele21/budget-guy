# Spec: Signed Amounts Refactor

## Problem

Transaction amounts are stored signed in the DB (negative for expenses, positive for income), but the form layer strips the sign and re-applies it:

```
Form: user enters "50" (always positive)
  -> Controller: -abs(50) = -50 for expenses
  -> DB: -50
  -> Controller edit(): abs(-50) = 50
  -> Form: shows "50" again
```

This creates unnecessary complexity:
- Controller must negate on store and abs() on edit
- Split amounts already use signed values (inconsistent with main amount)
- Recurring transactions store unsigned, then negate when creating actual transactions
- The `type` field does double duty (UI label + sign logic)
- Can't tap the amount field to flip expense/income (sign isn't in the value)

## Proposal

Make the form work with signed amounts directly. The amount field shows what's actually stored.

```
Form: user enters "-50" (red) or "50" (green)
  -> Controller: stores as-is
  -> DB: -50
  -> Controller edit(): sends -50 as-is
  -> Form: shows "-$50.00" (red)
```

## Current State (What Changes)

### 1. TransactionController.php

**store() - Currently:**
```php
if ($validated['type'] === 'expense') {
    $amount = -abs($amount);
} elseif ($validated['type'] === 'income') {
    $amount = abs($amount);
}
// Transfers: from = -abs(), to = abs()
```

**store() - After:**
```php
// Amount arrives already signed from frontend
$amount = (float) $validated['amount'];
// Transfers: from gets the negative, to gets the positive
// (frontend sends negative for "from" side)
```

**edit() - Currently:**
```php
'amount' => abs((float) $transaction->amount),
```

**edit() - After:**
```php
'amount' => (float) $transaction->amount,
```

**update() - Same changes as store().**

### 2. RecurringTransactionController.php

**store()/update() - Currently:**
- Stores amount unsigned (positive)

**After:**
- Stores amount signed (negative for expenses)

**createTransactionFromRecurring() - Currently:**
```php
$amount = $recurring->type === 'expense'
    ? -abs($recurring->amount)
    : abs($recurring->amount);
```

**After:**
```php
$amount = (float) $recurring->amount; // already signed
```

### 3. Frontend Forms (Create.vue / Edit.vue for both Transactions and Recurring)

**Amount field - Currently:**
- Always shows positive value
- `transactionType` prop controls color only
- Validation: `amount >= 0.01`

**After:**
- Shows signed value: `-$50.00` (red) for expense, `$50.00` (green) for income
- Tapping the amount toggles the sign (already built for splits!)
- Color derived from the value's sign, not from `form.type`
- Validation: `amount != 0` (can be negative)
- When user switches type via SegmentedControl, negate the amount to match

**Split amounts - no change needed.** They already work this way.

### 4. Form-to-Type Sync

When the amount sign changes (via tap toggle), sync `form.type`:
```js
// Amount went negative -> switch to expense
// Amount went positive -> switch to income
watch(() => form.amount, (newVal) => {
    const num = parseFloat(newVal);
    if (!isNaN(num) && num < 0 && form.type === 'income') {
        form.type = 'expense';
    } else if (!isNaN(num) && num > 0 && form.type === 'expense') {
        form.type = 'income';
    }
});
```

When type changes via SegmentedControl, flip the amount sign:
```js
watch(() => form.type, (newType, oldType) => {
    if (oldType && newType !== oldType && form.amount) {
        const num = parseFloat(form.amount);
        if (newType === 'expense' && num > 0) form.amount = (-num).toFixed(2);
        if (newType === 'income' && num < 0) form.amount = (-num).toFixed(2);
    }
});
```

### 5. AmountField Component

**Currently:** `allowNegative` prop enables tap-to-toggle (splits only).

**After:** Add a `signToggle` prop (or reuse `allowNegative`) for the labeled variant too. Tapping the amount while editing toggles the sign. This is already implemented for the bare inline variant.

The `colorClass` computation already handles this correctly - it uses `transactionType` prop. The parent just needs to update the prop when the sign changes.

### 6. Backend Validation

**Currently:**
```php
'amount' => 'required|numeric|min:0.01',
```

**After:**
```php
'amount' => 'required|numeric|not_in:0',
// Or keep min:0.01 and let frontend handle sign,
// then controller just checks: if expense, amount must be negative
```

**Simpler approach:** Keep validation as `min:0.01` but apply it to `abs(amount)`:
```php
$rules['amount'] = 'required|numeric';
// Then in controller:
if (abs($validated['amount']) < 0.01) abort(422);
```

### 7. Transfer Handling

Transfers are trickier - they have a "from" (negative) and "to" (positive) transaction.

**Currently:** User enters positive amount, controller creates -amount for "from" and +amount for "to".

**After:** The form amount represents the outflow (negative). Controller uses it directly for "from" and negates for "to":
```php
$fromAmount = (float) $validated['amount']; // negative from form
$toAmount = -$fromAmount; // positive
```

Or alternatively, keep transfers using positive amounts since the from/to accounts already imply direction. The sign toggle wouldn't apply to transfers (they're neither expense nor income).

**Recommendation:** Don't change transfer handling. Only apply signed amounts to expense/income. Transfers keep current behavior.

## Files to Change

| File | Change |
|------|--------|
| `AmountField.vue` | Enable `allowNegative` / tap-toggle for labeled variant |
| `Transactions/Create.vue` | Send signed amount, add type/amount sync watchers |
| `Transactions/Edit.vue` | Display signed amount, same watchers |
| `Recurring/Create.vue` | Send signed amount, same watchers |
| `Recurring/Edit.vue` | Display signed amount, same watchers |
| `TransactionController.php` | Remove negation in store/update, remove abs() in edit |
| `RecurringTransactionController.php` | Store signed, simplify createTransactionFromRecurring |
| Backend validation | Allow negative amounts |

## What Does NOT Change

- **Database values** - already signed, no migration needed
- **Split transactions** - already use signed amounts
- **Category.getSpentForMonth()** - already negates the sum, works with signed values
- **BudgetController calculations** - already handle signed amounts
- **Transfer handling** - keep using positive + controller negation (direction implied by from/to)

## Migration Strategy

### Recurring transactions need a data fix:
Existing recurring transactions store unsigned amounts. Need a migration to negate expense recurring amounts:

```php
RecurringTransaction::where('type', 'expense')->each(function ($r) {
    $r->update(['amount' => -abs($r->amount)]);
    // Also negate category split amounts in JSON
    if ($r->categories) {
        $cats = collect($r->categories)->map(function ($c) {
            $c['amount'] = -abs($c['amount']);
            return $c;
        });
        $r->update(['categories' => $cats]);
    }
});
```

### Existing transactions: no change needed (already signed correctly).

## Risk Assessment

**Low risk:**
- Database already stores signed values
- Split amounts already work this way
- Category/budget calculations already handle signs

**Medium risk:**
- Recurring transaction data migration (must negate existing amounts)
- Transfer edge cases (keep transfers unchanged to avoid complexity)
- Any external code reading amounts that assumes positive (grep for abs() usage)

**Testing checklist:**
- [ ] Create expense transaction -> DB has negative amount
- [ ] Create income transaction -> DB has positive amount
- [ ] Edit expense -> shows negative in form, red color
- [ ] Edit income -> shows positive in form, green color
- [ ] Tap amount to flip sign -> type switches, color switches
- [ ] Switch type via SegmentedControl -> amount sign flips
- [ ] Split expense -> split amounts are negative
- [ ] Create recurring expense -> stored negative
- [ ] Recurring creates transaction -> amount stays negative (no double-negate)
- [ ] Budget page shows correct spent/available after refactor
- [ ] Transfer still works (unchanged)
- [ ] Existing data displays correctly after migration
