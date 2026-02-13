<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/Domain/ApplicationLogo.vue';
</script>

<template>
    <Head title="Welcome" />

    <div class="min-h-screen bg-bg flex flex-col">
        <!-- Dark Header -->
        <div class="bg-bg px-6 pt-8 pb-14 text-center">
            <div class="mb-3">
                <ApplicationLogo size="lg" />
            </div>
            <h1 class="text-2xl font-bold text-body mb-1">Budget Guy</h1>
            <p class="text-body/70 text-sm">Simple budgeting for real life</p>
        </div>

        <!-- White Curved Section -->
        <div class="flex-1 bg-surface -mt-5 rounded-t-3xl px-6 pt-7 pb-6 flex flex-col">
            <!-- Feature Cards -->
            <div class="flex-1 space-y-3">
                <!-- Card 1 - Primary -->
                <div class="bg-surface rounded-xl p-4 shadow-md border-l-4 border-primary flex items-center gap-4">
                    <div class="w-11 h-11 bg-primary/15 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="font-semibold text-body text-sm">Plan before you spend</p>
                        <p class="text-xs text-subtle">Budget with intention</p>
                    </div>
                </div>

                <!-- Card 2 - Secondary -->
                <div class="bg-surface rounded-xl p-4 shadow-md border-l-4 border-secondary flex items-center gap-4">
                    <div class="w-11 h-11 bg-secondary/15 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-secondary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="font-semibold text-body text-sm">Share with family</p>
                        <p class="text-xs text-subtle">Budget together, stay aligned</p>
                    </div>
                </div>

                <!-- Card 3 - Expense -->
                <div class="bg-surface rounded-xl p-4 shadow-md border-l-4 border-expense flex items-center gap-4">
                    <div class="w-11 h-11 bg-expense/10 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-expense" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div class="text-left">
                        <p class="font-semibold text-body text-sm">See where it goes</p>
                        <p class="text-xs text-subtle">Track spending in real-time</p>
                    </div>
                </div>
            </div>

            <!-- Bottom Actions -->
            <div class="space-y-3 mt-5">
                <Link
                    :href="route('register')"
                    class="block w-full py-4 bg-primary text-white rounded-xl font-semibold text-center"
                >
                    Create Account
                </Link>
                <Link
                    :href="route('login')"
                    class="block w-full py-4 bg-surface-header text-body rounded-xl font-semibold text-center hover:bg-surface-overlay transition-colors"
                >
                    Sign In
                </Link>
            </div>
        </div>
    </div>
</template>
