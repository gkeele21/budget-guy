<script setup>
import ApplicationLogo from '@/Components/Domain/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: 'Welcome back',
    },
});
</script>

<template>
    <div class="min-h-screen bg-bg flex flex-col">
        <!-- Dark Header -->
        <div class="bg-bg px-6 pt-10 pb-20 text-center">
            <Link href="/">
                <ApplicationLogo size="lg" />
            </Link>
            <h1 class="text-2xl font-bold text-body mt-3">{{ title }}</h1>
        </div>

        <!-- White Curved Section -->
        <div class="flex-1 bg-surface -mt-8 rounded-t-3xl px-6 pt-8 pb-6">
            <slot />
        </div>
    </div>
</template>
