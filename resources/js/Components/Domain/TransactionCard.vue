<script setup>
const props = defineProps({
    transaction: { type: Object, required: true },
    // When true, shows a danger × dismiss button in the top-right corner
    dismissable: { type: Boolean, default: false },
    // Show account name below amount (hide when already filtered to one account)
    showAccount: { type: Boolean, default: true },
    // Optional highlight ring (e.g. after voice creation)
    highlighted: { type: Boolean, default: false },
});

const emit = defineEmits(['click', 'toggle-cleared', 'dismiss']);

const formatAmount = (tx) => {
    const abs = Math.abs(tx.amount).toFixed(2);
    if (tx.type === 'transfer') return `$${abs}`;
    return tx.amount < 0 ? `-$${abs}` : `+$${abs}`;
};

const amountColor = (type) => {
    if (type === 'expense') return 'text-danger';
    if (type === 'income') return 'text-success';
    return 'text-info';
};
</script>

<template>
    <div
        class="relative bg-surface rounded-card p-3 shadow-sm border-l-4 cursor-pointer select-none"
        :class="{
            'border-danger': transaction.type === 'expense',
            'border-success': transaction.type === 'income',
            'border-info': transaction.type === 'transfer',
            'ring-2 ring-primary/40': highlighted,
        }"
        @click="$emit('click')"
    >
        <!-- Dismiss × (top-right corner) -->
        <button
            v-if="dismissable"
            @click.stop="$emit('dismiss')"
            class="absolute top-1.5 right-1.5 w-5 h-5 flex items-center justify-center text-danger rounded"
            aria-label="Remove"
        >
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="flex items-start justify-between" :class="{ 'pr-4': dismissable }">
            <!-- Left: Payee + Category/Splits/Memo -->
            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-1.5">
                    <span class="font-medium text-body truncate">
                        <template v-if="transaction.type === 'transfer'">
                            <span class="text-info">↔</span>
                            {{ transaction.payee }}
                        </template>
                        <template v-else>{{ transaction.payee }}</template>
                    </span>
                    <span v-if="transaction.recurring_id" class="text-primary text-xs flex-shrink-0">↻</span>
                </div>

                <!-- Split categories -->
                <div v-if="transaction.is_split && transaction.splits" class="mt-0.5 grid grid-cols-[auto_auto] gap-x-1 gap-y-0.5 text-xs text-subtle w-fit">
                    <template v-for="(split, i) in transaction.splits" :key="i">
                        <span>{{ split.category || 'Unassigned' }}:</span>
                        <span>${{ Math.abs(split.amount).toFixed(2) }}</span>
                    </template>
                </div>

                <!-- Single category -->
                <div v-else-if="transaction.category" class="text-xs text-subtle mt-0.5 truncate">
                    {{ transaction.category }}
                </div>

                <!-- Unassigned / Income label -->
                <div
                    v-else-if="transaction.type !== 'transfer' && !transaction.is_split"
                    class="text-xs mt-0.5 truncate italic"
                    :class="transaction.type === 'income' ? 'text-subtle' : 'text-warning'"
                >
                    {{ transaction.type === 'income' ? 'Income' : 'Unassigned' }}
                </div>

                <!-- Memo -->
                <div v-if="transaction.memo" class="text-xs text-muted mt-0.5 truncate italic">
                    {{ transaction.memo }}
                </div>
            </div>

            <!-- Right: Amount + Account + Cleared dot -->
            <div class="flex items-start gap-2 flex-shrink-0 ml-3">
                <div class="text-right">
                    <div :class="['font-medium', amountColor(transaction.type)]">
                        {{ formatAmount(transaction) }}
                    </div>
                    <div v-if="showAccount && transaction.account && transaction.type !== 'transfer'" class="text-xs text-subtle mt-0.5">
                        {{ transaction.account }}
                    </div>
                </div>

                <!-- Cleared dot -->
                <button
                    @click.stop="$emit('toggle-cleared')"
                    class="flex-shrink-0 p-1 mt-0.5"
                    :aria-label="transaction.cleared ? 'Mark uncleared' : 'Mark cleared'"
                >
                    <div v-if="transaction.cleared" class="w-2 h-2 rounded-full bg-success"></div>
                    <div v-else class="w-2 h-2 rounded-full border-[1.5px] border-subtle"></div>
                </button>
            </div>
        </div>
    </div>
</template>
