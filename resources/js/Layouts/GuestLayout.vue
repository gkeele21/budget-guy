<script setup>
import ApplicationLogo from '@/Components/Domain/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex flex-col items-center bg-gradient-to-b from-primary/10 to-white pt-8 sm:justify-center sm:pt-0">
        <div class="mb-6">
            <Link href="/">
                <ApplicationLogo size="lg" />
            </Link>
            <h1 class="text-2xl font-bold text-body text-center mt-4">Budget Guy</h1>
        </div>

        <div class="w-full overflow-hidden bg-white px-6 py-6 shadow-lg sm:max-w-md sm:rounded-2xl">
            <slot />
        </div>
    </div>
</template>
