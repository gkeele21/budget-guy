<script setup>
const props = defineProps({
    modelValue: { type: Boolean, default: false },
    label: { type: String, default: '' },
    disabled: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue']);

const toggle = () => {
    if (!props.disabled) {
        emit('update:modelValue', !props.modelValue);
    }
};
</script>

<template>
    <div
        @click="toggle"
        class="flex items-center gap-2 cursor-pointer select-none"
        :class="disabled ? 'opacity-50 cursor-not-allowed' : ''"
    >
        <div
            class="relative w-8 h-[18px] rounded-full transition-colors"
            :class="modelValue ? 'bg-primary' : 'bg-surface-overlay'"
        >
            <div
                class="absolute top-[2px] left-[2px] w-[14px] h-[14px] bg-inverse rounded-full shadow transition-transform"
                :class="{ 'translate-x-[14px]': modelValue }"
            ></div>
        </div>
        <span v-if="label" class="text-xs text-subtle">{{ label }}</span>
        <slot v-else />
    </div>
</template>
