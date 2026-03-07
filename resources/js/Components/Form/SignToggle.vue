<script setup>
const props = defineProps({
    negative: { type: Boolean, default: false },
});

const emit = defineEmits(['toggle']);
</script>

<template>
    <button
        type="button"
        @mousedown.prevent="emit('toggle')"
        :class="[
            'text-xs font-bold w-6 h-6 rounded transition-colors select-none flex items-center justify-center',
            negative ? 'bg-danger/15 text-danger' : 'bg-success/15 text-success',
        ]"
    >{{ negative ? '&minus;' : '+' }}</button>
</template>
