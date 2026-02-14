<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextField from '@/Components/Form/TextField.vue';
import Button from '@/Components/Base/Button.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: true,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout title="Welcome back">
        <Head title="Log in" />

        <div v-if="status" class="mb-4 p-3 bg-success/10 border border-success/20 rounded-xl">
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
                :error="form.errors.email"
            />
            <TextField
                v-model="form.password"
                label="Password"
                type="password"
                placeholder="••••••••"
                autocomplete="current-password"
                required
                :border-bottom="false"
                :error="form.errors.password"
            />

            <!-- Forgot Password -->
            <div class="flex justify-end mt-3 mb-4">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-primary font-medium hover:text-primary/80 transition-colors"
                >
                    Forgot password?
                </Link>
            </div>

            <Button type="submit" :loading="form.processing" full-width size="lg">
                Sign In
            </Button>

            <!-- Sign Up Link -->
            <p class="mt-6 text-sm text-subtle text-center">
                Don't have an account?
                <Link
                    :href="route('register')"
                    class="text-primary font-semibold hover:text-primary/80 transition-colors"
                >
                    Sign up
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
