<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    members: Array,
    pendingInvites: Array,
    isOwner: Boolean,
    budgetName: String,
});

const showInviteForm = ref(false);
const inviteForm = useForm({
    email: '',
});

const submitInvite = () => {
    inviteForm.post(route('sharing.invite'), {
        preserveScroll: true,
        onSuccess: () => {
            inviteForm.reset();
            showInviteForm.value = false;
        },
    });
};

const cancelInvite = (inviteId) => {
    if (confirm('Cancel this invite?')) {
        router.delete(route('sharing.cancel-invite', inviteId), {
            preserveScroll: true,
        });
    }
};

const removeMember = (member) => {
    if (confirm(`Remove ${member.name} from this budget?`)) {
        router.delete(route('sharing.remove-member', member.id), {
            preserveScroll: true,
        });
    }
};

const getAvatarColor = (name) => {
    const colors = [
        'bg-blue-500',
        'bg-green-500',
        'bg-purple-500',
        'bg-pink-500',
        'bg-indigo-500',
        'bg-teal-500',
        'bg-orange-500',
        'bg-red-500',
    ];
    const index = name.charCodeAt(0) % colors.length;
    return colors[index];
};
</script>

<template>
    <Head title="Sharing" />

    <AppLayout>
        <template #title>Sharing</template>
        <template #header-left>
            <Link :href="route('settings.index')" class="p-2 -ml-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </Link>
        </template>

        <div class="p-4 space-y-6">
            <!-- Budget Name -->
            <div class="text-center">
                <p class="text-sm text-subtle">Sharing</p>
                <h2 class="text-xl font-semibold text-body">{{ budgetName }}</h2>
            </div>

            <!-- People with Access -->
            <div>
                <h3 class="text-sm font-semibold text-subtle uppercase tracking-wide px-1 mb-2">
                    People with Access
                </h3>
                <div class="bg-surface rounded-card divide-y divide-gray-100">
                    <div
                        v-for="member in members"
                        :key="member.id"
                        class="flex items-center justify-between p-4"
                    >
                        <div class="flex items-center gap-3">
                            <!-- Avatar -->
                            <div
                                :class="[
                                    'w-10 h-10 rounded-full flex items-center justify-center text-white font-semibold text-sm',
                                    getAvatarColor(member.name)
                                ]"
                            >
                                {{ member.avatar }}
                            </div>
                            <div>
                                <div class="font-medium text-body flex items-center gap-2">
                                    {{ member.name }}
                                    <span
                                        v-if="member.role === 'owner'"
                                        class="text-xs bg-primary/10 text-primary px-2 py-0.5 rounded-full"
                                    >
                                        Owner
                                    </span>
                                    <span
                                        v-if="member.is_current_user"
                                        class="text-xs text-subtle"
                                    >
                                        (you)
                                    </span>
                                </div>
                                <div class="text-sm text-subtle">{{ member.email }}</div>
                            </div>
                        </div>
                        <!-- Remove button (only for owner, not for themselves) -->
                        <button
                            v-if="isOwner && !member.is_current_user && member.role !== 'owner'"
                            @click="removeMember(member)"
                            class="p-2 text-subtle hover:text-expense hover:bg-gray-100 rounded-full transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Pending Invites (only show if there are any or user is owner) -->
            <div v-if="isOwner && pendingInvites.length > 0">
                <h3 class="text-sm font-semibold text-subtle uppercase tracking-wide px-1 mb-2">
                    Pending Invites
                </h3>
                <div class="bg-surface rounded-card divide-y divide-gray-100">
                    <div
                        v-for="invite in pendingInvites"
                        :key="invite.id"
                        class="flex items-center justify-between p-4"
                    >
                        <div class="flex items-center gap-3">
                            <!-- Placeholder avatar -->
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-body">{{ invite.email }}</div>
                                <div class="text-sm text-subtle">Invited {{ invite.created_at }}</div>
                            </div>
                        </div>
                        <button
                            @click="cancelInvite(invite.id)"
                            class="text-sm text-subtle hover:text-expense transition-colors"
                        >
                            Cancel
                        </button>
                    </div>
                </div>
            </div>

            <!-- Invite Form (only for owner) -->
            <div v-if="isOwner">
                <h3 class="text-sm font-semibold text-subtle uppercase tracking-wide px-1 mb-2">
                    Invite Someone
                </h3>

                <div v-if="!showInviteForm" class="bg-surface rounded-card">
                    <button
                        @click="showInviteForm = true"
                        class="w-full flex items-center gap-3 p-4 text-primary hover:bg-gray-50 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <span class="font-medium">Add a person</span>
                    </button>
                </div>

                <form v-else @submit.prevent="submitInvite" class="bg-surface rounded-card p-4 space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-body mb-1">
                            Email Address
                        </label>
                        <input
                            id="email"
                            v-model="inviteForm.email"
                            type="email"
                            placeholder="Enter email address"
                            class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none"
                            :class="{ 'border-expense': inviteForm.errors.email }"
                        />
                        <p v-if="inviteForm.errors.email" class="mt-1 text-sm text-expense">
                            {{ inviteForm.errors.email }}
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="showInviteForm = false; inviteForm.reset();"
                            class="flex-1 py-3 bg-gray-100 text-body rounded-card font-medium hover:bg-gray-200 transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="inviteForm.processing || !inviteForm.email"
                            class="flex-1 py-3 bg-primary text-white rounded-card font-medium hover:bg-primary/90 transition-colors disabled:opacity-50"
                        >
                            {{ inviteForm.processing ? 'Sending...' : 'Send Invite' }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Non-owner message -->
            <div v-if="!isOwner" class="text-center py-4">
                <p class="text-sm text-subtle">
                    Only the budget owner can invite or remove members.
                </p>
            </div>
        </div>
    </AppLayout>
</template>
