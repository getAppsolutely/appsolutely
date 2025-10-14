import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    base: '/build/page-builder',
    plugins: [
        laravel({
            input: [
                'resources/page-builder/assets/scss/app.scss',
                'resources/page-builder/assets/ts/app.ts'
            ],
            buildDirectory: 'build/page-builder',
        }),
        {
            name: 'blade',
            handleHotUpdate({ file, server }) {
                if (file.endsWith('.blade.php')) {
                    server.ws.send({
                        type: 'full-reload',
                        path: '*',
                    });
                }
            },
        },
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/page-builder/assets'),
            '@components': path.resolve(__dirname, 'resources/page-builder/assets/ts/components'),
            '@services': path.resolve(__dirname, 'resources/page-builder/assets/ts/services'),
            '@styles': path.resolve(__dirname, 'resources/page-builder/assets/scss'),
        }
    },
    server: {
        host: '0.0.0.0', // Docker-safe
        port: 5178,
        strictPort: true,
        hmr: {
            host: 'localhost',
            protocol: 'ws',
            clientPort: 5178,
        },
        cors: {
            origin: true,
            methods: ['GET', 'HEAD', 'PUT', 'PATCH', 'POST', 'DELETE'],
            credentials: true
        }
    },
    css: {
        preprocessorOptions: {
            scss: {
                silenceDeprecations: [
                    'import',
                    'mixed-decls',
                    'color-functions',
                    'global-builtin',
                ],
            },
        },
    },
    build: {
        assetsInlineLimit: 0,
        rollupOptions: {
            output: {
                manualChunks: {
                    'grapesjs': ['grapesjs'],
                    'vendor': ['axios', 'lodash']
                }
            }
        }
    },
    optimizeDeps: {
        include: ['grapesjs', 'axios', 'lodash']
    }
});
