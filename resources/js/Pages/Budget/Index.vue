<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Button from '@/Components/Base/Button.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, reactive, nextTick } from 'vue';

const props = defineProps({
    month: String,
    categoryGroups: Array,
    summary: Object,
    defaultMonthlyIncome: Number,
});

// Track budget amounts reactively for each category
const budgetAmounts = reactive({});

// Move money modal state
const showMoveMoneyModal = ref(false);
const moveMoneyTarget = ref(null); // The overspent category
const moveMoneyAmount = ref(0);
const selectedSourceCategory = ref(null);

// Toast state
const toast = ref({ show: false, message: '', type: 'success' });

// Copy last month confirmation modal
const showCopyConfirm = ref(false);

// Track edited amounts (for green border visual feedback)
const editedAmounts = reactive({});

// Track which field is being edited (to show input vs formatted)
const editingField = ref(null);

// Global toggle for showing category details (default/avg)
const showDetails = ref(false);

// Calculate group totals
const getGroupTotals = (group) => {
    let budgeted = 0;
    let spent = 0;
    // Handle both array and object (Laravel Collection) formats
    const categories = Array.isArray(group.categories) ? group.categories : Object.values(group.categories);
    categories.forEach(category => {
        budgeted += budgetAmounts[category.id] || 0;
        spent += category.spent || 0;
    });
    return {
        budgeted,
        spent,
        available: budgeted - spent,
    };
};

// Initialize budget amounts from props
props.categoryGroups.forEach(group => {
    // Handle both array and object (Laravel Collection) formats
    const categories = Array.isArray(group.categories) ? group.categories : Object.values(group.categories);
    categories.forEach(category => {
        budgetAmounts[category.id] = category.budgeted;
    });
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};

// Format number without $ sign (for tight columns)
const formatNumber = (amount) => {
    return new Intl.NumberFormat('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(amount || 0);
};

const formatMonth = (monthStr) => {
    const [year, month] = monthStr.split('-');
    const date = new Date(year, month - 1, 1); // month is 0-indexed
    return date.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
};

const previousMonth = computed(() => {
    const [year, month] = props.month.split('-').map(Number);
    const date = new Date(year, month - 1, 1); // month is 0-indexed
    date.setMonth(date.getMonth() - 1);
    return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
});

const nextMonth = computed(() => {
    const [year, month] = props.month.split('-').map(Number);
    const date = new Date(year, month - 1, 1); // month is 0-indexed
    date.setMonth(date.getMonth() + 1);
    return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
});

const navigateMonth = (month) => {
    router.get(route('budget.index', { month }));
};

const saveAmount = (categoryId) => {
    editingField.value = null;
    const amount = budgetAmounts[categoryId] || 0;
    router.put(route('budget.update', { month: props.month }), {
        budgets: [{ category_id: categoryId, amount: amount }],
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Show green border briefly to indicate successful save
            editedAmounts[categoryId] = true;
            setTimeout(() => {
                editedAmounts[categoryId] = false;
            }, 2000);
        },
    });
};

const startEditing = (categoryId) => {
    editingField.value = categoryId;
    nextTick(() => {
        const input = document.querySelector(`input[type="number"]`);
        if (input) {
            input.focus();
            input.select();
        }
    });
};

const isOverspent = (available) => available < 0;

const getAvailable = (category) => {
    const budgeted = budgetAmounts[category.id] || 0;
    return budgeted - category.spent;
};

// Check if any budget amounts exist for the current month
const hasExistingBudgetAmounts = computed(() => {
    for (const group of props.categoryGroups) {
        for (const category of group.categories) {
            if (category.budgeted > 0) {
                return true;
            }
        }
    }
    return false;
});

// Copy last month's budget - check for existing amounts first
const initiaCopyLastMonth = () => {
    if (hasExistingBudgetAmounts.value) {
        showCopyConfirm.value = true;
    } else {
        doCopyLastMonth();
    }
};

const doCopyLastMonth = () => {
    showCopyConfirm.value = false;
    router.post(route('budget.copy-last-month', { month: props.month }), {}, {
        preserveScroll: true,
        onSuccess: () => {
            showToast('Copied budget from last month', 'success');
        },
    });
};

// Get all categories with surplus (positive available)
const categoriesWithSurplus = computed(() => {
    const result = [];
    props.categoryGroups.forEach(group => {
        group.categories.forEach(category => {
            const available = getAvailable(category);
            if (available > 0) {
                result.push({
                    ...category,
                    groupName: group.name,
                    available: available,
                });
            }
        });
    });
    return result.sort((a, b) => b.available - a.available);
});

// Open move money modal for an overspent category
const openMoveMoneyModal = (category) => {
    const available = getAvailable(category);
    if (available >= 0) return; // Only open for overspent categories

    moveMoneyTarget.value = {
        ...category,
        overspentBy: Math.abs(available),
    };
    moveMoneyAmount.value = Math.abs(available);
    selectedSourceCategory.value = null;
    showMoveMoneyModal.value = true;
};

// Execute the move money action
const executeMoveMoneyFromCategory = (sourceCategory) => {
    const amountToMove = Math.min(moveMoneyAmount.value, sourceCategory.available);

    router.post(route('budget.move-money', { month: props.month }), {
        from_category_id: sourceCategory.id,
        to_category_id: moveMoneyTarget.value.id,
        amount: amountToMove,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Update local state
            const remaining = moveMoneyAmount.value - amountToMove;
            if (remaining <= 0) {
                showMoveMoneyModal.value = false;
                showToast(`Moved ${formatCurrency(amountToMove)} to ${moveMoneyTarget.value.name}`, 'success');
            } else {
                moveMoneyAmount.value = remaining;
                showToast(`Moved ${formatCurrency(amountToMove)}, still need ${formatCurrency(remaining)}`, 'info');
            }
        },
    });
};

// Projection picker modal state
const showProjectionPicker = ref(false);

// Get which projection indices have values (1, 2, or 3)
const availableProjections = computed(() => {
    const found = new Set();
    for (const group of props.categoryGroups) {
        for (const category of group.categories) {
            if (category.projections) {
                for (const key of Object.keys(category.projections)) {
                    if (category.projections[key] > 0) {
                        found.add(key);
                    }
                }
            }
        }
    }
    return Array.from(found).sort();
});

// Check if any projections exist
const hasProjections = computed(() => {
    return availableProjections.value.length > 0;
});

// Handle quick fill button click
const handleQuickFill = () => {
    if (hasProjections.value) {
        // If only one projection exists, apply it directly
        if (availableProjections.value.length === 1) {
            applyProjections(parseInt(availableProjections.value[0]));
        } else {
            // Multiple projections - show picker
            showProjectionPicker.value = true;
        }
    } else {
        // No projections - copy last month
        initiaCopyLastMonth();
    }
};

const applyProjections = (projectionIndex) => {
    showProjectionPicker.value = false;

    // Check if overwriting existing amounts
    if (hasExistingBudgetAmounts.value) {
        if (!confirm('This will overwrite existing budget amounts. Continue?')) {
            return;
        }
    }

    router.post(route('budget.apply-projections', { month: props.month }), {
        projection_index: projectionIndex,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showToast(`Applied Projection ${projectionIndex} to budget`, 'success');
        },
    });
};

// Toast helper
const showToast = (message, type = 'success') => {
    toast.value = { show: true, message, type };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};
</script>

<template>
    <Head title="Budget" />

    <AppLayout>
        <template #title>Budget</template>

        <div class="p-4 space-y-4">
            <!-- Toast Notification -->
            <Transition
                enter-active-class="transition ease-out duration-300"
                enter-from-class="transform opacity-0 -translate-y-2"
                enter-to-class="transform opacity-100 translate-y-0"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="transform opacity-100 translate-y-0"
                leave-to-class="transform opacity-0 -translate-y-2"
            >
                <div
                    v-if="toast.show"
                    class="fixed top-4 left-1/2 -translate-x-1/2 z-50 px-4 py-2 rounded-lg shadow-lg text-sm"
                    :class="{
                        'bg-primary text-body': toast.type === 'success',
                        'bg-secondary text-inverse': toast.type === 'info',
                        'bg-expense text-inverse': toast.type === 'error',
                    }"
                >
                    {{ toast.message }}
                </div>
            </Transition>

            <!-- Month Selector -->
            <div class="flex items-center justify-between bg-surface rounded-card p-3">
                <button
                    @click="navigateMonth(previousMonth)"
                    class="p-2 hover:bg-surface-secondary rounded-full"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <span class="font-semibold text-body">{{ formatMonth(month) }}</span>
                <button
                    @click="navigateMonth(nextMonth)"
                    class="p-2 hover:bg-surface-secondary rounded-full"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>

            <!-- Options Row: Toggle + Quick Fill -->
            <div class="flex items-center justify-between">
                <!-- Show Details Toggle -->
                <div
                    @click="showDetails = !showDetails"
                    class="flex items-center gap-2 cursor-pointer select-none"
                >
                    <div
                        class="relative w-8 h-[18px] rounded-full transition-colors"
                        :class="showDetails ? 'bg-primary' : 'bg-border'"
                    >
                        <div
                            class="absolute top-[2px] left-[2px] w-[14px] h-[14px] bg-white rounded-full shadow transition-transform"
                            :class="{ 'translate-x-[14px]': showDetails }"
                        ></div>
                    </div>
                    <span class="text-xs text-subtle">Show defaults & averages</span>
                </div>

                <!-- Quick Fill Button -->
                <Button
                    variant="ghost"
                    size="sm"
                    @click="handleQuickFill"
                >
                    {{ hasProjections ? 'Apply Projections' : 'Copy Last Month' }}
                </Button>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-2 gap-2">
                <div class="bg-surface rounded-card p-3 text-center">
                    <div class="text-xs text-subtle uppercase">Budgeted</div>
                    <div class="font-mono font-semibold text-body">
                        {{ formatCurrency(summary.budgeted) }}
                    </div>
                </div>
                <div class="bg-surface rounded-card p-3 text-center">
                    <div class="text-xs text-subtle uppercase">Spent</div>
                    <div class="font-mono font-semibold text-expense">
                        {{ formatCurrency(summary.spent) }}
                    </div>
                </div>
            </div>

            <!-- Ready to Assign -->
            <div
                class="rounded-card p-4 flex items-center justify-between"
                :class="summary.toBudget >= 0 ? 'bg-primary' : 'bg-expense'"
            >
                <div class="text-xs uppercase tracking-wider text-inverse/90">
                    Ready to Assign
                </div>
                <div class="font-mono text-2xl font-semibold text-inverse">
                    {{ formatCurrency(summary.toBudget) }}
                </div>
            </div>

            <!-- Category Groups -->
            <div v-for="group in categoryGroups" :key="group.id" class="space-y-2">
                <h2 class="text-sm font-semibold text-subtle uppercase tracking-wide px-1">
                    {{ group.name }}
                </h2>

                <div class="bg-surface rounded-card overflow-hidden">
                    <!-- Column Headers -->
                    <div class="grid grid-cols-12 gap-1 px-3 py-2 bg-surface-secondary text-xs text-subtle uppercase border-b border-border">
                        <div class="col-span-4">Category</div>
                        <div class="col-span-3 text-right">Budget</div>
                        <div class="col-span-2 text-right">Spent</div>
                        <div class="col-span-3 text-right">Balance</div>
                    </div>

                    <!-- Category Rows -->
                    <div
                        v-for="category in group.categories"
                        :key="category.id"
                        class="border-b border-border last:border-b-0"
                    >
                        <!-- Main Row -->
                        <div class="grid grid-cols-12 gap-1 px-3 pt-3 pb-1 items-center">
                            <!-- Category Name (Clickable) -->
                            <a
                                :href="route('budget.category-detail', { month: month, category: category.id })"
                                class="col-span-4 flex items-center gap-1 min-w-0 hover:text-primary transition-colors"
                            >
                                <span v-if="category.icon" class="flex-shrink-0 text-sm">{{ category.icon }}</span>
                                <span class="text-sm text-body truncate hover:text-primary">{{ category.name }}</span>
                            </a>

                            <!-- Budgeted (Editable) -->
                            <div class="col-span-3">
                                <input
                                    v-if="editingField === category.id"
                                    v-model.number="budgetAmounts[category.id]"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    class="w-full px-1 py-1 text-right text-sm bg-surface rounded border border-primary outline-none"
                                    @blur="saveAmount(category.id)"
                                    @keyup.enter="$event.target.blur()"
                                />
                                <div
                                    v-else
                                    @click="startEditing(category.id)"
                                    :class="[
                                        'w-full px-1 py-1 text-right text-sm rounded cursor-text transition-colors',
                                        editedAmounts[category.id]
                                            ? 'border-2 border-income bg-primary-bg'
                                            : 'border border-transparent hover:bg-surface-secondary'
                                    ]"
                                >
                                    {{ formatNumber(budgetAmounts[category.id]) }}
                                </div>
                            </div>

                            <!-- Spent -->
                            <div class="col-span-2 text-right text-sm text-subtle">
                                {{ formatNumber(category.spent) }}
                            </div>

                            <!-- Balance/Available (Clickable if overspent) -->
                            <div
                                class="col-span-3 text-right text-sm font-semibold"
                                :class="[
                                    isOverspent(getAvailable(category)) ? 'text-expense cursor-pointer hover:underline' : 'text-income',
                                ]"
                                @click="isOverspent(getAvailable(category)) && openMoveMoneyModal(category)"
                            >
                                {{ formatCurrency(getAvailable(category)) }}
                            </div>
                        </div>

                        <!-- Detail Row (Default & Avg Spent) -->
                        <div
                            v-if="showDetails && (category.default_amount > 0 || category.avg_spent > 0)"
                            class="grid grid-cols-12 gap-1 px-3 pb-2 items-center"
                        >
                            <div class="col-span-4"></div>
                            <div class="col-span-8 flex items-center gap-3 text-xs text-subtle">
                                <span v-if="category.default_amount > 0">
                                    Default: {{ formatNumber(category.default_amount) }}
                                </span>
                                <span v-if="category.default_amount > 0 && category.avg_spent > 0">•</span>
                                <span v-if="category.avg_spent > 0">
                                    Avg: {{ formatNumber(category.avg_spent) }}/mo
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Group Totals Row -->
                    <div class="grid grid-cols-12 gap-1 px-3 py-2 bg-surface-secondary text-xs font-semibold border-t border-border">
                        <div class="col-span-4 text-subtle uppercase">Total</div>
                        <div class="col-span-3 text-right text-body">
                            {{ formatNumber(getGroupTotals(group).budgeted) }}
                        </div>
                        <div class="col-span-2 text-right text-subtle">
                            {{ formatNumber(getGroupTotals(group).spent) }}
                        </div>
                        <div
                            class="col-span-3 text-right"
                            :class="getGroupTotals(group).available >= 0 ? 'text-income' : 'text-expense'"
                        >
                            {{ formatCurrency(getGroupTotals(group).available) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-if="categoryGroups.length === 0"
                class="text-center py-12"
            >
                <div class="text-4xl mb-4">📊</div>
                <h3 class="text-lg font-medium text-body mb-2">No categories yet</h3>
                <p class="text-subtle">
                    Go to Settings to add categories and start budgeting.
                </p>
            </div>
        </div>

        <!-- Move Money Modal -->
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
                    v-if="showMoveMoneyModal"
                    class="fixed inset-0 z-50 flex items-end justify-center bg-black/50"
                    @click.self="showMoveMoneyModal = false"
                >
                    <Transition
                        enter-active-class="transition ease-out duration-200"
                        enter-from-class="transform translate-y-full"
                        enter-to-class="transform translate-y-0"
                        leave-active-class="transition ease-in duration-150"
                        leave-from-class="transform translate-y-0"
                        leave-to-class="transform translate-y-full"
                    >
                        <div
                            v-if="showMoveMoneyModal"
                            class="w-full max-w-lg bg-surface rounded-t-2xl max-h-[80vh] flex flex-col"
                        >
                            <!-- Modal Header -->
                            <div class="p-4 border-b border-border">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-semibold text-body">
                                        {{ moveMoneyTarget?.icon }} {{ moveMoneyTarget?.name }}
                                    </h3>
                                    <button
                                        @click="showMoveMoneyModal = false"
                                        class="p-2 hover:bg-surface-secondary rounded-full"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-sm text-expense mt-1">
                                    Over by {{ formatCurrency(moveMoneyTarget?.overspentBy || 0) }}
                                </p>
                                <p class="text-xs text-subtle mt-2">
                                    Tap a category below to move funds
                                </p>
                            </div>

                            <!-- Categories with Surplus -->
                            <div class="flex-1 overflow-y-auto p-4 space-y-2">
                                <div
                                    v-for="category in categoriesWithSurplus"
                                    :key="category.id"
                                    @click="executeMoveMoneyFromCategory(category)"
                                    class="flex items-center justify-between p-3 bg-surface-secondary rounded-lg cursor-pointer hover:bg-surface-secondary transition-colors"
                                >
                                    <div class="flex items-center gap-2">
                                        <span v-if="category.icon">{{ category.icon }}</span>
                                        <div>
                                            <div class="font-medium text-body">{{ category.name }}</div>
                                            <div class="text-xs text-subtle">{{ category.groupName }}</div>
                                        </div>
                                    </div>
                                    <div class="font-mono text-sm font-semibold text-income">
                                        {{ formatCurrency(category.available) }}
                                    </div>
                                </div>

                                <div
                                    v-if="categoriesWithSurplus.length === 0"
                                    class="text-center py-8 text-subtle"
                                >
                                    No categories with available funds
                                </div>
                            </div>

                            <!-- Modal Footer -->
                            <div class="p-4 border-t border-border">
                                <button
                                    @click="showMoveMoneyModal = false"
                                    class="w-full py-3 bg-surface-secondary text-body rounded-card font-medium hover:bg-border transition-colors"
                                >
                                    Done
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>
            </Transition>
        </Teleport>

        <!-- Copy Last Month Confirmation Modal -->
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
                    v-if="showCopyConfirm"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                    @click.self="showCopyConfirm = false"
                >
                    <div class="w-full max-w-sm bg-surface rounded-2xl p-6 space-y-4">
                        <h3 class="text-lg font-semibold text-body">Overwrite existing amounts?</h3>
                        <p class="text-subtle">
                            This month already has budget amounts. Copying from last month will overwrite them.
                        </p>
                        <div class="flex gap-3">
                            <button
                                @click="showCopyConfirm = false"
                                class="flex-1 py-3 bg-surface-secondary text-body rounded-card font-medium hover:bg-border transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                @click="doCopyLastMonth"
                                class="flex-1 py-3 bg-primary text-body rounded-card font-medium hover:bg-primary/90 transition-colors"
                            >
                                Overwrite
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>

        <!-- Projection Picker Modal -->
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
                    v-if="showProjectionPicker"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
                    @click.self="showProjectionPicker = false"
                >
                    <div class="w-full max-w-sm bg-surface rounded-2xl p-6 space-y-4">
                        <h3 class="text-lg font-semibold text-body">Choose Projection</h3>
                        <p class="text-subtle text-sm">
                            Select which projection to apply to this month's budget.
                        </p>
                        <div class="space-y-2">
                            <button
                                v-for="projIndex in availableProjections"
                                :key="projIndex"
                                @click="applyProjections(parseInt(projIndex))"
                                class="w-full py-3 bg-primary text-body rounded-card font-medium hover:bg-primary/90 transition-colors"
                            >
                                Projection {{ projIndex }}
                            </button>
                        </div>
                        <button
                            @click="showProjectionPicker = false"
                            class="w-full py-3 bg-surface-secondary text-body rounded-card font-medium hover:bg-border transition-colors"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </AppLayout>
</template>
