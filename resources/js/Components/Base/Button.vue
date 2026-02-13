<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    variant: { type: String, default: 'primary' }, // primary, secondary, danger, ghost, outline, income, expense, transfer
    size: { type: String, default: 'md' }, // sm, md, lg
    disabled: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },
    href: { type: String, default: null },
    type: { type: String, default: 'button' },
    fullWidth: { type: Boolean, default: false },
});

const baseClasses = 'inline-flex items-center justify-center font-semibold rounded-xl transition-all focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed';

const variantClasses = {
    primary: 'bg-primary text-white hover:bg-white hover:text-primary border-2 border-transparent hover:border-primary',
    secondary: 'bg-info text-white hover:bg-white hover:text-info border-2 border-transparent hover:border-info',
    danger: 'bg-danger text-white hover:bg-white hover:text-danger border-2 border-transparent hover:border-danger',
    ghost: 'bg-transparent text-muted hover:bg-surface-overlay border-2 border-transparent',
    outline: 'bg-transparent text-primary border-2 border-primary hover:bg-primary hover:text-white',
    muted: 'bg-surface-inset hover:bg-surface-header text-body border-2 border-border hover:border-border-strong',
    // Transaction type variants
    income: 'bg-success text-white hover:bg-white hover:text-success border-2 border-transparent hover:border-success',
    expense: 'bg-danger text-white hover:bg-white hover:text-danger border-2 border-transparent hover:border-danger',
    transfer: 'bg-info text-white hover:bg-white hover:text-info border-2 border-transparent hover:border-info',
};

const sizeClasses = {
    sm: 'px-3 py-2 text-sm',
    md: 'px-4 py-3 text-sm',
    lg: 'px-6 py-4 text-base',
};

const classes = computed(() => [
    baseClasses,
    variantClasses[props.variant] || variantClasses.primary,
    sizeClasses[props.size] || sizeClasses.md,
    props.fullWidth ? 'w-full' : '',
]);
</script>

<template>
    <Link
        v-if="href"
        :href="href"
        :class="classes"
    >
        <span v-if="loading" class="mr-2">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </span>
        <slot />
    </Link>
    <button
        v-else
        :type="type"
        :disabled="disabled || loading"
        :class="classes"
    >
        <span v-if="loading" class="mr-2">
            <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </span>
        <slot />
    </button>
</template>
