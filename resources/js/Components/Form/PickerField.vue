<script setup>
import { ref, computed } from 'vue';
import FormRow from './FormRow.vue';
import BottomSheet from '@/Components/Base/BottomSheet.vue';

const props = defineProps({
    modelValue: { type: [String, Number, null], default: null },
    label: { type: String, required: true },
    options: { type: Array, required: true },
    placeholder: { type: String, default: 'Select...' },
    disabled: { type: Boolean, default: false },
    error: { type: String, default: '' },
    borderBottom: { type: Boolean, default: true },
    // For grouped options (like categories)
    grouped: { type: Boolean, default: false },
    groupLabelKey: { type: String, default: 'name' },
    groupItemsKey: { type: String, default: 'items' },
    // For flat options
    valueKey: { type: String, default: 'id' },
    labelKey: { type: String, default: 'name' },
    iconKey: { type: String, default: 'icon' },
    // Special action option (like "Split Transaction...")
    actionOption: { type: Object, default: null },
});

const emit = defineEmits(['update:modelValue', 'action']);

const isOpen = ref(false);

const openPicker = () => {
    if (!props.disabled) {
        isOpen.value = true;
    }
};

const closePicker = () => {
    isOpen.value = false;
};

const selectOption = (value) => {
    emit('update:modelValue', value);
    closePicker();
};

const selectAction = () => {
    emit('action');
    closePicker();
};

// Get display value for the selected option
const displayValue = computed(() => {
    if (!props.modelValue) return null;

    if (props.grouped) {
        for (const group of props.options) {
            const items = group[props.groupItemsKey] || group.categories || group.items || [];
            const found = items.find(item => getOptionValue(item) === props.modelValue);
            if (found) {
                const icon = found[props.iconKey];
                const label = found[props.labelKey] || found.name;
                return icon ? `${icon} ${label}` : label;
            }
        }
    } else {
        const found = props.options.find(opt => getOptionValue(opt) === props.modelValue);
        if (found) {
            const icon = found[props.iconKey];
            const label = found[props.labelKey] || found.name;
            return icon ? `${icon} ${label}` : label;
        }
    }
    return null;
});

const getOptionValue = (option) => {
    if (typeof option === 'object') {
        return option[props.valueKey] ?? option.id ?? option.value;
    }
    return option;
};

const getOptionLabel = (option) => {
    if (typeof option === 'object') {
        const icon = option[props.iconKey];
        const label = option[props.labelKey] || option.name || option.label;
        return icon ? `${icon} ${label}` : label;
    }
    return option;
};

const isSelected = (option) => {
    return getOptionValue(option) === props.modelValue;
};
</script>

<template>
    <FormRow :label="label" :border-bottom="borderBottom" :error="error">
        <button
            type="button"
            @click="openPicker"
            :disabled="disabled"
            class="flex items-center gap-1 text-sm font-medium text-right"
            :class="[
                displayValue ? 'text-secondary' : 'text-subtle',
                disabled ? 'opacity-50' : '',
            ]"
        >
            <span class="truncate">{{ displayValue || placeholder }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-subtle shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </FormRow>

    <BottomSheet :show="isOpen" :title="label" @close="closePicker">
        <div class="py-2">
            <!-- Action option (e.g., "Split Transaction...") -->
            <button
                v-if="actionOption"
                type="button"
                @click="selectAction"
                class="w-full px-4 py-3 text-left text-sm text-secondary font-medium hover:bg-surface-secondary"
            >
                {{ actionOption.label }}
            </button>

            <div v-if="actionOption" class="border-b border-border my-2" />

            <!-- Grouped options -->
            <template v-if="grouped">
                <div v-for="group in options" :key="group[groupLabelKey] || group.name">
                    <div class="px-4 py-2 text-xs font-semibold text-subtle uppercase tracking-wide bg-surface-secondary">
                        {{ group[groupLabelKey] || group.name }}
                    </div>
                    <button
                        v-for="item in (group[groupItemsKey] || group.categories || group.items || [])"
                        :key="getOptionValue(item)"
                        type="button"
                        @click="selectOption(getOptionValue(item))"
                        class="w-full px-4 py-3 text-left text-sm hover:bg-surface-secondary flex items-center justify-between"
                        :class="isSelected(item) ? 'text-secondary font-medium' : 'text-body'"
                    >
                        <span>{{ getOptionLabel(item) }}</span>
                        <svg v-if="isSelected(item)" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-secondary" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </template>

            <!-- Flat options -->
            <template v-else>
                <button
                    v-for="option in options"
                    :key="getOptionValue(option)"
                    type="button"
                    @click="selectOption(getOptionValue(option))"
                    class="w-full px-4 py-3 text-left text-sm hover:bg-surface-secondary flex items-center justify-between"
                    :class="isSelected(option) ? 'text-secondary font-medium' : 'text-body'"
                >
                    <span>{{ getOptionLabel(option) }}</span>
                    <svg v-if="isSelected(option)" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-secondary" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
            </template>
        </div>
    </BottomSheet>
</template>
