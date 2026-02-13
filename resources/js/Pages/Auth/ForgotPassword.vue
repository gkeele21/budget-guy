<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/Form/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout title="Forgot password?">
        <Head title="Forgot Password" />

        <p class="text-sm text-subtle mb-6">
            No problem. Enter your email and we'll send you a reset link.
        </p>

        <div
            v-if="status"
            class="mb-4 p-3 bg-success/10 border border-success/20 rounded-xl"
        >
            <p class="text-sm text-success text-center">{{ status }}</p>
        </div>

        <form @submit.prevent="submit">
            <!-- Form Fields - FormRow Style -->
            <div class="space-y-1">
                <div class="flex items-center justify-between py-4 border-b border-border">
                    <label for="email" class="text-sm text-subtle">Email</label>
                    <input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        class="text-sm text-body text-right bg-transparent border-none focus:ring-0 focus:outline-none p-0 w-2/3"
                        placeholder="you@example.com"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                :disabled="form.processing"
                class="w-full py-4 mt-6 bg-primary text-white rounded-xl font-semibold text-center disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span v-if="form.processing">Sending...</span>
                <span v-else>Send Reset Link</span>
            </button>

            <!-- Back to Login -->
            <p class="mt-6 text-sm text-subtle text-center">
                Remember your password?
                <Link
                    :href="route('login')"
                    class="text-primary font-semibold hover:text-primary/80 transition-colors"
                >
                    Sign in
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
