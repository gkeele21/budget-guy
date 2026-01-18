<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import TextField from '@/Components/Form/TextField.vue';
import AmountField from '@/Components/Form/AmountField.vue';
import PickerField from '@/Components/Form/PickerField.vue';
import Button from '@/Components/Base/Button.vue';
import BottomSheet from '@/Components/Base/BottomSheet.vue';

const props = defineProps({
    categoryGroups: Array,
});

// Transform category groups for PickerField
const groupOptions = computed(() => {
    return props.categoryGroups.map(g => ({ id: g.id, name: g.name }));
});

const showAddGroupModal = ref(false);
const showAddCategoryModal = ref(false);
const showEditCategoryModal = ref(false);
const selectedGroupId = ref(null);
const editingCategory = ref(null);
const showIconPicker = ref(false);

const groupForm = useForm({
    name: '',
});

const categoryForm = useForm({
    group_id: '',
    name: '',
    icon: '',
    default_amount: '',
});

const editForm = useForm({
    id: null,
    group_id: '',
    name: '',
    icon: '',
    default_amount: '',
});

const submitGroup = () => {
    groupForm.post(route('category-groups.store'), {
        onSuccess: () => {
            showAddGroupModal.value = false;
            groupForm.reset();
        },
    });
};

const openAddCategory = (groupId) => {
    selectedGroupId.value = groupId;
    categoryForm.group_id = groupId;
    showAddCategoryModal.value = true;
};

const submitCategory = () => {
    categoryForm.post(route('categories.store'), {
        onSuccess: () => {
            showAddCategoryModal.value = false;
            categoryForm.reset();
        },
    });
};

const deleteGroup = (groupId) => {
    if (confirm('Delete this category group and all its categories?')) {
        router.delete(route('category-groups.destroy', groupId));
    }
};

const deleteCategory = (categoryId) => {
    if (confirm('Delete this category? Transactions will become uncategorized.')) {
        router.delete(route('categories.destroy', categoryId));
        showEditCategoryModal.value = false;
    }
};

const openEditCategory = (category, groupId) => {
    editForm.id = category.id;
    editForm.group_id = groupId;
    editForm.name = category.name;
    editForm.icon = category.icon || '';
    editForm.default_amount = category.default_amount || '';
    showEditCategoryModal.value = true;
    showIconPicker.value = false;
};

const submitEditCategory = () => {
    editForm.put(route('categories.update', editForm.id), {
        onSuccess: () => {
            showEditCategoryModal.value = false;
            editForm.reset();
        },
    });
};

const formatCurrency = (amount) => {
    if (!amount) return '-';
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};

// Common emoji suggestions for budgeting - organized grid from wireframe
const emojiGrid = [
    '🛒', '🍎', '🥗', '🛍️', '🏠', '⚡', '💧', '📱',
    '🌐', '🚗', '⛽', '🍽️', '☕', '🎬', '🎮', '🎵',
    '💪', '💊', '👕', '✂️', '🎁', '✈️', '🏖️', '📚',
];
</script>

<template>
    <Head title="Categories" />

    <AppLayout>
        <template #title>Categories</template>
        <template #header-left>
            <Link :href="route('settings.index')" class="p-2 -ml-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </Link>
        </template>

        <div class="p-4 space-y-4">
            <!-- Category Groups -->
            <div v-for="group in categoryGroups" :key="group.id" class="space-y-2">
                <div class="flex items-center justify-between px-1">
                    <h2 class="text-sm font-semibold text-subtle uppercase tracking-wide">
                        {{ group.name }}
                    </h2>
                    <div class="flex items-center gap-2">
                        <button
                            @click="openAddCategory(group.id)"
                            class="text-primary text-sm font-medium"
                        >
                            + Add Category
                        </button>
                        <button
                            @click="deleteGroup(group.id)"
                            class="text-subtle hover:text-expense p-1"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="bg-surface rounded-card divide-y divide-gray-100">
                    <button
                        v-for="category in group.categories"
                        :key="category.id"
                        @click="openEditCategory(category, group.id)"
                        class="w-full flex items-center justify-between p-4 hover:bg-gray-50 text-left"
                        :class="{ 'opacity-50': category.is_hidden }"
                    >
                        <div class="flex items-center gap-3">
                            <span class="text-xl">{{ category.icon || '📁' }}</span>
                            <div>
                                <div class="font-medium text-body">{{ category.name }}</div>
                                <div v-if="category.default_amount" class="text-sm text-subtle">
                                    Default: {{ formatCurrency(category.default_amount) }}
                                </div>
                            </div>
                        </div>
                        <span class="text-subtle">›</span>
                    </button>

                    <div v-if="group.categories.length === 0" class="p-4 text-center text-subtle">
                        No categories yet
                    </div>
                </div>
            </div>

            <!-- Add Category Group Button -->
            <button
                @click="showAddGroupModal = true"
                class="w-full py-4 border-2 border-dashed border-primary text-primary rounded-card font-medium hover:bg-primary-bg transition-colors"
            >
                + New Group
            </button>
        </div>

        <!-- Add Group Modal -->
        <BottomSheet :show="showAddGroupModal" title="Add Category Group" @close="showAddGroupModal = false">
            <form @submit.prevent="submitGroup">
                <div class="bg-white mx-3 rounded-xl overflow-hidden">
                    <TextField
                        v-model="groupForm.name"
                        label="Group Name"
                        placeholder="e.g., Bills, Everyday, Savings Goals"
                        :border-bottom="false"
                        required
                    />
                </div>
            </form>

            <template #footer>
                <div class="flex gap-2">
                    <Button variant="secondary" @click="showAddGroupModal = false" class="flex-1">
                        Cancel
                    </Button>
                    <Button @click="submitGroup" :loading="groupForm.processing" class="flex-1">
                        Add Group
                    </Button>
                </div>
            </template>
        </BottomSheet>

        <!-- Add Category Modal -->
        <BottomSheet :show="showAddCategoryModal" title="Add Category" @close="showAddCategoryModal = false">
            <form @submit.prevent="submitCategory">
                <div class="bg-white mx-3 rounded-xl overflow-hidden">
                    <TextField
                        v-model="categoryForm.name"
                        label="Name"
                        placeholder="e.g., Groceries, Rent, Gas"
                        required
                    />
                    <AmountField
                        v-model="categoryForm.default_amount"
                        label="Default Amount"
                        :color-by-type="false"
                        placeholder="0.00"
                    />
                </div>

                <!-- Icon Picker -->
                <div class="mx-3 mt-3">
                    <div class="text-xs font-semibold text-subtle uppercase tracking-wide mb-2 px-1">
                        Icon
                    </div>
                    <div class="bg-white rounded-xl p-3">
                        <div class="grid grid-cols-8 gap-1.5">
                            <button
                                v-for="emoji in emojiGrid"
                                :key="emoji"
                                type="button"
                                @click="categoryForm.icon = emoji"
                                :class="[
                                    'w-9 h-9 flex items-center justify-center text-xl rounded-lg transition-colors',
                                    categoryForm.icon === emoji
                                        ? 'bg-primary/20 ring-2 ring-primary'
                                        : 'bg-gray-50 hover:bg-gray-100'
                                ]"
                            >
                                {{ emoji }}
                            </button>
                        </div>
                    </div>
                </div>

                <p class="text-xs text-subtle text-center mt-3 px-4">
                    Default amount is used as reference when budgeting
                </p>
            </form>

            <template #footer>
                <div class="flex gap-2">
                    <Button variant="secondary" @click="showAddCategoryModal = false" class="flex-1">
                        Cancel
                    </Button>
                    <Button @click="submitCategory" :loading="categoryForm.processing" class="flex-1">
                        Add Category
                    </Button>
                </div>
            </template>
        </BottomSheet>

        <!-- Edit Category Modal -->
        <BottomSheet :show="showEditCategoryModal" title="Edit Category" @close="showEditCategoryModal = false">
            <form @submit.prevent="submitEditCategory">
                <div class="bg-white mx-3 rounded-xl overflow-hidden">
                    <TextField
                        v-model="editForm.name"
                        label="Name"
                        placeholder="Category name"
                        required
                    />
                    <PickerField
                        v-model="editForm.group_id"
                        label="Group"
                        :options="groupOptions"
                        placeholder="Select group"
                    />
                    <AmountField
                        v-model="editForm.default_amount"
                        label="Default Amount"
                        :color-by-type="false"
                        placeholder="0.00"
                        :border-bottom="false"
                    />
                </div>

                <!-- Icon Picker -->
                <div class="mx-3 mt-3">
                    <div class="flex items-center justify-between mb-2 px-1">
                        <span class="text-xs font-semibold text-subtle uppercase tracking-wide">Icon</span>
                        <span class="text-2xl">{{ editForm.icon || '📁' }}</span>
                    </div>
                    <div class="bg-white rounded-xl p-3">
                        <div class="grid grid-cols-8 gap-1.5">
                            <button
                                v-for="emoji in emojiGrid"
                                :key="emoji"
                                type="button"
                                @click="editForm.icon = emoji"
                                :class="[
                                    'w-9 h-9 flex items-center justify-center text-xl rounded-lg transition-colors',
                                    editForm.icon === emoji
                                        ? 'bg-primary/20 ring-2 ring-primary'
                                        : 'bg-gray-50 hover:bg-gray-100'
                                ]"
                            >
                                {{ emoji }}
                            </button>
                        </div>
                    </div>
                </div>

                <p class="text-xs text-subtle text-center mt-3 px-4">
                    Default amount is used as reference when budgeting
                </p>

                <!-- Delete Button -->
                <div class="mx-3 mt-4">
                    <button
                        type="button"
                        @click="deleteCategory(editForm.id)"
                        class="w-full py-3 text-expense font-medium text-sm"
                    >
                        Delete Category
                    </button>
                </div>
            </form>

            <template #footer>
                <Button
                    type="button"
                    @click="submitEditCategory"
                    :loading="editForm.processing"
                    full-width
                >
                    Save Changes
                </Button>
            </template>
        </BottomSheet>
    </AppLayout>
</template>
