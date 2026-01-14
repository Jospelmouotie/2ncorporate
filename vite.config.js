import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';
// import VueDevTools from 'vite-plugin-vue-devtools'; // Optionnel en prod
import path from 'path'; // Ajoute cet import pour gérer les chemins

export default defineConfig({
    plugins: [
        // VueDevTools(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            // Utilise path.resolve pour éviter les erreurs de chemin sur Render/Docker
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
});
