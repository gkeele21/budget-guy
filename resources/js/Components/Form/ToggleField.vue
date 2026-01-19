<script setup>
import FormRow from './FormRow.vue';

const props = defineProps({
    modelValue: { type: Boolean, default: false },
    label: { type: String, required: true },
    onLabel: { type: String, default: 'Yes' },
    offLabel: { type: String, default: 'No' },
    disabled: { type: Boolean, default: false },
    error: { type: String, default: '' },
    borderBottom: { type: Boolean, default: true },
    // Visual variant
    variant: { type: String, default: 'dot' }, // dot, switch, text
});

const emit = defineEmits(['update:modelValue']);

const toggle = () => {
    if (!props.disabled) {
        emit('update:modelValue', !props.modelValue);
    }
};
</script>

<template>
    <FormRow :label="label" :border-bottom="borderBottom" :error="error">
        <button
            type="button"
            @click="toggle"
            :disabled="disabled"
            class="flex items-center gap-2 text-sm font-medium"
            :class="disabled ? 'opacity-50' : ''"
        >
            <!-- Dot variant (like wireframe: ● Cleared / ○ Not yet) -->
            <template v-if="variant === 'dot'">
                <span v-if="modelValue" class="text-income">● {{ onLabel }}</span>
                <span v-else class="text-subtle">○ {{ offLabel }}</span>
            </template>

            <!-- Switch variant (iOS-style toggle) -->
            <template v-else-if="variant === 'switch'">
                <span :class="modelValue ? 'text-body' : 'text-subtle'">
                    {{ modelValue ? onLabel : offLabel }}
                </span>
                <div
                    class="relative w-10 h-6 rounded-full transition-colors"
                    :class="modelValue ? 'bg-income' : 'bg-border-dark'"
                >
                    <div
                        class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-transform"
                        :class="modelValue ? 'translate-x-5' : 'translate-x-1'"
                    />
                </div>
            </template>

            <!-- Text-only variant -->
            <template v-else>
                <span :class="modelValue ? 'text-income' : 'text-subtle'">
                    {{ modelValue ? onLabel : offLabel }}
                </span>
            </template>
        </button>
    </FormRow>
</template>
