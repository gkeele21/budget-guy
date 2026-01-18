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
        class="flex items-start justify-between p-4 bg-surface rounded-card hover:bg-gray-50 transition-colors"
    >
        <div class="flex items-start gap-3">
            <span class="text-2xl">{{ getAccountIcon(account.type) }}</span>
            <div>
                <div class="font-medium text-body">{{ account.name }}</div>
                <div v-if="hasUncleared" class="text-xs text-yellow-600">
                    {{ formatCurrency(unclearedBalance) }} pending
                </div>
            </div>
        </div>
        <div class="text-right">
            <div
                class="font-mono font-semibold"
                :class="account.balance >= 0 ? 'text-body' : 'text-expense'"
            >
                {{ formatCurrency(account.balance) }}
            </div>
            <div v-if="hasUncleared" class="font-mono text-xs text-gray-400">
                {{ formatCurrency(account.cleared_balance) }}
            </div>
        </div>
    </Link>
</template>
