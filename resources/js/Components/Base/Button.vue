<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    variant: { type: String, default: 'primary' }, // primary, secondary, danger, ghost, income, expense, transfer
    size: { type: String, default: 'md' }, // sm, md, lg
    disabled: { type: Boolean, default: false },
    loading: { type: Boolean, default: false },
    href: { type: String, default: null },
    type: { type: String, default: 'button' },
    fullWidth: { type: Boolean, default: false },
});

const baseClasses = 'inline-flex items-center justify-center font-semibold rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

const variantClasses = {
    primary: 'bg-primary text-inverse hover:bg-primary-hover focus:ring-primary',
    secondary: 'bg-gray-100 text-body hover:bg-gray-200 focus:ring-gray-300',
    danger: 'bg-danger text-inverse hover:bg-danger-hover focus:ring-danger',
    ghost: 'bg-transparent text-subtle hover:bg-gray-100 focus:ring-gray-300',
    success: 'bg-success text-inverse hover:bg-success-hover focus:ring-success',
    warning: 'bg-warning text-inverse hover:bg-warning-hover focus:ring-warning',
    info: 'bg-info text-inverse hover:bg-info-hover focus:ring-info',
    // Transaction type variants
    income: 'bg-income text-inverse hover:bg-income-hover focus:ring-income',
    expense: 'bg-expense text-inverse hover:bg-expense-hover focus:ring-expense',
    transfer: 'bg-transfer text-inverse hover:bg-transfer-hover focus:ring-transfer',
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
