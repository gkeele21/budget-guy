<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import SegmentedControl from '@/Components/Form/SegmentedControl.vue';
import AutocompleteField from '@/Components/Form/AutocompleteField.vue';
import AmountField from '@/Components/Form/AmountField.vue';
import PickerField from '@/Components/Form/PickerField.vue';
import DateField from '@/Components/Form/DateField.vue';
import Modal from '@/Components/Base/Modal.vue';
import Button from '@/Components/Base/Button.vue';
import BottomSheet from '@/Components/Base/BottomSheet.vue';

const props = defineProps({
    accounts: Array,
    categories: Array,
    payees: Array,
});

const query = new URLSearchParams(window.location.search);

const parseIntOrDefault = (value, defaultValue) => {
    const parsed = parseInt(value, 10);
    return isNaN(parsed) ? defaultValue : parsed;
};

const form = useForm({
    type: query.get('type') || 'expense',
    amount: query.get('amount') || '',
    account_id: parseIntOrDefault(query.get('account_id'), props.accounts[0]?.id || ''),
    categories: query.get('category_id')
        ? [{ category_id: parseIntOrDefault(query.get('category_id'), ''), amount: parseFloat(query.get('amount')) || 0 }]
        : [],
    payee_name: query.get('payee_name') || '',
    frequency: 'monthly',
    next_date: (() => { const d = new Date(); return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`; })(),
    end_date: '',
});

// Track single category for the PickerField display
const singleCategoryId = ref(form.categories.length === 1 ? form.categories[0].category_id : '');
const isSplit = ref(form.categories.length > 1);

// Sync single category changes back to form.categories
watch(singleCategoryId, (newVal) => {
    if (!isSplit.value) {
        form.categories = newVal
            ? [{ category_id: newVal, amount: parseFloat(form.amount) || 0 }]
            : [];
    }
});

// Keep single-category amount in sync with form amount
watch(() => form.amount, (newVal) => {
    if (!isSplit.value && form.categories.length === 1) {
        form.categories = [{ category_id: form.categories[0].category_id, amount: parseFloat(newVal) || 0 }];
    }
});

const selectPayee = (payee) => {
    form.payee_name = payee.name;
    if (payee.default_category_id && !isSplit.value) {
        singleCategoryId.value = payee.default_category_id;
    }
};

// When type changes via SegmentedControl, flip the amount sign to match
watch(() => form.type, (newType, oldType) => {
    if (!oldType || newType === oldType) return;
    const num = parseFloat(form.amount);
    if (isNaN(num) || num === 0) return;
    if (newType === 'expense' && num > 0) form.amount = (-num).toFixed(2);
    if (newType === 'income' && num < 0) form.amount = (-num).toFixed(2);
});

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

const flatCategories = computed(() => {
    const result = [];
    props.categories.forEach(group => {
        group.categories.forEach(cat => {
            result.push({ ...cat, groupName: group.name });
        });
    });
    return result;
});

const getSplitCategoryDisplay = (categoryId) => {
    if (!categoryId) return null;
    const cat = flatCategories.value.find(c => c.id === categoryId);
    return cat ? (cat.icon ? `${cat.icon} ${cat.name}` : cat.name) : null;
};

const selectSplitCategory = (index, categoryId) => {
    splitItems.value[index].category_id = categoryId;
    splitCategorySheetIndex.value = null;
    splitCategorySearch.value = '';
};

// Split transaction functions
const showSplitModal = ref(false);
const splitItems = ref([{ category_id: '', amount: '' }]);
const splitCategorySheetIndex = ref(null);
const splitCategorySearch = ref('');

const filteredSplitCategories = computed(() => {
    const query = splitCategorySearch.value.toLowerCase().trim();
    if (!query) return props.categories;
    return props.categories
        .map(group => ({
            ...group,
            categories: group.categories.filter(cat =>
                cat.name.toLowerCase().includes(query) || group.name.toLowerCase().includes(query)
            ),
        }))
        .filter(group => group.categories.length > 0);
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(Math.abs(amount));
};

const openSplitModal = () => {
    if (isSplit.value && form.categories.length > 0) {
        splitItems.value = form.categories.map(s => ({ ...s }));
    } else if (singleCategoryId.value && form.amount) {
        splitItems.value = [{ category_id: singleCategoryId.value, amount: form.amount }];
    } else {
        splitItems.value = [{ category_id: '', amount: '' }];
    }
    showSplitModal.value = true;
};

const addSplitItem = () => {
    splitItems.value.push({ category_id: '', amount: '' });
};

const removeSplitItem = (index) => {
    if (splitItems.value.length > 1) {
        splitItems.value.splice(index, 1);
    }
};

const totalSplitAmount = computed(() => {
    const sum = splitItems.value.reduce((s, item) => s + (parseFloat(item.amount) || 0), 0);
    return Math.abs(sum);
});

const remainingAmount = computed(() => {
    return Math.abs(parseFloat(form.amount) || 0) - totalSplitAmount.value;
});

const isSplitBalanced = computed(() => Math.abs(remainingAmount.value) < 0.01);

const splitItemColor = (item) => {
    const amt = parseFloat(item.amount);
    if (!isNaN(amt) && amt < 0) return 'expense';
    if (!isNaN(amt) && amt > 0) return 'income';
    return form.type;
};

const saveSplit = () => {
    const validSplits = splitItems.value.filter(s => {
        const amt = parseFloat(s.amount);
        if (isNaN(amt) || amt === 0) return false;
        return true;
    });

    if (validSplits.length === 0) {
        isSplit.value = false;
        form.categories = [];
        singleCategoryId.value = '';
    } else if (validSplits.length === 1) {
        isSplit.value = false;
        singleCategoryId.value = validSplits[0].category_id;
        form.categories = validSplits;
    } else {
        isSplit.value = true;
        singleCategoryId.value = '';
        form.categories = validSplits;
    }

    showSplitModal.value = false;
};

const clearSplit = () => {
    isSplit.value = false;
    form.categories = [];
    singleCategoryId.value = '';
    showSplitModal.value = false;
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
                    <span class="text-lg">&times;</span> Cancel
                </Link>
                <span class="font-semibold text-body">New Recurring</span>
                <button
                    @click="submit"
                    :disabled="form.processing"
                    :class="[
                        'font-semibold',
                        form.type === 'expense' ? 'text-danger' : 'text-success'
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
                <!-- Next Date -->
                <DateField
                    v-model="form.next_date"
                    label="Next Date"
                />

                <!-- Account -->
                <PickerField
                    v-model="form.account_id"
                    label="Account"
                    :options="accounts"
                    placeholder="Select account"
                />

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
                    label="Amount"
                    :transaction-type="form.type"
                    :error="form.errors.amount"
                />

                <!-- Category -->
                <template v-if="form.type !== 'transfer'">
                    <!-- Split display -->
                    <div v-if="isSplit" class="flex items-center justify-between px-4 py-3.5 border-b border-border">
                        <span class="text-sm text-subtle">Category</span>
                        <button
                            type="button"
                            @click="openSplitModal"
                            class="text-sm font-medium text-primary"
                        >
                            Split ({{ form.categories.length }}) &rarr;
                        </button>
                    </div>
                    <!-- Regular category picker -->
                    <PickerField
                        v-else
                        v-model="singleCategoryId"
                        label="Category"
                        :options="categories"
                        placeholder="Select category"
                        grouped
                        group-items-key="categories"
                        searchable
                        :action-option="{ label: 'Split Transaction...' }"
                        :null-option="{ label: 'Unassigned' }"
                        @action="openSplitModal"
                    />
                </template>

                <!-- Frequency -->
                <PickerField
                    v-model="form.frequency"
                    label="Frequency"
                    :options="frequencyOptions"
                    placeholder="Select frequency"
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
                <DateField
                    v-else
                    v-model="form.end_date"
                    label="End"
                    :border-bottom="false"
                    clearable
                    @clear="showEndDatePicker = false"
                />
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

        <!-- Split Transaction Modal -->
        <Modal :show="showSplitModal" title="Split Transaction" @close="showSplitModal = false">
            <div class="px-4 pb-2 text-sm text-subtle">
                Total: {{ formatCurrency(parseFloat(form.amount) || 0) }}
            </div>

            <div class="flex-1 overflow-y-auto">
                <div class="bg-surface mx-3 rounded-xl overflow-hidden">
                    <div
                        v-for="(item, index) in splitItems"
                        :key="index"
                        class="flex items-center px-4 py-3.5 border-b border-border last:border-b-0"
                    >
                        <div class="flex-1 min-w-0">
                            <button
                                type="button"
                                @click="splitCategorySheetIndex = index"
                                class="flex items-center gap-1 text-sm font-medium"
                                :class="item.category_id ? 'text-secondary' : 'text-subtle'"
                            >
                                <span class="truncate">{{ getSplitCategoryDisplay(item.category_id) || 'Select category' }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-subtle shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </div>
                        <AmountField
                            v-model="item.amount"
                            :transaction-type="splitItemColor(item)"
                            allow-negative
                            class="w-20 ml-3"
                        />
                        <button
                            type="button"
                            @click="removeSplitItem(index)"
                            class="ml-2 p-1 text-border-strong hover:text-danger transition-colors"
                            :class="{ 'opacity-30 pointer-events-none': splitItems.length <= 1 }"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button
                    type="button"
                    @click="addSplitItem"
                    class="w-full mt-3 mx-3 py-3 text-sm font-medium text-primary"
                >
                    + Add Category
                </button>
            </div>

            <!-- Remaining indicator -->
            <div class="px-4 py-3 border-t border-border">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-subtle">Remaining</span>
                    <span
                        class="font-semibold"
                        :class="{
                            'text-danger': !isSplitBalanced,
                            'text-success': isSplitBalanced,
                        }"
                    >
                        {{ formatCurrency(remainingAmount) }}
                    </span>
                </div>
                <div class="mt-2 h-2 bg-border rounded-full overflow-hidden">
                    <div
                        class="h-full transition-all duration-300"
                        :class="{
                            'bg-danger': !isSplitBalanced,
                            'bg-success': isSplitBalanced,
                        }"
                        :style="{ width: `${Math.min(100, (totalSplitAmount / (Math.abs(parseFloat(form.amount)) || 1)) * 100)}%` }"
                    ></div>
                </div>
            </div>

            <template #footer>
                <div class="flex gap-2">
                    <Button variant="secondary" @click="clearSplit" class="flex-1">Cancel</Button>
                    <Button :disabled="!isSplitBalanced" @click="saveSplit" class="flex-1">Save Split</Button>
                </div>
            </template>
        </Modal>

        <!-- Split Category Picker -->
        <BottomSheet :show="splitCategorySheetIndex !== null" title="Category" @close="splitCategorySheetIndex = null; splitCategorySearch = ''">
            <div class="px-4 pt-3 pb-2 border-b border-border">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-subtle" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input
                        v-model="splitCategorySearch"
                        type="text"
                        placeholder="Search..."
                        class="w-full pl-9 pr-8 py-2 text-sm bg-surface-inset border border-border rounded-lg text-body placeholder:text-subtle focus-glow"
                    />
                    <button
                        v-if="splitCategorySearch"
                        type="button"
                        @click="splitCategorySearch = ''"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-subtle hover:text-body"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <div class="py-2">
                <button
                    v-if="!splitCategorySearch"
                    type="button"
                    @click="selectSplitCategory(splitCategorySheetIndex, null)"
                    class="w-full px-4 py-3 text-left text-sm hover:bg-surface-overlay flex items-center justify-between border-b border-border"
                    :class="splitCategorySheetIndex !== null && !splitItems[splitCategorySheetIndex]?.category_id ? 'text-secondary font-medium' : 'text-body'"
                >
                    <span>Unassigned</span>
                    <svg v-if="splitCategorySheetIndex !== null && !splitItems[splitCategorySheetIndex]?.category_id" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-secondary" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div v-for="group in filteredSplitCategories" :key="group.name">
                    <div class="px-4 py-2 text-xs font-semibold text-subtle uppercase tracking-wide bg-surface-header">
                        {{ group.name }}
                    </div>
                    <button
                        v-for="cat in group.categories"
                        :key="cat.id"
                        type="button"
                        @click="selectSplitCategory(splitCategorySheetIndex, cat.id)"
                        class="w-full px-4 py-3 text-left text-sm hover:bg-surface-overlay flex items-center justify-between"
                        :class="splitCategorySheetIndex !== null && splitItems[splitCategorySheetIndex]?.category_id === cat.id ? 'text-secondary font-medium' : 'text-body'"
                    >
                        <span>{{ cat.icon ? `${cat.icon} ${cat.name}` : cat.name }}</span>
                        <svg v-if="splitCategorySheetIndex !== null && splitItems[splitCategorySheetIndex]?.category_id === cat.id" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-secondary" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </BottomSheet>
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
