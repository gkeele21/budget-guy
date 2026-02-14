<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextField from '@/Components/Form/TextField.vue';
import Button from '@/Components/Base/Button.vue';
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
            <TextField
                v-model="form.email"
                label="Email"
                type="email"
                placeholder="you@example.com"
                autocomplete="username"
                autofocus
                required
                :border-bottom="false"
                :error="form.errors.email"
            />

            <div class="mt-6">
                <Button type="submit" :loading="form.processing" full-width size="lg">
                    Send Reset Link
                </Button>
            </div>

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
