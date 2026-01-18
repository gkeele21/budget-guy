<script setup>
import { computed } from 'vue';
import FormRow from './FormRow.vue';

const props = defineProps({
    modelValue: { type: [String, Number], default: '' },
    label: { type: String, default: 'Amount' },
    transactionType: { type: String, default: 'expense' }, // expense, income, transfer
    placeholder: { type: String, default: '0.00' },
    required: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
    error: { type: String, default: '' },
    borderBottom: { type: Boolean, default: true },
    // When true, uses transaction type colors. When false, uses primary green like other fields.
    colorByType: { type: Boolean, default: true },
});

const emit = defineEmits(['update:modelValue', 'blur']);

const colorClass = computed(() => {
    // If no value, show placeholder color
    if (!props.modelValue && props.modelValue !== 0) {
        return 'text-gray-400';
    }
    // If colorByType is false, use primary green like picker fields
    if (!props.colorByType) {
        return 'text-primary';
    }
    // Color by transaction type
    switch (props.transactionType) {
        case 'income': return 'text-income';
        case 'transfer': return 'text-transfer';
        default: return 'text-expense';
    }
});

const onInput = (e) => {
    emit('update:modelValue', e.target.value);
};

const formatOnBlur = (e) => {
    const value = e.target.value;
    if (value !== '' && value !== null) {
        const formatted = parseFloat(value).toFixed(2);
        if (!isNaN(formatted)) {
            emit('update:modelValue', formatted);
        }
    }
    emit('blur', e);
};

const displayValue = computed(() => {
    if (props.modelValue === '' || props.modelValue === null || props.modelValue === undefined) {
        return '';
    }
    return props.modelValue;
});
</script>

<template>
    <FormRow :label="label" :border-bottom="borderBottom" :error="error">
        <div class="flex items-center">
            <span :class="['text-sm font-medium', colorClass]">$</span>
            <input
                type="text"
                inputmode="decimal"
                :value="displayValue"
                @input="onInput"
                @blur="formatOnBlur"
                :placeholder="placeholder"
                :required="required"
                :disabled="disabled"
                :class="[
                    'bg-transparent focus:outline-none text-sm font-medium text-right w-24',
                    colorClass,
                    disabled ? 'opacity-50' : '',
                ]"
            />
        </div>
    </FormRow>
</template>
