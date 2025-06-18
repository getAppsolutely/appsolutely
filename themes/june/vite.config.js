import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import path from "path";




export default defineConfig({
    plugins: [
        laravel({
            input: [
                "themes/june/sass/app.scss",
                "themes/june/js/app.js"
            ],
            buildDirectory: "build/theme/june",
        }),


        {
            name: "blade",
            handleHotUpdate({ file, server }) {
                if (file.endsWith(".blade.php")) {
                    server.ws.send({
                        type: "full-reload",
                        path: "*",
                    });
                }
            },
        },
    ],
    resolve: {
        alias: {
            '@': '/themes/june/js',
            '~bootstrap': path.resolve('node_modules/bootstrap'),
        }
    },
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
