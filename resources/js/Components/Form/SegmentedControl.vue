<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: { type: [String, Number], default: null },
    options: { type: Array, required: true },
    disabled: { type: Boolean, default: false },
    size: { type: String, default: 'md' }, // sm, md, lg
    // Flexible option format
    valueKey: { type: String, default: 'value' },
    labelKey: { type: String, default: 'label' },
    // Custom colors per option (for expense/income/transfer)
    colorKey: { type: String, default: 'color' },
});

const emit = defineEmits(['update:modelValue', 'change']);

const sizeClasses = {
    sm: 'py-2 text-xs',
    md: 'py-2.5 text-sm',
    lg: 'py-3 text-base',
};

const getOptionValue = (option) => {
    if (typeof option === 'object') {
        return option[props.valueKey] ?? option.id ?? option.value;
    }
    return option;
};

const getOptionLabel = (option) => {
    if (typeof option === 'object') {
        return option[props.labelKey] ?? option.text ?? option.name ?? option.label;
    }
    return option;
};

const getOptionColor = (option) => {
    if (typeof option === 'object' && option[props.colorKey]) {
        return option[props.colorKey];
    }
    return 'primary';
};

const isSelected = (option) => {
    return props.modelValue === getOptionValue(option);
};

const selectOption = (option) => {
    if (props.disabled || option.disabled) return;
    const value = getOptionValue(option);
    emit('update:modelValue', value);
    emit('change', value);
};

// Color classes for different transaction types
const colorClasses = {
    primary: 'bg-primary text-body',
    expense: 'bg-expense text-inverse',
    income: 'bg-income text-inverse',
    transfer: 'bg-transfer text-inverse',
    secondary: 'bg-secondary text-inverse',
};
</script>

<template>
    <div class="flex bg-surface-secondary rounded-lg p-1">
        <button
            v-for="option in options"
            :key="getOptionValue(option)"
            type="button"
            @click="selectOption(option)"
            :disabled="disabled || option.disabled"
            :class="[
                'flex-1 rounded-md font-semibold transition-colors',
                sizeClasses[size],
                isSelected(option)
                    ? colorClasses[getOptionColor(option)] || colorClasses.primary
                    : 'text-subtle',
                (disabled || option.disabled) ? 'opacity-50 cursor-not-allowed' : '',
            ]"
        >
            {{ getOptionLabel(option) }}
        </button>
    </div>
</template>
