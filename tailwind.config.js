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
                sans: ['IBM Plex Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // ===========================================
                // PRIMARY (Brand Green)
                // ===========================================
                'primary': {
                    DEFAULT: '#7ED957',
                    light: '#8bd93e',
                    bg: '#edfce0',
                    hover: '#6bc94a',
                },

                // ===========================================
                // SECONDARY (Brand Blue)
                // ===========================================
                'secondary': {
                    DEFAULT: '#2196F3',
                    hover: '#1976D2',
                },

                // ===========================================
                // FINANCIAL SEMANTICS
                // ===========================================
                'income': {
                    DEFAULT: '#2E7D32',
                    hover: '#256b29',
                },
                'expense': {
                    DEFAULT: '#E5533D',
                    hover: '#d04a35',
                },
                'transfer': {
                    DEFAULT: '#2196F3',
                    hover: '#1976D2',
                },

                // ===========================================
                // FEEDBACK (aliases to financial colors)
                // ===========================================
                'success': {
                    DEFAULT: '#2E7D32',
                    hover: '#256b29',
                },
                'danger': {
                    DEFAULT: '#E5533D',
                    hover: '#d04a35',
                },

                // ===========================================
                // SURFACES
                // ===========================================
                'surface': {
                    DEFAULT: '#ffffff',
                    secondary: '#f5f5f5',
                    tertiary: '#f0f0f0',
                },

                // ===========================================
                // BORDERS
                // ===========================================
                'border': {
                    DEFAULT: '#e5e7eb',
                    dark: '#d1d5db',
                },

                // ===========================================
                // TEXT
                // ===========================================
                'body': '#1F2933',
                'subtle': '#888888',
                'inverse': '#ffffff',
            },
            borderRadius: {
                'card': '12px',
            },
        },
    },

    plugins: [],
};
