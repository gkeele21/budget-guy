<template>
    <div class="flex items-center justify-center">
        <img src="/images/logo.png" alt="Budget Guy" :class="sizeClasses" />
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
});

const sizeClasses = computed(() => {
    const sizes = {
        sm: 'w-10 h-10',
        md: 'w-16 h-16',
        lg: 'w-24 h-24',
    };
    return sizes[props.size];
});

</script>
