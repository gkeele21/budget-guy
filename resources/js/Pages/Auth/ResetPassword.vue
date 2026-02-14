<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import TextField from '@/Components/Form/TextField.vue';
import Button from '@/Components/Base/Button.vue';
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
            <TextField
                v-model="form.email"
                label="Email"
                type="email"
                autocomplete="username"
                required
                :error="form.errors.email"
            />
            <TextField
                v-model="form.password"
                label="New Password"
                type="password"
                placeholder="••••••••"
                autocomplete="new-password"
                autofocus
                required
                :error="form.errors.password"
            />
            <TextField
                v-model="form.password_confirmation"
                label="Confirm Password"
                type="password"
                placeholder="••••••••"
                autocomplete="new-password"
                required
                :border-bottom="false"
                :error="form.errors.password_confirmation"
            />

            <div class="mt-6">
                <Button type="submit" :loading="form.processing" full-width size="lg">
                    Reset Password
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
