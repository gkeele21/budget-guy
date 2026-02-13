<script setup>
import { computed } from 'vue';

const emit = defineEmits(['update:checked']);

const props = defineProps({
    checked: {
        type: [Array, Boolean],
        required: true,
    },
    value: {
        default: null,
    },
});

const proxyChecked = computed({
    get() {
        return props.checked;
    },

    set(val) {
        emit('update:checked', val);
    },
});
</script>

<template>
    <label class="relative inline-flex items-center cursor-pointer shrink-0">
        <input
            type="checkbox"
            :value="value"
            v-model="proxyChecked"
            class="sr-only peer"
        />
        <div class="w-4 h-4 border border-border rounded bg-surface-inset peer-checked:bg-primary peer-checked:border-primary peer-focus:shadow-[0_0_0_2px_rgb(var(--color-primary)),0_0_8px_rgb(var(--color-primary)/0.4)] flex items-center justify-center">
            <svg
                v-if="proxyChecked"
                class="w-3 h-3 text-body"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="3"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>
    </label>
</template>
