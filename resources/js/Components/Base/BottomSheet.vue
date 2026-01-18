<script setup>
import { watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    title: { type: String, default: '' },
    maxHeight: { type: String, default: '85vh' },
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
                class="fixed inset-0 z-50 flex items-end justify-center"
                @click.self="close"
            >
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-black/50" @click="close" />

                <!-- Sheet -->
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="transform translate-y-full"
                    enter-to-class="transform translate-y-0"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="transform translate-y-0"
                    leave-to-class="transform translate-y-full"
                >
                    <div
                        v-if="show"
                        class="relative w-full bg-white rounded-t-2xl flex flex-col"
                        :style="{ maxHeight }"
                    >
                        <!-- Handle -->
                        <div class="flex justify-center pt-3 pb-1">
                            <div class="w-10 h-1 bg-gray-300 rounded-full" />
                        </div>

                        <!-- Header -->
                        <div v-if="title || $slots.header" class="px-4 pb-3 border-b border-gray-100">
                            <slot name="header">
                                <h3 class="text-lg font-semibold text-body text-center">{{ title }}</h3>
                            </slot>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 overflow-y-auto">
                            <slot />
                        </div>

                        <!-- Footer -->
                        <div v-if="$slots.footer" class="border-t border-gray-100 p-4 safe-area-bottom">
                            <slot name="footer" />
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.safe-area-bottom {
    padding-bottom: max(16px, env(safe-area-inset-bottom));
}
</style>
