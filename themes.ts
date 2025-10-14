import { exec } from 'child_process';
import { readdirSync, statSync, existsSync, rmSync } from 'fs';
import path from 'path';
import { promisify } from 'util';
import detect from 'detect-port';

const execAsync = promisify(exec);
const THEMES_DIR = path.resolve('themes');
const BUILD_DIR = path.resolve('public/build/themes');
const DEFAULT_DEV_PORT = 5173;

const args = process.argv.slice(2);
const command = args[0]; // 'build' or 'dev'
const specifiedThemes = args.slice(1);

if (!['build', 'dev'].includes(command)) {
    console.error('❌ Usage: tsx themes.ts <build|dev> [theme1 theme2 ...]');
    process.exit(1);
}

// Available themes
const allThemes = readdirSync(THEMES_DIR).filter((dir) => {
    const configPath = path.join(THEMES_DIR, dir, 'vite.config.ts');
    return statSync(path.join(THEMES_DIR, dir)).isDirectory() && existsSync(configPath);
});

const themes = specifiedThemes.length > 0 ? specifiedThemes : allThemes;

// 🧹 Clean
function cleanBuildDir() {
    if (existsSync(BUILD_DIR)) {
        console.log('🧹 Cleaning build directory...');
        rmSync(BUILD_DIR, { recursive: true, force: true });
    }
}

// 💀 Kill port when possible
async function killPort(port: number) {
    try {
        const { stdout } = await execAsync(`lsof -t -i:${port}`);
        const pids = stdout.split('\n').filter(Boolean);
        for (const pid of pids) {
            console.log(`💀 Killing process ${pid} on port ${port}`);
            await execAsync(`kill -9 ${pid}`);
        }
    } catch {
    }
}

async function getPortFromConfig(configPath: string): Promise<number | null> {
    try {
        const configModule = await import(configPath);
        const config = configModule.default || configModule;

        if (config.server && typeof config.server.port === 'number') {
            return config.server.port;
        }

        console.warn(`⚠️ No port found in ${configPath}.`);
        return null;
    } catch (e: any) {
        console.warn(`❌ Failed to import config from ${configPath}:`, e.message);
        return null;
    }
}

// 🚀 Build
async function runBuild() {
    cleanBuildDir();
    for (const theme of themes) {
        const configPath = `themes/${theme}/vite.config.ts`;
        console.log(`🚀 Building theme: ${theme}`);
        try {
            await execAsync(`vite build --config ${configPath}`);
        } catch (err) {
            console.error(`❌ Failed to build theme "${theme}"`, (err as any).message);
        }
    }
}

// ⚡ Dev
async function runDev() {
    const devCommands: string[] = [];

    for (const theme of themes) {
        const configPath = path.resolve(`themes/${theme}/vite.config.ts`);
        const configImportPath = `file://${configPath}`;
        let desiredPort = await getPortFromConfig(configImportPath);

        if (!desiredPort) {
            desiredPort = DEFAULT_DEV_PORT + Math.floor(Math.random() * 1000);
            console.warn(`⚠️ No port in config for "${theme}", fallback to ${desiredPort}`);
        }

        let port = await detect(desiredPort);
        if (port !== desiredPort) {
            console.warn(`⛔ Port ${desiredPort} is in use, trying to kill...`);
            await killPort(desiredPort);
            port = await detect(desiredPort);
        }

        if (port !== desiredPort) {
            console.warn(`⚠️ Still cannot use ${desiredPort}. "${theme}" will run on ${port}`);
        }

        console.log(`🌍 ${theme} running at http://localhost:${port}`);
        devCommands.push(`vite --config themes/${theme}/vite.config.ts --port ${port}`);
    }

    const cmd = `concurrently -k -n "${themes.join(',')}" -c "cyan,magenta,green,yellow" ${devCommands
        .map((c) => `"${c}"`)
        .join(' ')}`;

    await execAsync(cmd, { shell: '/bin/sh' });
}

// 🏁 Run
(async () => {
    if (command === 'build') {
        await runBuild();
    } else {
        await runDev();
    }
})();
