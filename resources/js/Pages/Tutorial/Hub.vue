<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    hasCompletedLearn: Boolean,
    hasCompletedSetup: Boolean,
    hasCompletedPlan: Boolean,
    hasCompletedTransactions: Boolean,
    hasCompletedSplits: Boolean,
    hasCompletedRecurring: Boolean,
    hasBudget: Boolean,
});

const allCards = [
    {
        key: 'learn',
        emoji: '🎓',
        iconClasses: 'bg-success/15 border border-success/30',
        title: 'Learn Budgeting',
        subtitle: 'Try it with sample data',
        description: 'Practice with a pre-built budget. Learn how envelope budgeting works by assigning money and seeing how spending works.',
        tag: 'Interactive',
        tagClasses: 'bg-success/15 text-success',
        time: '~3 minutes',
        action: () => router.post('/tutorial/learn'),
    },
    {
        key: 'setup',
        emoji: '🚀',
        iconClasses: 'bg-info/15 border border-info/30',
        title: 'Set Up My Budget',
        subtitle: "I'm ready to start for real",
        description: "Create your accounts, categories, and first month's budget step by step.",
        tag: 'Guided Setup',
        tagClasses: 'bg-info/15 text-info',
        time: '~10 minutes',
        action: () => router.post('/tutorial/setup'),
    },
];

const tourCards = [
    {
        key: 'transactions',
        emoji: '💳',
        iconClasses: 'bg-expense/15 border border-expense/30',
        title: 'Add a Transaction',
        subtitle: 'Record money in and out',
        description: 'Step-by-step walkthrough of recording an expense, income, or transfer.',
        tag: 'Guided Tour',
        tagClasses: 'bg-info/15 text-info',
        time: '~2 minutes',
        action: () => router.post('/tutorial/transactions'),
    },
    {
        key: 'splits',
        emoji: '✂️',
        iconClasses: 'bg-secondary/15 border border-secondary/30',
        title: 'Split Transactions',
        subtitle: 'Divide one purchase across categories',
        description: 'Learn how to split a single transaction — like a grocery run — across multiple envelopes.',
        tag: 'Guided Tour',
        tagClasses: 'bg-info/15 text-info',
        time: '~2 minutes',
        action: () => router.post('/tutorial/splits'),
    },
    {
        key: 'recurring',
        emoji: '🔁',
        iconClasses: 'bg-info/15 border border-info/30',
        title: 'Recurring Bills',
        subtitle: 'Set up repeating transactions',
        description: 'Set up rent, subscriptions, and paychecks so Budget Guy tracks what\'s coming up.',
        tag: 'Guided Tour',
        tagClasses: 'bg-info/15 text-info',
        time: '~2 minutes',
        action: () => router.post('/tutorial/recurring'),
    },
    {
        key: 'plan',
        emoji: '📅',
        iconClasses: 'bg-primary/15 border border-primary/30',
        title: 'Plan Your Budget',
        subtitle: 'Project next month before it starts',
        description: 'Learn how to use the Plan page to forecast income and spending category by category.',
        tag: 'Guided Tour',
        tagClasses: 'bg-info/15 text-info',
        time: '~2 minutes',
        action: () => router.post('/tutorial/plan'),
    },
];

const cards = computed(() => allCards);

const isCompleted = (key) => {
    if (key === 'learn') return props.hasCompletedLearn;
    if (key === 'setup') return props.hasCompletedSetup;
    if (key === 'plan') return props.hasCompletedPlan;
    if (key === 'transactions') return props.hasCompletedTransactions;
    if (key === 'splits') return props.hasCompletedSplits;
    if (key === 'recurring') return props.hasCompletedRecurring;
    return false;
};
</script>

<template>
    <Head title="Get Started" />

    <AppLayout>
        <template #title>Help & Tutorials</template>
        <template #header-left>
            <Link :href="route('settings.index')" class="p-2 -ml-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </Link>
        </template>

    <!-- Hero Section -->
    <div class="pt-8 pb-6 px-6 text-center">
        <div class="mx-auto mb-4 w-[100px] h-[100px] rounded-full border-2 border-primary overflow-hidden shadow-[0_0_20px_rgba(var(--color-primary),0.3)]">
            <img src="/images/Avatar.png" alt="Budget Guy" class="w-full h-full object-cover" />
        </div>
        <h2 class="text-xl font-bold text-body mb-1">Welcome to Budget Guy!</h2>
        <p class="text-sm text-muted">
            I'm here to help you take control of your money. How would you like to get started?
        </p>
    </div>

    <!-- Tutorial Cards -->
    <div class="px-4 pt-2 pb-1">
        <h3 class="text-xs font-semibold text-subtle uppercase tracking-wide">Get Started</h3>
    </div>
    <div class="flex flex-col gap-3 px-4 pb-4">
        <button
            v-for="card in cards"
            :key="card.key"
            @click="card.action()"
            class="w-full text-left bg-surface rounded-xl p-5 border border-border active:opacity-80 transition-opacity"
        >
            <div class="flex items-center gap-3 mb-2">
                <div
                    class="w-11 h-11 rounded-lg flex items-center justify-center text-xl flex-shrink-0"
                    :class="card.iconClasses"
                >
                    {{ card.emoji }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-body">{{ card.title }}</div>
                    <div class="text-xs text-muted">{{ card.subtitle }}</div>
                </div>
                <div
                    v-if="isCompleted(card.key)"
                    class="flex items-center gap-1 text-xs font-medium text-success flex-shrink-0"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Completed
                </div>
            </div>
            <p class="text-sm text-muted mb-3">{{ card.description }}</p>
            <div class="flex items-center gap-2">
                <span
                    class="text-xs font-medium px-2 py-0.5 rounded-full"
                    :class="card.tagClasses"
                >
                    {{ card.tag }}
                </span>
                <span class="text-xs text-subtle">{{ card.time }}</span>
            </div>
        </button>
    </div>

    <!-- Guided Tours Section -->
    <div class="px-4 pt-2 pb-1">
        <h3 class="text-xs font-semibold text-subtle uppercase tracking-wide">Feature Tours</h3>
    </div>
    <div class="flex flex-col gap-3 px-4 pb-4">
        <button
            v-for="card in tourCards"
            :key="card.key"
            @click="card.action()"
            class="w-full text-left bg-surface rounded-xl p-5 border border-border active:opacity-80 transition-opacity"
        >
            <div class="flex items-center gap-3 mb-2">
                <div
                    class="w-11 h-11 rounded-lg flex items-center justify-center text-xl flex-shrink-0"
                    :class="card.iconClasses"
                >
                    {{ card.emoji }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-body">{{ card.title }}</div>
                    <div class="text-xs text-muted">{{ card.subtitle }}</div>
                </div>
                <div
                    v-if="isCompleted(card.key)"
                    class="flex items-center gap-1 text-xs font-medium text-success flex-shrink-0"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    Completed
                </div>
            </div>
            <p class="text-sm text-muted mb-3">{{ card.description }}</p>
            <div class="flex items-center gap-2">
                <span
                    class="text-xs font-medium px-2 py-0.5 rounded-full"
                    :class="card.tagClasses"
                >
                    {{ card.tag }}
                </span>
                <span class="text-xs text-subtle">{{ card.time }}</span>
            </div>
        </button>
    </div>

    <!-- Quick Tips -->
    <div class="px-4 pt-2 pb-1">
        <h3 class="text-xs font-semibold text-subtle uppercase tracking-wide">Reference</h3>
    </div>
    <div class="px-4 pb-4">
        <button
            @click="router.visit('/tutorial/tips')"
            class="w-full text-left bg-surface rounded-xl p-5 border border-border active:opacity-80 transition-opacity"
        >
            <div class="flex items-center gap-3 mb-2">
                <div class="w-11 h-11 rounded-lg flex items-center justify-center text-xl flex-shrink-0 bg-warning/15 border border-warning/30">
                    💡
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-semibold text-body">Quick Tips</div>
                    <div class="text-xs text-muted">Browse help topics</div>
                </div>
            </div>
            <p class="text-sm text-muted mb-3">Browse answers to common questions about budgeting, categories, transfers, and more.</p>
            <div class="flex items-center gap-2">
                <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-warning/15 text-warning">Reference</span>
                <span class="text-xs text-subtle">Browse anytime</span>
            </div>
        </button>
    </div>

    <!-- Skip Link -->
    <div class="text-center pb-6">
        <Link href="/budget" class="text-sm text-muted underline">
            Skip — I know what I'm doing
        </Link>
    </div>
    </AppLayout>
</template>
