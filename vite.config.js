import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'themes/default/css/app.css',
                'themes/default/js/app.js'
            ],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0', // Docker-safe
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost', // or your Docker host domain
            protocol: 'ws',
            clientPort: 5173,
        },
    },
});
