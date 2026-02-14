<script setup>
import { ref } from 'vue';

defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Search...' },
});

const emit = defineEmits(['update:modelValue']);

const inputRef = ref(null);

const focus = () => {
    inputRef.value?.focus();
};

defineExpose({ focus });
</script>

<template>
    <div class="relative">
        <input
            ref="inputRef"
            type="text"
            :value="modelValue"
            @input="emit('update:modelValue', $event.target.value)"
            :placeholder="placeholder"
            class="w-full px-4 py-3 pl-10 bg-surface rounded-card text-body placeholder-subtle focus:outline-none"
        />
        <svg
            xmlns="http://www.w3.org/2000/svg"
            class="h-5 w-5 absolute left-3 top-1/2 -translate-y-1/2 text-subtle"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <button
            v-if="modelValue"
            @click="emit('update:modelValue', '')"
            class="absolute right-3 top-1/2 -translate-y-1/2 p-1 hover:bg-surface-overlay rounded-full"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-subtle" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</template>
