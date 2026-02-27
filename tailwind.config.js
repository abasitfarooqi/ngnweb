// File: tailwind.config.js
import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
import aspectRatio from '@tailwindcss/aspect-ratio';
import scrollbar from 'tailwind-scrollbar';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        // Flux Pro — scan component stubs so Tailwind doesn't purge Flux classes
        './vendor/livewire/flux/stubs/**/*.blade.php',
        './vendor/livewire/flux-pro/stubs/**/*.blade.php',
    ],
    theme: {
        extend: {
            screens: {
                // Adding a custom breakpoint at 599px
                'custom': '599px',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            backgroundImage: {
                'hero-pattern': "url('/assets/images/temp/delivery-service-lg.png')",
            },
            colors: {
                primary: {
                    50: '#fff5f5',
                    100: '#ffe5e5',
                    200: '#ffbfbf',
                    300: '#ff9999',
                    400: '#ff7373',
                    500: '#ff4d4d',
                    600: '#e63939',
                    700: '#cc2f2f',
                    800: '#b32626',
                    900: '#991d1d',
                },
                secondary: {
                    50: '#f0fcff',
                    100: '#dff9ff',
                    200: '#b3f0ff',
                    300: '#80e4ff',
                    400: '#4dd8ff',
                    500: '#26ccff',
                    600: '#1ba9e6',
                    700: '#1486cc',
                    800: '#0f66b3',
                    900: '#0b4d99',
                },
                'ngn-primary': {
                    50: '#ffe5e5',   // Lightest shade
                    100: '#ffcccc',
                    200: '#ff9999',
                    300: '#ff6666',
                    400: '#ff3333',
                    500: '#C31924',   // Base color
                    600: '#a3101e',
                    700: '#821618',
                    800: '#610e12',
                    900: '#40060c',   // Darkest shade
                    1000: '#000000',
                },
                'bg-ngn-primary': {
                    50: '#ffe5e5',   // Lightest shade
                    100: '#ffcccc',
                    200: '#ff9999',
                    300: '#ff6666',
                    400: '#ff3333',
                    500: '#C31924',   // Base color
                    600: '#a3101e',
                    700: '#821618',
                    800: '#610e12',
                    900: '#40060c',   // Darkest shade
                    1000: '#000000',
                },
            },
        },
    },
    plugins: [
        forms,
        typography,
        aspectRatio,
        scrollbar,
        function({ addUtilities, theme }) {
            const newUtilities = {
                // Background NGN Primary
                '.bg-ngn-primary': {
                    backgroundColor: theme('colors.ngn-primary.500'),
                },
                // Hover Background NGN Primary
                '.hover\\:bg-ngn-primary:hover': {
                    backgroundColor: theme('colors.ngn-primary.500'),
                },
            };
            addUtilities(newUtilities, ['responsive', 'hover']);
        },
    ],
};