<script setup>
import { watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: '' },
    maxWidth: { type: String, default: '500px' },
});

const emit = defineEmits(['close']);

const close = () => {
    emit('close');
};

const closeOnEscape = (e) => {
    if (e.key === 'Escape' && props.show) {
        close();
    }
};

watch(() => props.show, (isOpen) => {
    document.body.style.overflow = isOpen ? 'hidden' : null;
});

onMounted(() => document.addEventListener('keydown', closeOnEscape));

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);
    document.body.style.overflow = null;
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="show"
                class="fixed inset-0 z-50 flex items-center justify-center"
            >
                <!-- Backdrop (desktop only) -->
                <div class="hidden md:block absolute inset-0 bg-black/50" @click="close" />

                <!-- Modal -->
                <div
                    class="relative w-full h-full md:h-auto md:max-h-[85vh] md:rounded-xl bg-surface flex flex-col"
                    :style="{ maxWidth: `var(--modal-max-width, ${maxWidth})` }"
                    :class="['md:shadow-xl', 'md:mx-4']"
                >
                    <!-- Header -->
                    <div class="flex items-center justify-between px-4 py-3 border-b border-border flex-shrink-0 bg-surface-header">
                        <h2 class="text-lg font-semibold text-body">{{ title }}</h2>
                        <button
                            type="button"
                            @click="close"
                            class="w-8 h-8 flex items-center justify-center rounded-full text-subtle hover:bg-surface-overlay transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 overflow-y-auto">
                        <slot />
                    </div>

                    <!-- Footer -->
                    <div v-if="$slots.footer" class="border-t border-border p-4 flex-shrink-0 safe-area-bottom">
                        <slot name="footer" />
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.safe-area-bottom {
    padding-bottom: max(16px, env(safe-area-inset-bottom));
}

/* Full screen on mobile, constrained on desktop */
@media (min-width: 768px) {
    [style*="--modal-max-width"] {
        max-width: var(--modal-max-width);
    }
}
</style>
