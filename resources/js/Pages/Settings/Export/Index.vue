<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Button from '@/Components/Base/Button.vue';

const props = defineProps({
    budgetName: String,
    dateRange: Object,
    transactionCount: Number,
});

const format = ref('csv');
const exportType = ref('transactions');
const startDate = ref('');
const endDate = ref('');
const isExporting = ref(false);

const formatOptions = [
    { value: 'csv', label: 'CSV', description: 'Spreadsheet compatible' },
    { value: 'json', label: 'JSON', description: 'For developers' },
];

const typeOptions = [
    { value: 'transactions', label: 'Transactions Only', description: 'All your transaction data' },
    { value: 'budget', label: 'Budget Only', description: 'Monthly budget allocations' },
    { value: 'all', label: 'Full Export', description: 'Everything (transactions, budget, accounts, categories, payees)' },
];

const canExport = computed(() => {
    return props.transactionCount > 0 || exportType.value !== 'transactions';
});

const handleExport = () => {
    if (!canExport.value) return;

    isExporting.value = true;

    // Build the export URL with query params
    const params = new URLSearchParams({
        format: format.value,
        type: exportType.value,
    });

    if (startDate.value) params.append('start_date', startDate.value);
    if (endDate.value) params.append('end_date', endDate.value);

    // Trigger download by navigating to the export URL
    window.location.href = route('export.download') + '?' + params.toString();

    // Reset exporting state after a delay
    setTimeout(() => {
        isExporting.value = false;
    }, 2000);
};

const formatDateForInput = (dateStr) => {
    if (!dateStr) return '';
    return dateStr.split('T')[0];
};
</script>

<template>
    <Head title="Export Data" />

    <AppLayout>
        <template #title>Export Data</template>
        <template #header-left>
            <Link :href="route('settings.index')" class="p-2 -ml-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </Link>
        </template>

        <div class="p-4 space-y-6">
            <!-- Budget Info -->
            <div class="bg-surface rounded-card p-4">
                <h2 class="text-sm font-semibold text-subtle mb-2">Exporting from</h2>
                <p class="text-body font-medium">{{ budgetName }}</p>
                <p class="text-sm text-subtle mt-1">
                    {{ transactionCount }} transactions
                    <template v-if="dateRange.earliest && dateRange.latest">
                        ({{ formatDateForInput(dateRange.earliest) }} to {{ formatDateForInput(dateRange.latest) }})
                    </template>
                </p>
            </div>

            <!-- Export Format -->
            <div>
                <h2 class="text-sm font-semibold text-subtle uppercase tracking-wide px-1 mb-2">
                    Format
                </h2>
                <div class="bg-surface rounded-card divide-y divide-gray-100">
                    <label
                        v-for="option in formatOptions"
                        :key="option.value"
                        class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50"
                    >
                        <div>
                            <div class="text-body font-medium">{{ option.label }}</div>
                            <div class="text-sm text-subtle">{{ option.description }}</div>
                        </div>
                        <input
                            type="radio"
                            :value="option.value"
                            v-model="format"
                            class="w-5 h-5 text-primary focus:ring-primary"
                        />
                    </label>
                </div>
            </div>

            <!-- Export Type -->
            <div>
                <h2 class="text-sm font-semibold text-subtle uppercase tracking-wide px-1 mb-2">
                    What to Export
                </h2>
                <div class="bg-surface rounded-card divide-y divide-gray-100">
                    <label
                        v-for="option in typeOptions"
                        :key="option.value"
                        class="flex items-center justify-between p-4 cursor-pointer hover:bg-gray-50"
                    >
                        <div>
                            <div class="text-body font-medium">{{ option.label }}</div>
                            <div class="text-sm text-subtle">{{ option.description }}</div>
                        </div>
                        <input
                            type="radio"
                            :value="option.value"
                            v-model="exportType"
                            class="w-5 h-5 text-primary focus:ring-primary"
                        />
                    </label>
                </div>
            </div>

            <!-- Date Range (only for transactions) -->
            <div v-if="exportType === 'transactions' || exportType === 'all'">
                <h2 class="text-sm font-semibold text-subtle uppercase tracking-wide px-1 mb-2">
                    Date Range (Optional)
                </h2>
                <div class="bg-surface rounded-card overflow-hidden divide-y divide-gray-100">
                    <div class="flex items-center justify-between px-4 py-3.5">
                        <span class="text-sm text-subtle">From</span>
                        <input
                            type="date"
                            v-model="startDate"
                            :max="endDate || undefined"
                            :class="[
                                'bg-transparent text-right text-sm font-medium focus:outline-none',
                                startDate ? 'text-primary' : 'text-gray-400'
                            ]"
                        />
                    </div>
                    <div class="flex items-center justify-between px-4 py-3.5">
                        <span class="text-sm text-subtle">To</span>
                        <input
                            type="date"
                            v-model="endDate"
                            :min="startDate || undefined"
                            :class="[
                                'bg-transparent text-right text-sm font-medium focus:outline-none',
                                endDate ? 'text-primary' : 'text-gray-400'
                            ]"
                        />
                    </div>
                </div>
                <p class="text-xs text-subtle px-1 mt-2">
                    Leave empty to export all dates
                </p>
            </div>

            <!-- Export Button -->
            <Button
                @click="handleExport"
                :disabled="!canExport || isExporting"
                :loading="isExporting"
                full-width
                size="lg"
            >
                Export {{ format.toUpperCase() }}
            </Button>

            <!-- Empty State -->
            <div v-if="transactionCount === 0 && exportType === 'transactions'" class="text-center py-4">
                <p class="text-subtle text-sm">
                    No transactions to export. Add some transactions first.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
