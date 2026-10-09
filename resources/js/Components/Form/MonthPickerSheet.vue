<script setup>
import { ref, computed, watch } from 'vue';
import BottomSheet from '@/Components/Base/BottomSheet.vue';
import Button from '@/Components/Base/Button.vue';

// Quick month/year jump. Values are 'YYYY-MM' strings.
const props = defineProps({
    show: { type: Boolean, default: false },
    modelValue: { type: String, required: true },
    min: { type: String, default: null },
});

const emit = defineEmits(['select', 'close']);

const MONTHS = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

const toKey = (year, monthIndex) => `${year}-${String(monthIndex + 1).padStart(2, '0')}`;

const today = new Date();
const thisMonth = toKey(today.getFullYear(), today.getMonth());

// Year being browsed in the sheet; resets to the selected month's year on open
const viewYear = ref(Number(props.modelValue.split('-')[0]));
watch(() => props.show, (open) => {
    if (open) viewYear.value = Number(props.modelValue.split('-')[0]);
});

const minYear = computed(() => props.min ? Number(props.min.split('-')[0]) : null);
const maxYear = today.getFullYear() + 1;

const isDisabled = (key) => !!props.min && key < props.min;

const variantFor = (key) => {
    if (key === props.modelValue) return 'primary';
    if (key === thisMonth) return 'outline';
    return 'muted';
};

const select = (key) => {
    if (isDisabled(key)) return;
    emit('select', key);
};
</script>

<template>
    <BottomSheet :show="show" title="Go to Month" @close="emit('close')">
        <div class="px-4 pb-4">
            <!-- Year switcher -->
            <div class="flex items-center justify-between mb-3">
                <Button
                    variant="ghost"
                    size="sm"
                    :disabled="minYear !== null && viewYear <= minYear"
                    aria-label="Previous year"
                    @click="viewYear--"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </Button>
                <span class="text-lg font-semibold text-body">{{ viewYear }}</span>
                <Button
                    variant="ghost"
                    size="sm"
                    :disabled="viewYear >= maxYear"
                    aria-label="Next year"
                    @click="viewYear++"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </Button>
            </div>

            <!-- Month grid -->
            <div class="grid grid-cols-4 gap-2">
                <Button
                    v-for="(label, i) in MONTHS"
                    :key="label"
                    :variant="variantFor(toKey(viewYear, i))"
                    :disabled="isDisabled(toKey(viewYear, i))"
                    @click="select(toKey(viewYear, i))"
                >
                    {{ label }}
                </Button>
            </div>

            <!-- Jump to current month -->
            <Button
                v-if="modelValue !== thisMonth"
                variant="ghost"
                size="sm"
                full-width
                class="mt-3"
                @click="select(thisMonth)"
            >
                This Month
            </Button>
        </div>
    </BottomSheet>
</template>
