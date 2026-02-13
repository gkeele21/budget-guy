<script setup>
import { Head, useForm, Link, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import SegmentedControl from '@/Components/Form/SegmentedControl.vue';
import AutocompleteField from '@/Components/Form/AutocompleteField.vue';
import AmountField from '@/Components/Form/AmountField.vue';
import PickerField from '@/Components/Form/PickerField.vue';
import DateField from '@/Components/Form/DateField.vue';
import Button from '@/Components/Base/Button.vue';

const props = defineProps({
    accounts: Array,
    categories: Array,
    payees: Array,
});

// Get query params for prefilling from a transaction
const query = new URLSearchParams(window.location.search);

// Parse IDs as integers for proper matching with PickerField
const parseIntOrDefault = (value, defaultValue) => {
    const parsed = parseInt(value, 10);
    return isNaN(parsed) ? defaultValue : parsed;
};

const form = useForm({
    type: query.get('type') || 'expense',
    amount: query.get('amount') || '',
    account_id: parseIntOrDefault(query.get('account_id'), props.accounts[0]?.id || ''),
    category_id: parseIntOrDefault(query.get('category_id'), ''),
    payee_name: query.get('payee_name') || '',
    frequency: 'monthly',
    next_date: new Date().toISOString().split('T')[0],
    end_date: '',
});

const selectPayee = (payee) => {
    form.payee_name = payee.name;
    if (payee.default_category_id) {
        form.category_id = payee.default_category_id;
    }
};

const submit = () => {
    form.post(route('recurring.store'));
};

const typeOptions = [
    { value: 'expense', label: 'Expense', color: 'expense' },
    { value: 'income', label: 'Income', color: 'income' },
];

const frequencyOptions = [
    { id: 'daily', name: 'Daily' },
    { id: 'weekly', name: 'Weekly' },
    { id: 'biweekly', name: 'Every 2 weeks' },
    { id: 'monthly', name: 'Monthly' },
    { id: 'yearly', name: 'Yearly' },
];

const endOptions = [
    { id: '', name: 'Never' },
    { id: 'custom', name: 'On date...' },
];

const showEndDatePicker = ref(false);

watch(() => form.end_date, (newValue) => {
    if (newValue && newValue !== '' && newValue !== 'custom') {
        showEndDatePicker.value = true;
    }
});

const getSaveButtonVariant = () => {
    return form.type === 'expense' ? 'expense' : 'income';
};
</script>

<template>
    <Head title="New Recurring" />

    <div class="min-h-screen bg-bg flex flex-col">
        <!-- Header with Cancel / Title / Save -->
        <div class="bg-surface border-b border-border px-4 py-3 safe-area-top">
            <div class="flex items-center justify-between">
                <Link
                    :href="route('recurring.index')"
                    class="text-subtle font-medium flex items-center gap-1"
                >
                    <span class="text-lg">×</span> Cancel
                </Link>
                <span class="font-semibold text-body">New Recurring</span>
                <button
                    @click="submit"
                    :disabled="form.processing"
                    :class="[
                        'font-semibold',
                        form.type === 'expense' ? 'text-expense' : 'text-income'
                    ]"
                >
                    Save
                </button>
            </div>
        </div>

        <form @submit.prevent="submit" class="flex-1 flex flex-col">
            <!-- Type Toggle -->
            <div class="mx-3 mt-3">
                <SegmentedControl
                    v-model="form.type"
                    :options="typeOptions"
                />
            </div>

            <!-- Compact Fields Card -->
            <div class="mx-3 mt-3 bg-surface rounded-xl overflow-hidden">
                <!-- Payee -->
                <AutocompleteField
                    v-model="form.payee_name"
                    label="Payee"
                    placeholder="Who is this for?"
                    :suggestions="payees"
                    @select="selectPayee"
                />

                <!-- Amount -->
                <AmountField
                    v-model="form.amount"
                    :transaction-type="form.type"
                    :error="form.errors.amount"
                />

                <!-- Account -->
                <PickerField
                    v-model="form.account_id"
                    label="Account"
                    :options="accounts"
                    placeholder="Select account"
                />

                <!-- Category -->
                <PickerField
                    v-model="form.category_id"
                    label="Category"
                    :options="categories"
                    placeholder="Select category"
                    grouped
                    group-items-key="categories"
                />

                <!-- Frequency -->
                <PickerField
                    v-model="form.frequency"
                    label="Frequency"
                    :options="frequencyOptions"
                    placeholder="Select frequency"
                />

                <!-- Next Date -->
                <DateField
                    v-model="form.next_date"
                    label="Next Date"
                />

                <!-- End -->
                <PickerField
                    v-if="!showEndDatePicker"
                    v-model="form.end_date"
                    label="End"
                    :options="endOptions"
                    placeholder="Never"
                    :border-bottom="false"
                    @update:model-value="(val) => { if (val === 'custom') showEndDatePicker = true; }"
                />
                <div v-else class="flex items-center justify-between px-4 py-3.5">
                    <span class="text-sm text-subtle">End</span>
                    <div class="flex items-center gap-2">
                        <input
                            v-model="form.end_date"
                            type="date"
                            class="text-right text-sm font-medium text-primary bg-transparent focus:outline-none"
                        />
                        <button
                            type="button"
                            @click="form.end_date = ''; showEndDatePicker = false;"
                            class="text-subtle text-lg leading-none"
                        >
                            ×
                        </button>
                    </div>
                </div>
            </div>

            <!-- Spacer -->
            <div class="flex-1"></div>

            <!-- Submit Button -->
            <div class="p-3 safe-area-bottom">
                <Button
                    type="submit"
                    :variant="getSaveButtonVariant()"
                    :loading="form.processing"
                    full-width
                    size="lg"
                >
                    Save Recurring
                </Button>
            </div>
        </form>
    </div>
</template>

<style scoped>
.safe-area-top {
    padding-top: max(12px, env(safe-area-inset-top));
}
.safe-area-bottom {
    padding-bottom: max(12px, env(safe-area-inset-bottom));
}
</style>
