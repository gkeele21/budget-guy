<script setup>
import Button from '@/Components/Base/Button.vue';
import InputError from '@/Components/Form/InputError.vue';
import InputLabel from '@/Components/Form/InputLabel.vue';
import TextInput from '@/Components/Form/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-body">
                Delete Account
            </h2>

            <p class="mt-1 text-sm text-subtle">
                Once your account is deleted, all of its resources and data will
                be permanently deleted. Before deleting your account, please
                download any data or information that you wish to retain.
            </p>
        </header>

        <Button variant="danger" @click="confirmUserDeletion">Delete Account</Button>

        <!-- Modal -->
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
                    v-if="confirmingUserDeletion"
                    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4"
                    @click.self="closeModal"
                >
                    <div class="bg-surface rounded-2xl p-6 max-w-md w-full">
                        <h2 class="text-lg font-medium text-body">
                            Are you sure you want to delete your account?
                        </h2>

                        <p class="mt-1 text-sm text-subtle">
                            Once your account is deleted, all of its resources and data
                            will be permanently deleted. Please enter your password to
                            confirm you would like to permanently delete your account.
                        </p>

                        <div class="mt-6">
                            <InputLabel
                                for="password"
                                value="Password"
                                class="sr-only"
                            />

                            <TextInput
                                id="password"
                                ref="passwordInput"
                                v-model="form.password"
                                type="password"
                                class="mt-1 block w-full"
                                placeholder="Password"
                                @keyup.enter="deleteUser"
                            />

                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <Button variant="secondary" @click="closeModal">
                                Cancel
                            </Button>

                            <Button
                                variant="danger"
                                :disabled="form.processing"
                                :loading="form.processing"
                                @click="deleteUser"
                            >
                                Delete Account
                            </Button>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </section>
</template>
