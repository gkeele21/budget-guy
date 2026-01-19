<script setup>
const props = defineProps({
    active: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
});

const emit = defineEmits(['click']);

const handleClick = () => {
    if (!props.disabled) {
        emit('click');
    }
};
</script>

<template>
    <button
        type="button"
        @click="handleClick"
        :disabled="disabled"
        :class="[
            'px-3 py-1.5 rounded-full text-xs font-medium whitespace-nowrap transition-colors',
            active
                ? 'bg-primary text-inverse'
                : 'bg-surface-tertiary text-subtle',
            disabled ? 'opacity-50 cursor-not-allowed' : '',
        ]"
    >
        <slot />
    </button>
</template>
