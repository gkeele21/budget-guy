<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import TextField from '@/Components/Form/TextField.vue';
import AmountField from '@/Components/Form/AmountField.vue';
import Button from '@/Components/Base/Button.vue';

const currentStep = ref(1);
const totalSteps = 3;

const form = useForm({
    budget_name: '',
    account_name: '',
    account_type: 'checking',
    account_balance: '',
    use_template: 'basic',
});

const canProceedStep1 = computed(() => form.budget_name.length > 0);
const canProceedStep2 = computed(() => true); // Account is optional
const canProceedStep3 = computed(() => true); // Template selection

const nextStep = () => {
    if (currentStep.value < totalSteps) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const submit = () => {
    form.post(route('onboarding.store'));
};

const accountTypes = [
    { value: 'checking', label: 'Checking', icon: '🏦' },
    { value: 'savings', label: 'Savings', icon: '💰' },
    { value: 'credit_card', label: 'Credit Card', icon: '💳' },
    { value: 'cash', label: 'Cash', icon: '💵' },
];

const templates = [
    {
        value: 'basic',
        label: 'Basic',
        description: 'Perfect for getting started. Includes common categories for bills, everyday spending, and savings.',
    },
    {
        value: 'detailed',
        label: 'Detailed',
        description: 'More granular categories for tracking housing, utilities, food, transportation, and more.',
    },
    {
        value: 'minimal',
        label: 'Minimal',
        description: 'Just the essentials. Three groups with basic categories you can customize later.',
    },
];
</script>

<template>
    <Head title="Setup" />

    <div class="min-h-screen bg-gray-50 flex flex-col">
        <!-- Progress Bar -->
        <div class="bg-white border-b border-gray-100 px-6 py-4">
            <div class="max-w-md mx-auto">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-body">Step {{ currentStep }} of {{ totalSteps }}</span>
                    <Link
                        :href="route('onboarding.skip')"
                        method="post"
                        as="button"
                        class="text-sm text-subtle hover:text-body"
                    >
                        Skip setup
                    </Link>
                </div>
                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div
                        class="h-full bg-primary transition-all duration-300"
                        :style="{ width: `${(currentStep / totalSteps) * 100}%` }"
                    ></div>
                </div>
            </div>
        </div>

        <!-- Step Content -->
        <div class="flex-1 flex flex-col items-center justify-center px-6 py-8">
            <div class="w-full max-w-md">
                <!-- Step 1: Budget Name -->
                <div v-if="currentStep === 1" class="space-y-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">💰</span>
                        </div>
                        <h2 class="text-2xl font-bold text-body mb-2">Name your budget</h2>
                        <p class="text-subtle">
                            Choose a name for your budget. You can change this later.
                        </p>
                    </div>

                    <div class="bg-white rounded-card overflow-hidden">
                        <TextField
                            v-model="form.budget_name"
                            label="Budget Name"
                            placeholder="e.g., My Budget"
                            variant="subtle"
                            :border-bottom="false"
                            :error="form.errors.budget_name"
                        />
                    </div>
                </div>

                <!-- Step 2: First Account -->
                <div v-if="currentStep === 2" class="space-y-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-income/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">🏦</span>
                        </div>
                        <h2 class="text-2xl font-bold text-body mb-2">Add your first account</h2>
                        <p class="text-subtle">
                            Add a checking, savings, or credit card account. You can add more later.
                        </p>
                    </div>

                    <div class="space-y-4">
                        <!-- Account Name & Balance -->
                        <div class="bg-white rounded-card overflow-hidden">
                            <TextField
                                v-model="form.account_name"
                                label="Account Name"
                                placeholder="e.g., Main Checking"
                                variant="subtle"
                            />
                            <AmountField
                                v-model="form.account_balance"
                                label="Current Balance"
                                :color-by-type="false"
                                placeholder="0.00"
                                :border-bottom="false"
                            />
                        </div>

                        <!-- Account Type -->
                        <div>
                            <div class="text-xs font-semibold text-subtle uppercase tracking-wide mb-2 px-1">
                                Account Type
                            </div>
                            <div class="grid grid-cols-4 gap-2">
                                <button
                                    v-for="type in accountTypes"
                                    :key="type.value"
                                    type="button"
                                    @click="form.account_type = type.value"
                                    class="flex flex-col items-center p-3 rounded-xl border-2 transition-colors bg-white"
                                    :class="form.account_type === type.value
                                        ? 'border-primary bg-primary/10'
                                        : 'border-gray-200'"
                                >
                                    <span class="text-2xl mb-1">{{ type.icon }}</span>
                                    <span
                                        :class="[
                                            'text-xs font-semibold',
                                            form.account_type === type.value ? 'text-primary' : 'text-subtle'
                                        ]"
                                    >{{ type.label }}</span>
                                </button>
                            </div>
                        </div>

                        <p class="text-sm text-subtle text-center">
                            You can skip this step and add accounts later.
                        </p>
                    </div>
                </div>

                <!-- Step 3: Category Template -->
                <div v-if="currentStep === 3" class="space-y-6">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-2xl">📋</span>
                        </div>
                        <h2 class="text-2xl font-bold text-body mb-2">Choose your categories</h2>
                        <p class="text-subtle">
                            Start with a template. You can customize these anytime.
                        </p>
                    </div>

                    <div class="space-y-3">
                        <button
                            v-for="template in templates"
                            :key="template.value"
                            type="button"
                            @click="form.use_template = template.value"
                            class="w-full text-left p-4 border-2 rounded-card transition-colors bg-white"
                            :class="form.use_template === template.value
                                ? 'border-primary bg-primary/5'
                                : 'border-gray-200 hover:border-gray-300'"
                        >
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-semibold text-body">{{ template.label }}</span>
                                <div
                                    v-if="form.use_template === template.value"
                                    class="w-5 h-5 bg-primary rounded-full flex items-center justify-center"
                                >
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </div>
                            <p class="text-sm text-subtle">{{ template.description }}</p>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Navigation -->
        <div class="bg-white border-t border-gray-100 px-6 py-4">
            <div class="max-w-md mx-auto flex gap-3">
                <Button
                    v-if="currentStep > 1"
                    variant="secondary"
                    @click="prevStep"
                    class="flex-1"
                    size="lg"
                >
                    Back
                </Button>

                <Button
                    v-if="currentStep < totalSteps"
                    @click="nextStep"
                    :disabled="(currentStep === 1 && !canProceedStep1) || (currentStep === 2 && !canProceedStep2)"
                    class="flex-1"
                    size="lg"
                >
                    Continue
                </Button>

                <Button
                    v-if="currentStep === totalSteps"
                    @click="submit"
                    :loading="form.processing"
                    class="flex-1"
                    size="lg"
                >
                    Get Started
                </Button>
            </div>
        </div>
    </div>
</template>
