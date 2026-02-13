<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useTheme } from '@/Composables/useTheme.js';

const { theme, setTheme, bgMode, setBgMode } = useTheme();

const accentOptions = [
    { value: 'green', label: 'Green', color: '#57d025' },
    { value: 'blue', label: 'Blue', color: '#3b82f6' },
    { value: 'orange', label: 'Orange', color: '#f47612' },
];

const bgModeOptions = [
    { value: 'slate', label: 'Slate' },
    { value: 'cream', label: 'Cream' },
];

const props = defineProps({
    user: Object,
    budgetName: String,
    pendingInviteCount: Number,
    counts: Object,
});

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <Head title="Settings" />

    <AppLayout>
        <template #title>Settings</template>

        <div class="p-4 space-y-6">
            <!-- Budget Section -->
            <div>
                <h2 class="text-sm font-semibold text-warning uppercase tracking-wide px-1 mb-2">
                    Budget
                </h2>
                <div class="bg-surface rounded-card divide-y divide-border">
                    <Link
                        :href="route('settings.accounts')"
                        class="flex items-center justify-between p-4 hover:bg-surface-overlay"
                    >
                        <div class="flex items-center gap-3">
                            <span class="text-xl">🏦</span>
                            <span class="text-body">Accounts</span>
                        </div>
                        <div class="flex items-center gap-1 text-subtle">
                            <span v-if="counts?.accounts">{{ counts.accounts }}</span>
                            <span>›</span>
                        </div>
                    </Link>
                    <Link
                        :href="route('settings.categories')"
                        class="flex items-center justify-between p-4 hover:bg-surface-overlay"
                    >
                        <div class="flex items-center gap-3">
                            <span class="text-xl">📁</span>
                            <span class="text-body">Categories</span>
                        </div>
                        <div class="flex items-center gap-1 text-subtle">
                            <span v-if="counts?.categories">{{ counts.categories }}</span>
                            <span>›</span>
                        </div>
                    </Link>
                    <Link
                        :href="route('recurring.index')"
                        class="flex items-center justify-between p-4 hover:bg-surface-overlay"
                    >
                        <div class="flex items-center gap-3">
                            <span class="text-xl">🔄</span>
                            <span class="text-body">Recurring</span>
                        </div>
                        <div class="flex items-center gap-1 text-subtle">
                            <span v-if="counts?.recurring">{{ counts.recurring }}</span>
                            <span>›</span>
                        </div>
                    </Link>
                    <Link
                        :href="route('payees.index')"
                        class="flex items-center justify-between p-4 hover:bg-surface-overlay"
                    >
                        <div class="flex items-center gap-3">
                            <span class="text-xl">👥</span>
                            <span class="text-body">Payees</span>
                        </div>
                        <div class="flex items-center gap-1 text-subtle">
                            <span v-if="counts?.payees">{{ counts.payees }}</span>
                            <span>›</span>
                        </div>
                    </Link>
                    <!-- Export Data - hidden for now
                    <Link
                        :href="route('export.index')"
                        class="flex items-center justify-between p-4 hover:bg-surface-overlay"
                    >
                        <div class="flex items-center gap-3">
                            <span class="text-xl">📤</span>
                            <span class="text-body">Export Data</span>
                        </div>
                        <span class="text-subtle">›</span>
                    </Link>
                    -->
                    <Link
                        :href="route('sharing.index')"
                        class="flex items-center justify-between p-4 hover:bg-surface-overlay"
                    >
                        <div class="flex items-center gap-3">
                            <span class="text-xl">🔗</span>
                            <span class="text-body">Sharing</span>
                        </div>
                        <span class="text-subtle">›</span>
                    </Link>
                </div>
            </div>

            <!-- Pending Invites Banner -->
            <Link
                v-if="pendingInviteCount > 0"
                :href="route('sharing.pending')"
                class="block bg-primary/10 border border-primary/20 rounded-card p-4 hover:bg-primary/20 transition-colors"
            >
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-inverse font-semibold">
                        {{ pendingInviteCount }}
                    </div>
                    <div>
                        <div class="font-medium text-body">You have pending invites!</div>
                        <div class="text-sm text-subtle">
                            {{ pendingInviteCount === 1 ? 'Someone invited you to share a budget' : `${pendingInviteCount} people invited you to share budgets` }}
                        </div>
                    </div>
                </div>
            </Link>

            <!-- Appearance Section -->
            <div>
                <h2 class="text-sm font-semibold text-warning uppercase tracking-wide px-1 mb-2">
                    Appearance
                </h2>
                <div class="bg-surface rounded-card p-4 space-y-4">
                    <!-- Accent Color -->
                    <div>
                        <label class="text-sm text-subtle mb-2 block">Accent Color</label>
                        <div class="flex gap-3">
                            <button
                                v-for="option in accentOptions"
                                :key="option.value"
                                @click="setTheme(option.value)"
                                class="w-10 h-10 rounded-full border-2 transition-all flex items-center justify-center"
                                :class="theme === option.value ? 'border-body scale-110' : 'border-transparent'"
                                :style="{ backgroundColor: option.color }"
                            >
                                <svg v-if="theme === option.value" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Background Mode -->
                    <div>
                        <label class="text-sm text-subtle mb-2 block">Background</label>
                        <div class="flex gap-2">
                            <button
                                v-for="option in bgModeOptions"
                                :key="option.value"
                                @click="setBgMode(option.value)"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                                :class="bgMode === option.value
                                    ? 'bg-primary text-white'
                                    : 'bg-surface-overlay text-subtle'"
                            >
                                {{ option.label }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Section -->
            <div>
                <h2 class="text-sm font-semibold text-warning uppercase tracking-wide px-1 mb-2">
                    Account
                </h2>
                <div class="bg-surface rounded-card divide-y divide-border">
                    <Link
                        :href="route('profile.edit')"
                        class="flex items-center justify-between p-4 hover:bg-surface-overlay"
                    >
                        <div class="flex items-center gap-3">
                            <span class="text-xl">👤</span>
                            <div>
                                <div class="text-body">{{ user.name }}</div>
                                <div class="text-sm text-subtle">{{ user.email }}</div>
                            </div>
                        </div>
                        <span class="text-subtle">›</span>
                    </Link>
                    <button
                        @click="logout"
                        class="w-full flex items-center gap-3 p-4 hover:bg-surface-overlay text-left"
                    >
                        <span class="text-xl">🚪</span>
                        <span class="text-expense">Sign Out</span>
                    </button>
                </div>
            </div>

            <!-- Current Budget Info -->
            <div class="text-center text-subtle text-sm py-4">
                <p>Current Budget: <span class="font-medium text-body">{{ budgetName }}</span></p>
            </div>
        </div>
    </AppLayout>
</template>
