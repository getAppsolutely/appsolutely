import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'themes/default/css/app.css',
                'themes/default/js/app.js'
            ],
            refresh: true,
        }),
        vue()
    ],
    server: {
        host: '0.0.0.0', // Docker-safe
        port: 5177,
        strictPort: true,
        hmr: {
            host: 'localhost', // or your Docker host domain
            protocol: 'ws',
            clientPort: 5177,
        },
        cors: {
            origin: true,
            methods: ['GET', 'HEAD', 'PUT', 'PATCH', 'POST', 'DELETE'],
            credentials: true
        }
    },
});
