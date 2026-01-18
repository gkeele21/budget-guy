<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import AccountCard from '@/Components/Domain/AccountCard.vue';
import FAB from '@/Components/Domain/FAB.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    accounts: {
        type: Object,
        required: true,
    },
    budgetName: {
        type: String,
        required: true,
    },
});

const accountTypeLabels = {
    cash: 'Cash',
    checking: 'Checking',
    savings: 'Savings',
    credit_card: 'Credit Cards',
};

const accountTypeOrder = ['cash', 'checking', 'savings', 'credit_card'];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <template #title>{{ budgetName }}</template>

        <div class="p-4 space-y-4">
            <h1 class="text-lg font-semibold text-body">Accounts</h1>

            <!-- Accounts by Type -->
            <div v-for="type in accountTypeOrder" :key="type">
                <div v-if="accounts[type] && accounts[type].length > 0" class="space-y-2">
                    <h2 class="text-sm font-semibold text-subtle uppercase tracking-wide px-1">
                        {{ accountTypeLabels[type] }}
                    </h2>
                    <div class="space-y-2">
                        <AccountCard
                            v-for="account in accounts[type]"
                            :key="account.id"
                            :account="account"
                        />
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-if="Object.keys(accounts).length === 0"
                class="text-center py-12"
            >
                <div class="text-4xl mb-4">🏦</div>
                <h3 class="text-lg font-medium text-body mb-2">No accounts yet</h3>
                <p class="text-subtle">
                    Tap the settings icon above to add your accounts.
                </p>
            </div>
        </div>

        <template v-if="Object.keys(accounts).length > 0" #fab>
            <FAB :href="route('transactions.create')" />
        </template>
    </AppLayout>
</template>
