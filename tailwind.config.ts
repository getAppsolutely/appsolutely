import type { Config } from 'tailwindcss';
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/page-builder/assets/**/*.vue',
        './resources/page-builder/**/*.blade.php',
        './resources/page-builder/assets/**/*.ts',
        './resources/page-builder/assets/**/*.scss',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#6366f1',
                secondary: '#8b5cf6',
                dark: '#1e293b',
                editor: '#f8fafc',
            },
            height: {
                '11/12': '91.666667%',
            },
        },
    },

    plugins: [forms, typography],
} satisfies Config;

