<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import { useSpeechRecognition } from '@/Composables/useSpeechRecognition.js';
import Button from '@/Components/Base/Button.vue';

const props = defineProps({
    show: { type: Boolean, default: false },
});

const emit = defineEmits(['close', 'created']);

const { isListening, transcript, interimTranscript, error: speechError, start, stop, abort, reset, onEnd } = useSpeechRecognition();

// Overlay states: idle, listening, processing, error, success
const state = ref('idle');
const errorMessage = ref('');
const createdGroups = ref([]);

// Silence timeout — auto-close if nothing heard for 5s, or auto-stop after 5s pause
let silenceTimer = null;

function startSilenceTimer() {
    clearSilenceTimer();
    silenceTimer = setTimeout(() => {
        if (state.value !== 'listening') return;

        if (transcript.value || interimTranscript.value) {
            // User spoke then went silent — auto-finish
            stop();
        } else {
            // Never spoke at all — show error
            abort();
            state.value = 'error';
            errorMessage.value = "I didn't hear anything. Try again?";
        }
    }, 5000);
}

function clearSilenceTimer() {
    if (silenceTimer) {
        clearTimeout(silenceTimer);
        silenceTimer = null;
    }
}

// Restart silence timer on speech activity
watch([transcript, interimTranscript], () => {
    if (state.value === 'listening') {
        startSilenceTimer();
    }
});

// Start listening when overlay opens
watch(() => props.show, (isOpen) => {
    if (isOpen) {
        startListening();
    } else {
        clearSilenceTimer();
        state.value = 'idle';
        errorMessage.value = '';
        createdGroups.value = [];
        reset();
    }
});

// Handle escape key
const closeOnEscape = (e) => {
    if (e.key === 'Escape' && props.show) {
        handleClose();
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

// When speech ends, send to backend
onEnd(async (finalTranscript) => {
    if (!finalTranscript.trim()) {
        state.value = 'error';
        errorMessage.value = "I didn't hear anything. Try again?";
        return;
    }
    await sendToBackend(finalTranscript);
});

function startListening() {
    state.value = 'listening';
    reset();
    start();
    startSilenceTimer();
}

async function sendToBackend(text) {
    state.value = 'processing';

    try {
        const { data } = await window.axios.post(route('categories.voice.parse'), {
            transcript: text,
        });

        if (data.status === 'created') {
            createdGroups.value = data.groups;
            state.value = 'success';

            setTimeout(() => {
                emit('created', { groups: data.groups });
            }, 1500);
        } else {
            state.value = 'error';
            errorMessage.value = data.message || "Couldn't understand that.";
        }
    } catch (e) {
        state.value = 'error';
        errorMessage.value = 'Something went wrong. Please try again.';
    }
}

function handleClose() {
    if (isListening.value) {
        abort();
    }
    emit('close');
}

function finishListening() {
    stop();
}

function tryAgain() {
    startListening();
}

// Display text: show interim while listening, final when done
const displayTranscript = () => {
    if (interimTranscript.value) {
        return transcript.value
            ? `${transcript.value} ${interimTranscript.value}`
            : interimTranscript.value;
    }
    return transcript.value;
};

const isServiceError = () => errorMessage.value.includes('AI service') || errorMessage.value.includes('credits');

const totalCategories = () => {
    return createdGroups.value.reduce((sum, g) => sum + g.categories.length, 0);
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
};
</script>

<template>
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
                v-if="show"
                class="fixed inset-0 z-50 flex items-center justify-center p-6"
            >
                <!-- Backdrop -->
                <div
                    class="absolute inset-0 bg-black/75"
                    @click="(state === 'listening' || state === 'error') ? handleClose() : null"
                />

                <!-- LISTENING STATE -->
                <div v-if="state === 'listening'" class="relative w-full max-w-sm bg-surface rounded-2xl p-8 text-center">
                    <!-- Budget Guy avatar with pulse -->
                    <div class="relative w-[88px] h-[88px] mx-auto mb-4">
                        <div class="avatar-pulse-ring"></div>
                        <div class="avatar-pulse-ring avatar-pulse-ring-2"></div>
                        <div class="avatar-pulse-ring avatar-pulse-ring-3"></div>
                        <div class="w-[72px] h-[72px] rounded-full absolute top-2 left-2 overflow-hidden shadow-[0_0_0_3px_rgb(var(--color-primary)/0.5)] z-[2]">
                            <img src="/images/Avatar.png" alt="Budget Guy" class="w-full h-full object-cover" />
                        </div>
                        <div class="avatar-mic-indicator">
                            <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                            </svg>
                        </div>
                    </div>

                    <p class="text-body font-semibold mb-2">Budget Guy is listening...</p>
                    <div class="bg-surface-inset rounded-2xl rounded-bl-sm px-3.5 py-2.5 mx-2 mb-3 text-left relative">
                        <div class="absolute -top-1.5 left-5 w-3 h-3 bg-surface-inset rotate-45 rounded-sm"></div>
                        <p v-if="displayTranscript()" class="text-body text-sm italic">
                            "{{ displayTranscript() }}"
                        </p>
                        <p v-else class="text-subtle text-sm">
                            Try: "Bills group with Rent $1500, Electric $150"
                        </p>
                    </div>
                    <Button
                        v-if="displayTranscript()"
                        variant="primary"
                        full-width
                        @click="finishListening"
                    >
                        Done
                    </Button>
                    <p class="text-subtle text-xs mt-3">Tap outside to cancel</p>
                </div>

                <!-- PROCESSING STATE -->
                <div v-else-if="state === 'processing'" class="relative w-full max-w-sm bg-surface rounded-2xl p-8 text-center">
                    <div class="relative w-[88px] h-[88px] mx-auto mb-3">
                        <div class="w-[72px] h-[72px] rounded-full absolute top-2 left-2 overflow-hidden shadow-[0_0_0_3px_rgb(var(--color-primary)/0.2)] z-[2] avatar-bob">
                            <img src="/images/Avatar.png" alt="Budget Guy" class="w-full h-full object-cover" />
                        </div>
                    </div>
                    <div class="inline-flex gap-1.5 bg-surface-inset rounded-2xl px-4 py-2.5 mb-3">
                        <div class="thinking-dot"></div>
                        <div class="thinking-dot"></div>
                        <div class="thinking-dot"></div>
                    </div>
                    <p class="text-body font-semibold mb-1">Budget Guy is on it...</p>
                    <p v-if="transcript" class="text-muted text-sm italic">
                        "{{ transcript }}"
                    </p>
                </div>

                <!-- SUCCESS STATE -->
                <div v-else-if="state === 'success'" class="relative w-full max-w-sm bg-surface rounded-2xl p-8">
                    <div class="text-center mb-4">
                        <div class="relative w-[88px] h-[88px] mx-auto mb-3">
                            <div class="absolute inset-0 border-2 border-primary/30 rounded-full shadow-[0_0_20px_rgb(var(--color-primary)/0.15)]"></div>
                            <div class="w-[72px] h-[72px] rounded-full absolute top-2 left-2 overflow-hidden shadow-[0_0_0_3px_rgb(var(--color-primary)/0.5)] z-[2]">
                                <img src="/images/Avatar.png" alt="Budget Guy" class="w-full h-full object-cover" />
                            </div>
                            <div class="absolute bottom-1 right-1 w-6 h-6 bg-success rounded-full flex items-center justify-center z-[3] border-2 border-surface">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <p class="text-success font-semibold mb-1">Done!</p>
                        <p class="text-muted text-sm">
                            Budget Guy created {{ createdGroups.length }} group{{ createdGroups.length !== 1 ? 's' : '' }},
                            {{ totalCategories() }} categor{{ totalCategories() !== 1 ? 'ies' : 'y' }}
                        </p>
                    </div>
                    <div class="space-y-3">
                        <div v-for="group in createdGroups" :key="group.id">
                            <p class="text-xs font-semibold text-warning uppercase tracking-wide mb-1">
                                {{ group.name }}
                                <span v-if="!group.is_new" class="text-subtle font-normal normal-case">(existing)</span>
                            </p>
                            <div class="space-y-0.5">
                                <div
                                    v-for="cat in group.categories"
                                    :key="cat.id"
                                    class="flex items-center justify-between text-sm"
                                >
                                    <span class="text-body">
                                        {{ cat.icon || '📁' }} {{ cat.name }}
                                    </span>
                                    <span v-if="cat.default_amount" class="text-muted font-mono text-xs">
                                        {{ formatCurrency(cat.default_amount) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ERROR STATE -->
                <div v-else-if="state === 'error'" class="relative w-full max-w-sm bg-surface rounded-2xl p-8 text-center">
                    <div class="relative w-[88px] h-[88px] mx-auto mb-3">
                        <div class="absolute inset-0 border-2 border-danger/30 rounded-full"></div>
                        <div class="w-[72px] h-[72px] rounded-full absolute top-2 left-2 overflow-hidden shadow-[0_0_0_3px_rgb(var(--color-danger)/0.3)] z-[2]">
                            <img src="/images/Avatar.png" alt="Budget Guy" class="w-full h-full object-cover" />
                        </div>
                        <div class="absolute bottom-1 right-1 w-6 h-6 bg-danger rounded-full flex items-center justify-center z-[3] border-2 border-surface text-white text-xs font-bold">?</div>
                    </div>
                    <p class="text-body font-semibold mb-2">
                        {{ speechError === 'not-allowed' ? 'Microphone access denied' : isServiceError() ? 'Something went wrong' : "Budget Guy didn't catch that" }}
                    </p>
                    <p class="text-muted text-sm mb-5">
                        {{ speechError === 'not-allowed'
                            ? 'Allow microphone access in your browser settings.'
                            : errorMessage
                        }}
                    </p>
                    <div v-if="speechError !== 'not-allowed'" class="flex gap-3">
                        <Button variant="muted" class="flex-1" @click="handleClose">Cancel</Button>
                        <Button variant="primary" class="flex-1" @click="tryAgain">Try Again</Button>
                    </div>
                    <Button v-else variant="muted" full-width @click="handleClose">Close</Button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.avatar-pulse-ring {
    position: absolute;
    inset: 0;
    border: 2px solid rgb(var(--color-primary));
    border-radius: 50%;
    animation: avatar-pulse 2s ease-out infinite;
}

.avatar-pulse-ring-2 { animation-delay: 0.7s; }
.avatar-pulse-ring-3 { animation-delay: 1.4s; }

@keyframes avatar-pulse {
    0% { transform: scale(1); opacity: 0.5; }
    100% { transform: scale(1.7); opacity: 0; }
}

.avatar-mic-indicator {
    position: absolute;
    bottom: 4px;
    right: 4px;
    width: 24px;
    height: 24px;
    background: rgb(var(--color-danger));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 3;
    border: 2px solid rgb(var(--color-surface));
    animation: mic-glow 1s ease-in-out infinite alternate;
}

@keyframes mic-glow {
    0% { box-shadow: 0 0 0 0 rgb(var(--color-danger) / 0.4); }
    100% { box-shadow: 0 0 8px 3px rgb(var(--color-danger) / 0.3); }
}

.thinking-dot {
    width: 8px;
    height: 8px;
    background: rgb(var(--color-primary));
    border-radius: 50%;
    animation: thinking-bounce 1.4s ease-in-out infinite;
}

.thinking-dot:nth-child(2) { animation-delay: 0.2s; }
.thinking-dot:nth-child(3) { animation-delay: 0.4s; }

@keyframes thinking-bounce {
    0%, 80%, 100% { transform: scale(0.6); opacity: 0.4; }
    40% { transform: scale(1); opacity: 1; }
}

.avatar-bob {
    animation: subtle-bob 2s ease-in-out infinite;
}

@keyframes subtle-bob {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-3px); }
}
</style>
