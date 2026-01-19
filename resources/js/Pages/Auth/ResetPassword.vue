<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/Form/InputError.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout title="Reset password">
        <Head title="Reset Password" />

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
                        autocomplete="username"
                        class="text-sm text-body text-right bg-transparent border-none focus:ring-0 focus:outline-none p-0 w-2/3"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.email" />

                <div class="flex items-center justify-between py-4 border-b border-border">
                    <label for="password" class="text-sm text-subtle">New Password</label>
                    <input
                        id="password"
                        type="password"
                        v-model="form.password"
                        required
                        autofocus
                        autocomplete="new-password"
                        class="text-sm text-body text-right bg-transparent border-none focus:ring-0 focus:outline-none p-0 w-2/3"
                        placeholder="••••••••"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.password" />

                <div class="flex items-center justify-between py-4 border-b border-border">
                    <label for="password_confirmation" class="text-sm text-subtle">Confirm Password</label>
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
                class="w-full py-4 mt-6 bg-gradient-to-r from-primary to-primary-light text-body rounded-xl font-semibold text-center shadow-[0_8px_16px_-4px_rgba(126,217,87,0.4)] hover:shadow-[0_10px_20px_-4px_rgba(126,217,87,0.5)] transition-shadow disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span v-if="form.processing">Resetting...</span>
                <span v-else>Reset Password</span>
            </button>
        </form>
    </GuestLayout>
</template>
