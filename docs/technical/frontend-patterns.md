# Frontend Patterns

## Vue 3 Composition API

All components use the Composition API with `<script setup>`:

```vue
<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';

// Props
const props = defineProps({
    transactions: Array,
});

// Reactive state
const search = ref('');

// Computed
const filtered = computed(() =>
    props.transactions.filter(t => t.payee.name.includes(search.value))
);

// Form handling
const form = useForm({
    amount: '',
    category_id: null,
});

const submit = () => {
    form.post(route('transactions.store'));
};
</script>
```

---

## Inertia.js Patterns

### Receiving Props

```vue
<script setup>
const props = defineProps({
    budget: Object,
    categories: Array,
    monthlyBudgets: Object,
});
</script>
```

### Navigation

```vue
import { router } from '@inertiajs/vue3';

// Programmatic navigation
router.visit(route('budget.index'));

// With Link component
import { Link } from '@inertiajs/vue3';
<Link :href="route('transactions.edit', transaction.id)">Edit</Link>
```

### Form Submission

```vue
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    amount: '',
    payee_id: null,
    category_id: null,
    date: new Date().toISOString().split('T')[0],
});

// POST
form.post(route('transactions.store'), {
    onSuccess: () => form.reset(),
});

// PUT
form.put(route('transactions.update', props.transaction.id));

// DELETE
form.delete(route('transactions.destroy', props.transaction.id));
```

### Preserving State on Navigation

```vue
router.visit(url, {
    preserveState: true,
    preserveScroll: true,
});
```

---

## Component Patterns

### Page Structure

```vue
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/Base/Button.vue';

defineProps({
    data: Object,
});
</script>

<template>
    <AppLayout title="Page Title">
        <!-- Page content -->
        <div class="p-4 space-y-4">
            <!-- Content here -->
        </div>
    </AppLayout>
</template>
```

### Form Page Structure

```vue
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/Base/Button.vue';
import TextField from '@/Components/Form/TextField.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    amount: '',
});

const submit = () => {
    form.post(route('things.store'));
};
</script>

<template>
    <AppLayout title="Add Thing">
        <form @submit.prevent="submit" class="p-4 space-y-4">
            <TextField
                v-model="form.name"
                label="Name"
                :error="form.errors.name"
            />

            <Button
                type="submit"
                variant="primary"
                :fullWidth="true"
                :loading="form.processing"
            >
                Save
            </Button>
        </form>
    </AppLayout>
</template>
```

---

## Form Component Usage

### TextField

```vue
<TextField
    v-model="form.name"
    label="Payee Name"
    placeholder="Enter name"
    :error="form.errors.name"
/>
```

### AmountField

```vue
<AmountField
    v-model="form.amount"
    label="Amount"
    :type="form.type"  <!-- 'expense', 'income', or 'transfer' -->
    :error="form.errors.amount"
/>
```

### PickerField

```vue
<PickerField
    v-model="form.category_id"
    label="Category"
    :options="categories"
    option-label="name"
    option-value="id"
    placeholder="Select category"
    :error="form.errors.category_id"
/>
```

### SegmentedControl

```vue
<SegmentedControl
    v-model="form.type"
    :options="[
        { value: 'expense', label: 'Expense' },
        { value: 'income', label: 'Income' },
        { value: 'transfer', label: 'Transfer' },
    ]"
/>
```

### DateField

```vue
<DateField
    v-model="form.date"
    label="Date"
    :error="form.errors.date"
/>
```

### ToggleField

```vue
<ToggleField
    v-model="form.cleared"
    label="Cleared"
/>
```

---

## Common Patterns

### Conditional Rendering

```vue
<!-- v-if for presence -->
<div v-if="transaction.memo">{{ transaction.memo }}</div>

<!-- v-show for frequent toggles -->
<div v-show="isExpanded">Details...</div>
```

### List Rendering

```vue
<div v-for="transaction in transactions" :key="transaction.id">
    {{ transaction.payee.name }}
</div>
```

### Event Handling

```vue
<button @click="handleClick">Click</button>
<button @click.prevent="handleSubmit">Submit</button>
<input @input="handleInput" @blur="handleBlur" />
```

### Two-Way Binding with Components

```vue
<!-- Parent -->
<ChildComponent v-model="value" />

<!-- Child -->
<script setup>
const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue']);

const updateValue = (newValue) => {
    emit('update:modelValue', newValue);
};
</script>
```

---

## State Management

### Local State (ref/reactive)

```vue
const isOpen = ref(false);
const filters = reactive({
    account: null,
    search: '',
});
```

### Computed Properties

```vue
const total = computed(() =>
    transactions.value.reduce((sum, t) => sum + t.amount, 0)
);

const formattedTotal = computed(() =>
    new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(total.value)
);
```

### Watchers

```vue
import { watch } from 'vue';

watch(
    () => props.categoryId,
    (newId) => {
        loadTransactions(newId);
    }
);
```

---

## Currency Formatting

```vue
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};

// Usage
<span class="font-mono">{{ formatCurrency(transaction.amount) }}</span>
```

---

## Bottom Sheet Pattern

```vue
<script setup>
import BottomSheet from '@/Components/Base/BottomSheet.vue';

const isOpen = ref(false);
const selected = ref(null);

const select = (item) => {
    selected.value = item;
    isOpen.value = false;
};
</script>

<template>
    <button @click="isOpen = true">
        {{ selected?.name || 'Select...' }}
    </button>

    <BottomSheet :show="isOpen" title="Select Item" @close="isOpen = false">
        <div v-for="item in items" :key="item.id" @click="select(item)">
            {{ item.name }}
        </div>
    </BottomSheet>
</template>
```

---

## Mobile-First Responsive

Budget Guy is mobile-first. Use Tailwind breakpoints for larger screens:

```vue
<!-- Mobile (default) -->
<div class="p-4">

<!-- Tablet and up -->
<div class="md:p-6">

<!-- Desktop and up -->
<div class="lg:max-w-2xl lg:mx-auto">
```

Most pages don't need responsive variants since the app is designed for mobile.

---

## Error Handling

### Form Errors

```vue
<TextField
    v-model="form.name"
    :error="form.errors.name"
/>

<!-- Or manually -->
<InputError :message="form.errors.name" />
```

### Global Error Display

Form errors are typically displayed inline with each field using the `:error` prop.
