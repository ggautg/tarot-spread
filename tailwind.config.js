import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/**
 * Tailwind solo aporta utilidades de layout y espaciado.
 * El color y la tipografía de marca viven en las variables --t-* de app.css,
 * para que cada corriente visual (tema) se aplique sin duplicar clases.
 *
 * @type {import('tailwindcss').Config}
 */
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
                // La familia real la decide el tema activo.
                sans: ['var(--t-font-body)', ...defaultTheme.fontFamily.serif],
                display: ['var(--t-font-display)', ...defaultTheme.fontFamily.serif],
            },
        },
    },

    plugins: [forms],
};
