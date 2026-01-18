<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, reactive } from 'vue';

const props = defineProps({
    categoryGroups: Array,
    defaultMonthlyIncome: Number,
    hasProjections: Boolean,
});

// Projection state
const expectedIncome = ref(props.defaultMonthlyIncome || 0);
const projectionAmounts = reactive({}); // { categoryId: { 1: amount, 2: amount, 3: amount } }

// Toast state
const toast = ref({ show: false, message: '', type: 'success' });

// Initialize projections from props
props.categoryGroups.forEach(group => {
    group.categories.forEach(category => {
        projectionAmounts[category.id] = category.projections || {};
    });
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
    }).format(amount);
};

// Calculate total projected (using projection 1 as primary)
const totalProjected = computed(() => {
    let total = 0;
    props.categoryGroups.forEach(group => {
        group.categories.forEach(category => {
            const projections = projectionAmounts[category.id] || {};
            total += parseFloat(projections['1'] || category.default_amount || 0);
        });
    });
    return total;
});

const leftToAllocate = computed(() => {
    return expectedIncome.value - totalProjected.value;
});

const getProjectionValue = (categoryId, index) => {
    return projectionAmounts[categoryId]?.[index] ?? '';
};

// Get the difference between projection and default
const getProjectionDifference = (category, index) => {
    const projValue = parseFloat(projectionAmounts[category.id]?.[index]) || 0;
    const defaultValue = parseFloat(category.default_amount) || 0;

    if (projValue === 0) return null; // No projection set

    const diff = projValue - defaultValue;
    if (Math.abs(diff) < 0.01) return null; // No meaningful difference

    return diff;
};

const setProjectionValue = (categoryId, index, value) => {
    if (!projectionAmounts[categoryId]) {
        projectionAmounts[categoryId] = {};
    }
    projectionAmounts[categoryId][index] = parseFloat(value) || 0;
};

const saveProjections = () => {
    const projectionsData = [];
    props.categoryGroups.forEach(group => {
        group.categories.forEach(category => {
            if (projectionAmounts[category.id] && Object.keys(projectionAmounts[category.id]).length > 0) {
                projectionsData.push({
                    category_id: category.id,
                    values: projectionAmounts[category.id],
                });
            }
        });
    });

    router.post(route('budget.save-projections'), {
        projections: projectionsData,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showToast('Projections saved', 'success');
        },
    });
};

const clearProjections = () => {
    if (confirm('Clear all projections? This cannot be undone.')) {
        router.post(route('budget.clear-projections'), {}, {
            preserveScroll: true,
            onSuccess: () => {
                // Clear local state
                props.categoryGroups.forEach(group => {
                    group.categories.forEach(category => {
                        projectionAmounts[category.id] = {};
                    });
                });
                showToast('All projections cleared', 'success');
            },
        });
    }
};

const applyProjections = (projectionIndex) => {
    const currentMonth = new Date().toISOString().slice(0, 7);
    router.post(route('budget.apply-projections', { month: currentMonth }), {
        projection_index: projectionIndex,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            showToast(`Applied projection ${projectionIndex} to current month's budget`, 'success');
        },
    });
};

// Check if any projections exist locally
const hasAnyProjections = computed(() => {
    for (const group of props.categoryGroups) {
        for (const category of group.categories) {
            const projections = projectionAmounts[category.id];
            if (projections && Object.keys(projections).length > 0) {
                // Check if any projection has a non-zero value
                for (const key of Object.keys(projections)) {
                    if (projections[key] > 0) return true;
                }
            }
        }
    }
    return false;
});

// Toast helper
const showToast = (message, type = 'success') => {
    toast.value = { show: true, message, type };
    setTimeout(() => {
        toast.value.show = false;
    }, 3000);
};
</script>

<template>
    <Head title="Plan" />

    <AppLayout>
        <template #title>Plan</template>

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
                    class="fixed top-4 left-1/2 -translate-x-1/2 z-50 px-4 py-2 rounded-lg shadow-lg text-white text-sm"
                    :class="{
                        'bg-primary': toast.type === 'success',
                        'bg-blue-500': toast.type === 'info',
                        'bg-expense': toast.type === 'error',
                    }"
                >
                    {{ toast.message }}
                </div>
            </Transition>

            <!-- Projection Header (Sticky) -->
            <div class="sticky top-0 z-10 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-card p-4 space-y-3">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-body">Plan Your Budget</h3>
                    <div class="flex gap-2">
                        <button
                            @click="saveProjections"
                            class="px-3 py-1 bg-primary text-white rounded text-sm font-medium hover:bg-primary/90"
                        >
                            Save
                        </button>
                        <button
                            @click="clearProjections"
                            class="px-3 py-1 bg-gray-200 text-body rounded text-sm font-medium hover:bg-gray-300"
                        >
                            Clear All
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3 text-center">
                    <div>
                        <div class="text-xs text-subtle uppercase">Expected Income</div>
                        <input
                            v-model.number="expectedIncome"
                            type="number"
                            step="0.01"
                            class="w-full mt-1 px-2 py-1 text-center font-mono font-semibold text-income bg-white border border-gray-200 rounded focus:border-primary focus:outline-none"
                        />
                    </div>
                    <div>
                        <div class="text-xs text-subtle uppercase">Total Projected</div>
                        <div class="mt-1 py-1 font-mono font-semibold text-body">
                            {{ formatCurrency(totalProjected) }}
                        </div>
                    </div>
                    <div>
                        <div class="text-xs text-subtle uppercase">Left to Allocate</div>
                        <div
                            class="mt-1 py-1 font-mono font-semibold"
                            :class="leftToAllocate >= 0 ? 'text-income' : 'text-expense'"
                        >
                            {{ formatCurrency(leftToAllocate) }}
                        </div>
                    </div>
                </div>

                <div v-if="hasAnyProjections" class="flex gap-2 justify-center pt-2">
                    <button
                        @click="applyProjections(1)"
                        class="px-4 py-2 bg-primary text-white rounded-card text-sm font-medium hover:bg-primary/90"
                    >
                        Apply to Current Month
                    </button>
                </div>
            </div>

            <!-- Category Groups -->
            <div v-for="group in categoryGroups" :key="group.id" class="space-y-2">
                <h2 class="text-sm font-semibold text-subtle uppercase tracking-wide px-1">
                    {{ group.name }}
                </h2>

                <div class="bg-surface rounded-card overflow-hidden">
                    <!-- Column Headers -->
                    <div class="grid grid-cols-12 gap-2 px-3 py-2 bg-blue-50 text-xs text-subtle uppercase border-b border-blue-100">
                        <div class="col-span-4">Category</div>
                        <div class="col-span-2 text-right">Default</div>
                        <div class="col-span-2 text-right">Proj 1</div>
                        <div class="col-span-2 text-right">Proj 2</div>
                        <div class="col-span-2 text-right">Proj 3</div>
                    </div>

                    <!-- Category Rows -->
                    <div
                        v-for="category in group.categories"
                        :key="category.id"
                        class="grid grid-cols-12 gap-2 px-3 py-3 items-center border-b border-blue-100 last:border-b-0"
                    >
                        <!-- Category Name -->
                        <div class="col-span-4 flex items-center gap-2 min-w-0">
                            <span v-if="category.icon" class="flex-shrink-0">{{ category.icon }}</span>
                            <span class="font-medium text-body truncate">{{ category.name }}</span>
                        </div>

                        <!-- Default Amount (read-only) -->
                        <div class="col-span-2 text-right font-mono text-sm text-subtle">
                            {{ category.default_amount ? formatCurrency(category.default_amount) : '-' }}
                        </div>

                        <!-- Projection 1 -->
                        <div class="col-span-2">
                            <input
                                :value="getProjectionValue(category.id, '1')"
                                @input="setProjectionValue(category.id, '1', $event.target.value)"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="-"
                                class="w-full px-1 py-1 text-right text-sm font-mono bg-white border border-gray-200 rounded focus:border-primary focus:outline-none"
                            />
                            <div
                                v-if="getProjectionDifference(category, '1') !== null"
                                class="text-[10px] text-right font-mono mt-0.5"
                                :class="getProjectionDifference(category, '1') > 0 ? 'text-income' : 'text-expense'"
                            >
                                {{ getProjectionDifference(category, '1') > 0 ? '+' : '' }}${{ getProjectionDifference(category, '1').toFixed(0) }}
                            </div>
                        </div>

                        <!-- Projection 2 -->
                        <div class="col-span-2">
                            <input
                                :value="getProjectionValue(category.id, '2')"
                                @input="setProjectionValue(category.id, '2', $event.target.value)"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="-"
                                class="w-full px-1 py-1 text-right text-sm font-mono bg-white border border-gray-200 rounded focus:border-primary focus:outline-none"
                            />
                            <div
                                v-if="getProjectionDifference(category, '2') !== null"
                                class="text-[10px] text-right font-mono mt-0.5"
                                :class="getProjectionDifference(category, '2') > 0 ? 'text-income' : 'text-expense'"
                            >
                                {{ getProjectionDifference(category, '2') > 0 ? '+' : '' }}${{ getProjectionDifference(category, '2').toFixed(0) }}
                            </div>
                        </div>

                        <!-- Projection 3 -->
                        <div class="col-span-2">
                            <input
                                :value="getProjectionValue(category.id, '3')"
                                @input="setProjectionValue(category.id, '3', $event.target.value)"
                                type="number"
                                step="0.01"
                                min="0"
                                placeholder="-"
                                class="w-full px-1 py-1 text-right text-sm font-mono bg-white border border-gray-200 rounded focus:border-primary focus:outline-none"
                            />
                            <div
                                v-if="getProjectionDifference(category, '3') !== null"
                                class="text-[10px] text-right font-mono mt-0.5"
                                :class="getProjectionDifference(category, '3') > 0 ? 'text-income' : 'text-expense'"
                            >
                                {{ getProjectionDifference(category, '3') > 0 ? '+' : '' }}${{ getProjectionDifference(category, '3').toFixed(0) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-if="categoryGroups.length === 0"
                class="text-center py-12"
            >
                <div class="text-4xl mb-4">📋</div>
                <h3 class="text-lg font-medium text-body mb-2">No categories yet</h3>
                <p class="text-subtle">
                    Go to Settings to add categories and start planning.
                </p>
            </div>

            <!-- Help Text -->
            <div class="bg-blue-50 border border-blue-200 rounded-card p-4 text-sm text-body">
                <h4 class="font-semibold mb-2">How to use projections</h4>
                <ul class="list-disc list-inside space-y-1 text-subtle">
                    <li>Set your expected monthly income at the top</li>
                    <li>Enter projected amounts for each category (Proj 1 is your primary plan)</li>
                    <li>Use Proj 2 and Proj 3 for alternative scenarios</li>
                    <li>Click "Save" to save your projections</li>
                    <li>Click "Apply to Current Month" to use Projection 1 in your budget</li>
                </ul>
            </div>
        </div>
    </AppLayout>
</template>
