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

const getAccountIcon = (type) => {
    const icons = {
        checking: '🏦',
        savings: '💰',
        credit_card: '💳',
        cash: '💵',
    };
    return icons[type] || '💳';
};

// Account type colors map to our 5-color palette
const getAccountBorderColor = (type) => {
    const colors = {
        cash: 'border-l-primary',       // Brand Green
        checking: 'border-l-secondary', // Brand Blue
        savings: 'border-l-income',     // Income Green
        credit_card: 'border-l-expense', // Expense Red
    };
    return colors[type] || 'border-l-secondary';
};

const unclearedBalance = computed(() => {
    return props.account.balance - props.account.cleared_balance;
});

const hasUncleared = computed(() => {
    return Math.abs(unclearedBalance.value) >= 0.01;
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
            <span class="text-2xl">{{ getAccountIcon(account.type) }}</span>
            <div>
                <div class="font-medium text-body">{{ account.name }}</div>
                <div v-if="hasUncleared" class="text-xs text-expense">
                    {{ formatCurrency(unclearedBalance) }} pending
                </div>
            </div>
        </div>
        <div class="text-right">
            <div
                class="font-semibold"
                :class="account.balance >= 0 ? 'text-body' : 'text-expense'"
            >
                {{ formatCurrency(account.balance) }}
            </div>
            <div v-if="hasUncleared" class="text-xs text-subtle">
                {{ formatCurrency(account.cleared_balance) }}
            </div>
        </div>
    </Link>
</template>
