<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/Domain/ApplicationLogo.vue';
</script>

<template>
    <Head title="Welcome" />

    <div class="min-h-screen bg-gradient-to-b from-primary/10 to-white flex flex-col">
        <!-- Main Content -->
        <div class="flex-1 flex flex-col items-center justify-center px-6 py-12">
            <!-- Logo/Icon -->
            <div class="mb-8">
                <ApplicationLogo size="lg" />
            </div>

            <!-- App Name -->
            <h1 class="text-3xl font-bold text-body mb-2">Budget Guy</h1>

            <!-- Tagline -->
            <p class="text-lg text-subtle text-center max-w-xs mb-12">
                Simple budgeting for real life
            </p>

            <!-- Feature Highlights -->
            <div class="w-full max-w-sm space-y-4 mb-12">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-income/10 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-income" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <p class="text-body">Plan your spending before it happens</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-primary/10 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <p class="text-body">Share budgets with family</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <p class="text-body">Track spending in real-time</p>
                </div>
            </div>
        </div>

        <!-- Bottom Actions -->
        <div class="p-6 space-y-3">
            <Link
                :href="route('register')"
                class="block w-full py-4 bg-primary text-white rounded-card font-semibold text-center hover:bg-primary/90 transition-colors"
            >
                Create Account
            </Link>
            <Link
                :href="route('login')"
                class="block w-full py-4 bg-white border border-gray-200 text-body rounded-card font-semibold text-center hover:bg-gray-50 transition-colors"
            >
                Sign In
            </Link>
        </div>
    </div>
</template>
