<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="w-full px-4 py-2.5 bg-surface border border-border rounded-xl shadow-sm text-body placeholder:text-subtle focus:outline-none focus:border-secondary focus:ring-2 focus:ring-secondary/20"
        v-model="model"
        ref="input"
    />
</template>
