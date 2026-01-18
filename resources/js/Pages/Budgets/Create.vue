<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import TextField from '@/Components/Form/TextField.vue';
import AmountField from '@/Components/Form/AmountField.vue';
import Button from '@/Components/Base/Button.vue';

const form = useForm({
    name: '',
    default_monthly_income: '',
});

const submit = () => {
    form.post(route('budgets.store'));
};
</script>

<template>
    <Head title="Create Budget" />

    <div class="min-h-screen bg-surface-secondary flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-6">
                <div class="text-5xl mb-3">💰</div>
                <h1 class="text-2xl font-bold text-body">Create Your Budget</h1>
                <p class="text-subtle mt-2">Let's set up your first budget to start tracking your finances.</p>
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <div class="bg-surface rounded-card overflow-hidden">
                    <TextField
                        v-model="form.name"
                        label="Budget Name"
                        placeholder="e.g., My Budget"
                        variant="subtle"
                        :error="form.errors.name"
                        required
                    />
                    <AmountField
                        v-model="form.default_monthly_income"
                        label="Monthly Income"
                        :color-by-type="false"
                        placeholder="0.00"
                        :border-bottom="false"
                    />
                </div>

                <p class="text-xs text-subtle text-center px-4">
                    Monthly income is optional and helps with budget planning.
                </p>

                <Button
                    type="submit"
                    :loading="form.processing"
                    full-width
                    size="lg"
                >
                    Create Budget
                </Button>
            </form>
        </div>
    </div>
</template>
