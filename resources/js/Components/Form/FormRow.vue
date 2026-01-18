<script setup>
defineProps({
    label: { type: String, required: true },
    borderBottom: { type: Boolean, default: true },
    error: { type: String, default: '' },
});
</script>

<template>
    <div>
        <div
            class="flex items-center justify-between px-4 py-3.5"
            :class="{ 'border-b border-gray-100': borderBottom && !error }"
        >
            <span class="text-sm text-subtle shrink-0">{{ label }}</span>
            <div class="flex-1 flex justify-end items-center min-w-0 ml-4">
                <slot />
            </div>
        </div>
        <p v-if="error" class="px-4 pb-2 text-xs text-danger">{{ error }}</p>
    </div>
</template>
