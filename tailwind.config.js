import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // ===========================================
                // THEME-AWARE PRIMARY (adapts via CSS vars)
                // ===========================================
                'primary': {
                    DEFAULT: 'rgb(var(--color-primary) / <alpha-value>)',
                    hover: 'rgb(var(--color-primary-hover) / <alpha-value>)',
                },

                // ===========================================
                // SEMANTIC COLORS
                // ===========================================
                'success': 'rgb(var(--color-success) / <alpha-value>)',
                'warning': 'rgb(var(--color-warning) / <alpha-value>)',
                'danger': 'rgb(var(--color-danger) / <alpha-value>)',
                'info': 'rgb(var(--color-info) / <alpha-value>)',

                // ===========================================
                // FINANCIAL SEMANTICS (mapped to semantic vars)
                // ===========================================
                'income': {
                    DEFAULT: 'rgb(var(--color-success) / <alpha-value>)',
                },
                'expense': {
                    DEFAULT: 'rgb(var(--color-danger) / <alpha-value>)',
                },
                'transfer': {
                    DEFAULT: 'rgb(var(--color-info) / <alpha-value>)',
                },

                // Backward compat: secondary → info
                'secondary': {
                    DEFAULT: 'rgb(var(--color-info) / <alpha-value>)',
                },

                // ===========================================
                // SURFACE HIERARCHY (5-layer dark surfaces)
                // ===========================================
                'bg': 'rgb(var(--color-bg) / <alpha-value>)',
                'surface': {
                    DEFAULT: 'rgb(var(--color-surface) / <alpha-value>)',
                    elevated: 'rgb(var(--color-surface-elevated) / <alpha-value>)',
                    overlay: 'rgb(var(--color-surface-overlay) / <alpha-value>)',
                    inset: 'rgb(var(--color-surface-inset) / <alpha-value>)',
                    header: 'rgb(var(--color-surface-header) / <alpha-value>)',
                    // Backward compat aliases
                    secondary: 'rgb(var(--color-surface-elevated) / <alpha-value>)',
                    tertiary: 'rgb(var(--color-surface-overlay) / <alpha-value>)',
                },

                // ===========================================
                // TEXT
                // ===========================================
                'body': 'rgb(var(--color-text) / <alpha-value>)',
                'muted': 'rgb(var(--color-text-muted) / <alpha-value>)',
                'subtle': 'rgb(var(--color-text-subtle) / <alpha-value>)',
                'inverse': 'rgb(var(--color-text-inverse) / <alpha-value>)',

                // ===========================================
                // BORDERS
                // ===========================================
                'border': {
                    DEFAULT: 'rgb(var(--color-border) / <alpha-value>)',
                    strong: 'rgb(var(--color-border-strong) / <alpha-value>)',
                    // Backward compat
                    dark: 'rgb(var(--color-border-strong) / <alpha-value>)',
                },

                // ===========================================
                // STATIC COLORS (don't change with theme)
                // ===========================================
                'white': '#ffffff',
                'black': '#171717',
            },
            borderRadius: {
                'card': '12px',
            },
        },
    },

    plugins: [],
};
