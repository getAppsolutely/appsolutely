import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                'resources/page-builder/assets/main.ts',
                'resources/page-builder/assets/page-builder.css'
            ],
            refresh: true,
            buildDirectory: 'page-builder',
        }),
    ],
    build: {
        outDir: 'public/build/page-builder',
        assetsDir: 'assets',
        manifest: true,
        rollupOptions: {
            output: {
                manualChunks: undefined,
            },
        },
    },
    server: {
        host: '0.0.0.0',
        port: 5174, // Different port from main app
        strictPort: true,
        hmr: {
            host: 'localhost',
            protocol: 'ws',
            clientPort: 5174,
        },
    },
});
