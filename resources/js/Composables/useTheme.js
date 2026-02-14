import { ref, watch } from 'vue';

const STORAGE_KEY = 'budgetguy-theme';
const STORAGE_KEY_BG = 'budgetguy-bg-mode';
const STORAGE_KEY_ICONS = 'budgetguy-show-category-icons';
const DEFAULT_THEME = 'green';
const DEFAULT_BG_MODE = 'slate';
const VALID_THEMES = ['green', 'blue', 'orange'];
const VALID_BG_MODES = ['slate', 'cream'];

// Shared reactive state (singleton pattern)
const currentTheme = ref(DEFAULT_THEME);
const currentBgMode = ref(DEFAULT_BG_MODE);
const showCategoryIcons = ref(true);
const isInitialized = ref(false);

function applyTheme(theme) {
    if (typeof document === 'undefined') return;
    const body = document.body;
    VALID_THEMES.forEach((t) => body.classList.remove(`accent-${t}`));
    body.classList.add(`accent-${theme}`);
}

function applyBgMode(mode) {
    if (typeof document === 'undefined') return;
    const body = document.body;
    VALID_BG_MODES.forEach((m) => body.classList.remove(`bg-mode-${m}`));
    body.classList.add(`bg-mode-${mode}`);
}

function loadTheme() {
    if (typeof localStorage === 'undefined') return DEFAULT_THEME;
    const stored = localStorage.getItem(STORAGE_KEY);
    return stored && VALID_THEMES.includes(stored) ? stored : DEFAULT_THEME;
}

function saveTheme(theme) {
    if (typeof localStorage === 'undefined') return;
    localStorage.setItem(STORAGE_KEY, theme);
}

function loadBgMode() {
    if (typeof localStorage === 'undefined') return DEFAULT_BG_MODE;
    const stored = localStorage.getItem(STORAGE_KEY_BG);
    return stored && VALID_BG_MODES.includes(stored) ? stored : DEFAULT_BG_MODE;
}

function saveBgMode(mode) {
    if (typeof localStorage === 'undefined') return;
    localStorage.setItem(STORAGE_KEY_BG, mode);
}

function loadShowCategoryIcons() {
    if (typeof localStorage === 'undefined') return true;
    const stored = localStorage.getItem(STORAGE_KEY_ICONS);
    return stored === null ? true : stored === 'true';
}

function saveShowCategoryIcons(value) {
    if (typeof localStorage === 'undefined') return;
    localStorage.setItem(STORAGE_KEY_ICONS, String(value));
}

function initializeTheme() {
    if (isInitialized.value) return;

    const storedTheme = loadTheme();
    currentTheme.value = storedTheme;
    applyTheme(storedTheme);

    const storedBgMode = loadBgMode();
    currentBgMode.value = storedBgMode;
    applyBgMode(storedBgMode);

    showCategoryIcons.value = loadShowCategoryIcons();

    isInitialized.value = true;
}

export function useTheme() {
    initializeTheme();

    watch(currentTheme, (newTheme) => {
        if (VALID_THEMES.includes(newTheme)) {
            applyTheme(newTheme);
            saveTheme(newTheme);
        }
    });

    watch(currentBgMode, (newMode) => {
        if (VALID_BG_MODES.includes(newMode)) {
            applyBgMode(newMode);
            saveBgMode(newMode);
        }
    });

    function setTheme(theme) {
        if (VALID_THEMES.includes(theme)) {
            currentTheme.value = theme;
        }
    }

    function cycleTheme() {
        const currentIndex = VALID_THEMES.indexOf(currentTheme.value);
        currentTheme.value = VALID_THEMES[(currentIndex + 1) % VALID_THEMES.length];
    }

    function setBgMode(mode) {
        if (VALID_BG_MODES.includes(mode)) {
            currentBgMode.value = mode;
        }
    }

    function setShowCategoryIcons(value) {
        showCategoryIcons.value = value;
        saveShowCategoryIcons(value);
    }

    return {
        theme: currentTheme,
        setTheme,
        themes: VALID_THEMES,
        cycleTheme,
        bgMode: currentBgMode,
        setBgMode,
        bgModes: VALID_BG_MODES,
        showCategoryIcons,
        setShowCategoryIcons,
    };
}

export default useTheme;
