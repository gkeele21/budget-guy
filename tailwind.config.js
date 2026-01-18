import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

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
                sans: ['IBM Plex Sans', ...defaultTheme.fontFamily.sans],
                mono: ['IBM Plex Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                // ===========================================
                // SEMANTIC COLORS
                // ===========================================

                // Primary brand color
                'primary': {
                    DEFAULT: '#76cd26',
                    light: '#8bd93e',
                    bg: '#edfce0',
                    hover: '#68b821',
                },

                // Secondary (neutral actions)
                'secondary': {
                    DEFAULT: '#6b7280',
                    hover: '#4b5563',
                },

                // Feedback colors
                'danger': {
                    DEFAULT: '#c0392b',
                    hover: '#a93226',
                },
                'success': {
                    DEFAULT: '#76cd26',
                    hover: '#68b821',
                },
                'warning': {
                    DEFAULT: '#f59e0b',
                    hover: '#d97706',
                },
                'info': {
                    DEFAULT: '#3b82f6',
                    hover: '#2563eb',
                },

                // Transaction type colors
                'expense': {
                    DEFAULT: '#c0392b',
                    hover: '#a93226',
                },
                'income': {
                    DEFAULT: '#76cd26',
                    hover: '#68b821',
                },
                'transfer': {
                    DEFAULT: '#3b82f6',
                    hover: '#2563eb',
                },

                // Surfaces
                'surface': {
                    DEFAULT: '#ffffff',
                    secondary: '#f5f5f5',
                },

                // Borders
                'border': {
                    DEFAULT: '#e5e7eb',
                    dark: '#d1d5db',
                },

                // Text
                'body': '#333333',
                'subtle': '#888888',
                'inverse': '#ffffff',
            },
            borderRadius: {
                'card': '12px',
            },
        },
    },

    plugins: [forms],
};
