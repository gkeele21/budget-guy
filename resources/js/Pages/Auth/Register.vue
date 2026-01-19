<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/Form/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout title="Create Account">
        <Head title="Register" />

        <form @submit.prevent="submit">
            <!-- Form Fields - FormRow Style -->
            <div class="space-y-1">
                <div class="flex items-center justify-between py-4 border-b border-border">
                    <label for="name" class="text-sm text-subtle">Name</label>
                    <input
                        id="name"
                        type="text"
                        v-model="form.name"
                        required
                        autofocus
                        autocomplete="name"
                        class="text-sm text-body text-right bg-transparent border-none focus:ring-0 focus:outline-none p-0 w-2/3"
                        placeholder="Jane Smith"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.name" />

                <div class="flex items-center justify-between py-4 border-b border-border">
                    <label for="email" class="text-sm text-subtle">Email</label>
                    <input
                        id="email"
                        type="email"
                        v-model="form.email"
                        required
                        autocomplete="username"
                        class="text-sm text-body text-right bg-transparent border-none focus:ring-0 focus:outline-none p-0 w-2/3"
                        placeholder="you@example.com"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.email" />

                <div class="flex items-center justify-between py-4 border-b border-border">
                    <label for="password" class="text-sm text-subtle">Password</label>
                    <input
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        class="text-sm text-body text-right bg-transparent border-none focus:ring-0 focus:outline-none p-0 w-2/3"
                        placeholder="••••••••"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.password" />

                <div class="flex items-center justify-between py-4 border-b border-border">
                    <label for="password_confirmation" class="text-sm text-subtle">Confirm</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        class="text-sm text-body text-right bg-transparent border-none focus:ring-0 focus:outline-none p-0 w-2/3"
                        placeholder="••••••••"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.password_confirmation" />
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                :disabled="form.processing"
                class="w-full mt-6 py-4 bg-gradient-to-r from-primary to-primary-light text-body rounded-xl font-semibold text-center shadow-[0_8px_16px_-4px_rgba(126,217,87,0.4)] hover:shadow-[0_10px_20px_-4px_rgba(126,217,87,0.5)] transition-shadow disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span v-if="form.processing">Creating account...</span>
                <span v-else>Create Account</span>
            </button>

            <!-- Sign In Link -->
            <p class="mt-6 text-sm text-subtle text-center">
                Already have an account?
                <Link
                    :href="route('login')"
                    class="text-secondary font-semibold hover:text-secondary/80 transition-colors"
                >
                    Sign in
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>
