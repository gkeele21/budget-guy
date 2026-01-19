<script setup>
import { computed, ref } from 'vue';
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

// Store cents as integer for ATM-style input
const centsValue = ref(parseInitialCents());

function parseInitialCents() {
    if (props.modelValue === '' || props.modelValue === null || props.modelValue === undefined) {
        return 0;
    }
    const num = parseFloat(props.modelValue);
    return isNaN(num) ? 0 : Math.round(num * 100);
}

const colorClass = computed(() => {
    // If colorByType is false, use secondary blue like other fields
    if (!props.colorByType) {
        return 'text-secondary';
    }
    // Color by transaction type
    switch (props.transactionType) {
        case 'income': return 'text-income';
        case 'transfer': return 'text-transfer';
        default: return 'text-expense';
    }
});

const displayValue = computed(() => {
    const dollars = (centsValue.value / 100).toFixed(2);
    return `$${dollars}`;
});

const onKeyDown = (e) => {
    // Allow backspace to remove last digit
    if (e.key === 'Backspace') {
        e.preventDefault();
        centsValue.value = Math.floor(centsValue.value / 10);
        emitValue();
        return;
    }

    // Only allow digits
    if (!/^\d$/.test(e.key)) {
        // Allow tab, etc. for navigation
        if (!['Tab', 'Enter', 'Escape'].includes(e.key)) {
            e.preventDefault();
        }
        return;
    }

    e.preventDefault();

    // Shift digits left and add new digit (ATM-style)
    const digit = parseInt(e.key, 10);
    const newCents = centsValue.value * 10 + digit;

    // Limit to reasonable max (e.g., $999,999.99)
    if (newCents <= 99999999) {
        centsValue.value = newCents;
        emitValue();
    }
};

const emitValue = () => {
    const dollars = (centsValue.value / 100).toFixed(2);
    emit('update:modelValue', dollars);
};

const onBlur = (e) => {
    emit('blur', e);
};

// Watch for external changes to modelValue
import { watch } from 'vue';
watch(() => props.modelValue, (newVal) => {
    const newCents = parseFloat(newVal) * 100;
    if (!isNaN(newCents) && Math.round(newCents) !== centsValue.value) {
        centsValue.value = Math.round(newCents);
    }
});
</script>

<template>
    <FormRow :label="label" :border-bottom="borderBottom" :error="error">
        <div class="flex items-center">
            <input
                type="text"
                inputmode="numeric"
                :value="displayValue"
                @keydown="onKeyDown"
                @blur="onBlur"
                :required="required"
                :disabled="disabled"
                readonly
                :class="[
                    'bg-transparent focus:outline-none text-sm font-medium text-right w-28 cursor-text caret-transparent',
                    colorClass,
                    disabled ? 'opacity-50' : '',
                ]"
            />
        </div>
    </FormRow>
</template>
