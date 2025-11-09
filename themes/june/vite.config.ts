import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';
import { fileURLToPath } from 'url';
import fs from 'fs';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

export default defineConfig({
    base: `/build/themes/june`,
    plugins: [
        laravel({
            input: ['themes/june/sass/app.scss', 'themes/june/js/app.ts'],
            buildDirectory: 'build/themes/june',
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
            '@june': path.resolve(__dirname, 'resources/themes/june'),
            '~bootstrap': path.resolve('node_modules/bootstrap'),
        },
    },
    server: {
        host: '0.0.0.0', // Docker-safe
        port: 5177,
        strictPort: true,
        hmr: {
            host: 'localhost', // or your Docker host domain
            protocol: 'wss', // ws if http
            clientPort: 5177,
        },
        cors: {
            origin: true,
            methods: ['GET', 'HEAD', 'PUT', 'PATCH', 'POST', 'DELETE'],
            credentials: true,
        },
        https: {
            key: fs.readFileSync('storage/ssl/appsolutely.key.pem'), // comment if http
            cert: fs.readFileSync('storage/ssl/appsolutely.pem'), // comment if http
        },
    },
    css: {
        preprocessorOptions: {
            scss: {
                silenceDeprecations: ['import', 'mixed-decls', 'color-functions', 'global-builtin'],
            },
        },
    },
    build: {
        assetsInlineLimit: 0,
        rollupOptions: {
            output: {
                assetFileNames: (assetInfo) => {
                    if (!assetInfo.name) return 'assets/[name].[hash][extname]';
                    const ext = assetInfo.name.split('.').pop();
                    if (ext && ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'].includes(ext)) {
                        return 'images/[name].[hash][extname]';
                    }
                    if (ext && ['woff2', 'woff', 'ttf'].includes(ext)) {
                        return 'fonts/[name].[hash][extname]';
                    }
                    return 'assets/[name].[hash][extname]';
                },
            },
        },
    },
});
