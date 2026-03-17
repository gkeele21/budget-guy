<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    account: {
        type: Object,
        required: true,
    },
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};

const getAccountIcon = (account) => {
    if (account.icon) return account.icon;
    const icons = {
        bank: '🏦',
        cash: '💵',
        credit: '💳',
    };
    return icons[account.type] || '💳';
};

// Account type colors map to our 5-color palette
const getAccountBorderColor = (type) => {
    const colors = {
        bank: 'border-l-secondary',     // Brand Blue
        cash: 'border-l-primary',       // Brand Green
        credit: 'border-l-danger',      // Expense Red
    };
    return colors[type] || 'border-l-secondary';
};

const unclearedBalance = computed(() => {
    return props.account.balance - props.account.cleared_balance;
});

const hasUncleared = computed(() => {
    return props.account.type !== 'cash' && Math.abs(unclearedBalance.value) >= 0.01;
});

const clearedLabel = computed(() => {
    return props.account.type === 'credit' ? 'Cleared' : 'Bank Cleared';
});
</script>

<template>
    <Link
        :href="route('transactions.index', { account: account.id })"
        :class="[
            'flex items-start justify-between p-4 bg-surface rounded-card hover:bg-surface-overlay transition-colors',
            'border-l-4',
            getAccountBorderColor(account.type)
        ]"
    >
        <div class="flex items-start gap-3">
            <span class="text-2xl">{{ getAccountIcon(account) }}</span>
            <div>
                <div class="font-medium text-body">{{ account.name }}</div>
                <div v-if="hasUncleared" class="text-xs text-danger">
                    {{ formatCurrency(unclearedBalance) }} pending
                </div>
            </div>
        </div>
        <div class="text-right">
            <div
                class="font-semibold"
                :class="account.balance >= 0 ? 'text-body' : 'text-danger'"
            >
                {{ formatCurrency(account.balance) }}
            </div>
            <div v-if="hasUncleared" class="text-xs text-subtle">
                {{ clearedLabel }}: {{ formatCurrency(account.cleared_balance) }}
            </div>
        </div>
    </Link>
</template>
