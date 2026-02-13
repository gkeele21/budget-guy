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
            'px-3 py-2 text-sm whitespace-nowrap transition-all border-b-2 -mb-px',
            active
                ? 'text-primary border-primary font-semibold'
                : 'text-subtle border-transparent font-medium hover:text-muted',
            disabled ? 'opacity-50 cursor-not-allowed' : '',
        ]"
    >
        <slot />
    </button>
</template>
