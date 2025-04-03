import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path'; // Import path module

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css', // Ensure this points to the file importing Bootstrap Sass
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        // Remove Tailwind plugin: tailwindcss(),
    ],
    // Add resolve alias for bootstrap
    resolve: {
        alias: {
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
        }
    },
});
