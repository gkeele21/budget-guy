<script setup>
import { ref, computed, watch, nextTick } from 'vue';
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
    // Null/none option (like "Unassigned") - selecting sets value to null
    nullOption: { type: Object, default: null },
    // Search/filter
    searchable: { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue', 'action']);

const isOpen = ref(false);
const searchQuery = ref('');
const searchInput = ref(null);

const openPicker = () => {
    if (!props.disabled) {
        isOpen.value = true;
    }
};

const closePicker = () => {
    isOpen.value = false;
    searchQuery.value = '';
};

// Focus search input when sheet opens
watch(isOpen, (open) => {
    if (open && props.searchable) {
        nextTick(() => searchInput.value?.focus());
    }
});

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
    if (props.modelValue === null && props.nullOption) return props.nullOption.label;
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

// Filter options by search query
const filteredOptions = computed(() => {
    const query = searchQuery.value.toLowerCase().trim();
    if (!query) return props.options;

    if (props.grouped) {
        return props.options
            .map(group => {
                const items = (group[props.groupItemsKey] || group.categories || group.items || [])
                    .filter(item => {
                        const label = (item[props.labelKey] || item.name || '').toLowerCase();
                        const icon = (item[props.iconKey] || '').toLowerCase();
                        return label.includes(query) || icon.includes(query);
                    });
                return items.length ? { ...group, [props.groupItemsKey]: items, categories: items, items: items } : null;
            })
            .filter(Boolean);
    }

    return props.options.filter(opt => {
        const label = (typeof opt === 'object' ? (opt[props.labelKey] || opt.name || opt.label || '') : String(opt)).toLowerCase();
        return label.includes(query);
    });
});
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
        <!-- Search input -->
        <div v-if="searchable" class="px-4 pt-3 pb-2 border-b border-border">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-subtle" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input
                    ref="searchInput"
                    v-model="searchQuery"
                    type="text"
                    placeholder="Search..."
                    class="w-full pl-9 pr-8 py-2 text-sm bg-surface-inset border border-border rounded-lg text-body placeholder:text-subtle focus-glow"
                />
                <button
                    v-if="searchQuery"
                    type="button"
                    @click="searchQuery = ''"
                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-subtle hover:text-body"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="py-2">
            <!-- Action option (e.g., "Split Transaction...") — hidden while searching -->
            <template v-if="!searchQuery">
                <button
                    v-if="actionOption"
                    type="button"
                    @click="selectAction"
                    class="w-full px-4 py-3 text-left text-sm text-secondary font-medium hover:bg-surface-overlay"
                >
                    {{ actionOption.label }}
                </button>

                <div v-if="actionOption" class="border-b border-border my-2" />

                <!-- Null option (e.g., "Unassigned") -->
                <button
                    v-if="nullOption"
                    type="button"
                    @click="selectOption(null)"
                    class="w-full px-4 py-3 text-left text-sm hover:bg-surface-overlay flex items-center justify-between"
                    :class="modelValue === null ? 'text-secondary font-medium' : 'text-body'"
                >
                    <span>{{ nullOption.label }}</span>
                    <svg v-if="modelValue === null" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-secondary" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>

                <div v-if="nullOption" class="border-b border-border my-2" />
            </template>

            <!-- Grouped options -->
            <template v-if="grouped">
                <div v-for="group in filteredOptions" :key="group[groupLabelKey] || group.name">
                    <div class="px-4 py-2 text-xs font-semibold text-subtle uppercase tracking-wide bg-surface-header">
                        {{ group[groupLabelKey] || group.name }}
                    </div>
                    <button
                        v-for="item in (group[groupItemsKey] || group.categories || group.items || [])"
                        :key="getOptionValue(item)"
                        type="button"
                        @click="selectOption(getOptionValue(item))"
                        class="w-full px-4 py-3 text-left text-sm hover:bg-surface-overlay flex items-center justify-between"
                        :class="isSelected(item) ? 'text-secondary font-medium' : 'text-body'"
                    >
                        <span>{{ getOptionLabel(item) }}</span>
                        <svg v-if="isSelected(item)" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-secondary" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <!-- No results -->
                <p v-if="searchQuery && filteredOptions.length === 0" class="px-4 py-6 text-sm text-subtle text-center">
                    No categories match "{{ searchQuery }}"
                </p>
            </template>

            <!-- Flat options -->
            <template v-else>
                <button
                    v-for="option in filteredOptions"
                    :key="getOptionValue(option)"
                    type="button"
                    @click="selectOption(getOptionValue(option))"
                    class="w-full px-4 py-3 text-left text-sm hover:bg-surface-overlay flex items-center justify-between"
                    :class="isSelected(option) ? 'text-secondary font-medium' : 'text-body'"
                >
                    <span>{{ getOptionLabel(option) }}</span>
                    <svg v-if="isSelected(option)" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-secondary" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </button>
                <!-- No results -->
                <p v-if="searchQuery && filteredOptions.length === 0" class="px-4 py-6 text-sm text-subtle text-center">
                    No options match "{{ searchQuery }}"
                </p>
            </template>
        </div>
    </BottomSheet>
</template>
