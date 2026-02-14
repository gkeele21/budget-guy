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
    primary: 'btn-primary bg-primary text-white border-2 border-transparent',
    secondary: 'btn-secondary bg-info text-white border-2 border-transparent',
    danger: 'btn-danger bg-danger text-white border-2 border-transparent',
    ghost: 'btn-ghost bg-transparent text-muted border-2 border-transparent',
    outline: 'btn-outline bg-transparent text-primary border-2 border-primary',
    muted: 'btn-muted bg-surface-inset text-body border-2 border-border',
    // Transaction type variants
    income: 'btn-income bg-success text-white border-2 border-transparent',
    expense: 'btn-expense bg-danger text-white border-2 border-transparent',
    transfer: 'btn-transfer bg-info text-white border-2 border-transparent',
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

<style>
/* Hover effects only on devices with pointer hover (not touch) */
@media (hover: hover) {
    .btn-primary:hover {
        background-color: #ffffff;
        color: rgb(var(--color-primary));
        border-color: rgb(var(--color-primary));
    }
    .btn-secondary:hover,
    .btn-transfer:hover {
        background-color: #ffffff;
        color: rgb(var(--color-info));
        border-color: rgb(var(--color-info));
    }
    .btn-danger:hover,
    .btn-expense:hover {
        background-color: #ffffff;
        color: rgb(var(--color-danger));
        border-color: rgb(var(--color-danger));
    }
    .btn-ghost:hover {
        background-color: rgb(var(--color-surface-overlay));
    }
    .btn-outline:hover {
        background-color: rgb(var(--color-primary));
        color: #ffffff;
    }
    .btn-muted:hover {
        background-color: rgb(var(--color-surface-header));
        border-color: rgb(var(--color-border-strong));
    }
    .btn-income:hover {
        background-color: #ffffff;
        color: rgb(var(--color-success));
        border-color: rgb(var(--color-success));
    }
}

/* Touch feedback for all devices */
.btn-primary:active,
.btn-secondary:active,
.btn-danger:active,
.btn-income:active,
.btn-expense:active,
.btn-transfer:active,
.btn-outline:active,
.btn-muted:active {
    opacity: 0.8;
}
</style>
