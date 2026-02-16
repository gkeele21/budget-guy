import { ref } from 'vue';

// Module-level shared state (singleton pattern)
const isSupported = ref(false);
const isListening = ref(false);
const transcript = ref('');
const interimTranscript = ref('');
const error = ref(null);

let recognition = null;
let initialized = false;

// Callbacks set by the overlay
let onEndCallback = null;

function initialize() {
    if (initialized || typeof window === 'undefined') return;
    initialized = true;

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    isSupported.value = !!SpeechRecognition;

    if (!isSupported.value) return;

    recognition = new SpeechRecognition();
    recognition.continuous = true;
    recognition.interimResults = true;
    recognition.lang = 'en-US';

    recognition.onstart = () => {
        isListening.value = true;
        error.value = null;
    };

    recognition.onresult = (event) => {
        let interim = '';
        let final = '';

        for (let i = 0; i < event.results.length; i++) {
            const result = event.results[i];
            if (result.isFinal) {
                final += result[0].transcript;
            } else {
                interim += result[0].transcript;
            }
        }

        if (final) {
            transcript.value = final;
        }
        interimTranscript.value = interim;
    };

    recognition.onend = () => {
        isListening.value = false;

        // Notify with whatever transcript we have (final + any trailing interim)
        const fullTranscript = transcript.value
            ? (interimTranscript.value ? `${transcript.value} ${interimTranscript.value}` : transcript.value)
            : interimTranscript.value;

        interimTranscript.value = '';

        if (fullTranscript && onEndCallback) {
            onEndCallback(fullTranscript);
        }
    };

    recognition.onerror = (event) => {
        isListening.value = false;

        // 'no-speech' and 'aborted' are not real errors
        if (event.error === 'no-speech') {
            error.value = 'no-speech';
        } else if (event.error === 'aborted') {
            // User cancelled, not an error
            error.value = null;
        } else if (event.error === 'not-allowed') {
            error.value = 'not-allowed';
        } else {
            error.value = event.error;
        }
    };
}

export function useSpeechRecognition() {
    initialize();

    function start() {
        if (!recognition || isListening.value) return;

        transcript.value = '';
        interimTranscript.value = '';
        error.value = null;

        try {
            recognition.start();
        } catch (e) {
            // Already started — ignore
        }
    }

    function stop() {
        if (!recognition || !isListening.value) return;

        try {
            recognition.stop();
        } catch (e) {
            // Already stopped — ignore
        }
    }

    function abort() {
        if (!recognition) return;

        try {
            recognition.abort();
        } catch (e) {
            // Ignore
        }
        isListening.value = false;
        transcript.value = '';
        interimTranscript.value = '';
    }

    function reset() {
        transcript.value = '';
        interimTranscript.value = '';
        error.value = null;
    }

    function onEnd(callback) {
        onEndCallback = callback;
    }

    return {
        isSupported,
        isListening,
        transcript,
        interimTranscript,
        error,
        start,
        stop,
        abort,
        reset,
        onEnd,
    };
}

export default useSpeechRecognition;
