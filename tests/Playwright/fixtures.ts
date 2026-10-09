// @ts-ignore
import { test as base, type Page } from '@playwright/test';
// @ts-ignore
import { execSync } from 'node:child_process';
// @ts-ignore
import { copyFileSync, existsSync, mkdirSync, statSync } from 'node:fs';
// @ts-ignore
import path from 'node:path';
import { clearPhpErrors, getPhpErrors, type Cli } from './helpers';

/**
 * Fixtures for the Playwright tests.
 *
 * Each test worker starts one WordPress Playground with this plugin mounted from the
 * working copy and activated. "page" is the logged-in administrator, "visitor" is
 * somebody who is not logged in.
 *
 * The WordPress and PHP version can be set with environment variables:
 * - PLAYGROUND_WP:  "latest" (default), a version like "6.8" or the URL of a WordPress zip.
 * - PLAYGROUND_PHP: "8.3" (default) or any other PHP version Playground supports.
 * - PLAYGROUND_HOME: where Playground keeps the WordPress it downloads, see loadPlayground().
 */

// @ts-ignore
const pluginRoot = path.resolve(__dirname, '../..');
const pluginSlug = 'personio-integration-light';
const autoloadPath = path.join(pluginRoot, 'vendor', 'autoload.php');
const lockPath = path.join(pluginRoot, 'composer.lock');
const urlConstantsPath = path.join(pluginRoot, 'inc', 'constants_urls.php');

function needsComposerInstall(): boolean {
    if (!existsSync(autoloadPath)) return true;
    if (!existsSync(lockPath)) return false;
    return statSync(lockPath).mtimeMs > statSync(autoloadPath).mtimeMs;
}

/**
 * Make sure the working copy contains everything the plugin needs at runtime.
 */
function preparePlugin(): void {
    if (needsComposerInstall()) {
        console.log('Running composer install …');
        execSync('composer install --optimize-autoloader', {
            cwd: pluginRoot,
            stdio: 'inherit',
        });
    }

    // the plugin loads this file. The placeholders of the .dist file are enough for the tests.
    if (!existsSync(urlConstantsPath)) {
        copyFileSync(`${urlConstantsPath}.dist`, urlConstantsPath);
    }

    // the blocks are compiled with npm. Without them the plugin can not be tested.
    for (const file of ['blocks/list/build/index.asset.php', 'blocks/commands/commands.asset.php', 'css/blocks.css']) {
        if (!existsSync(path.join(pluginRoot, file))) {
            throw new Error(`${file} is missing. Run "npm run build && npm run global-styles" first.`);
        }
    }
}

/**
 * Load Playground's runCLI().
 *
 * Playground keeps the WordPress it downloads in "<home directory>/.wordpress-playground" and
 * determines that directory once, while its module is loaded. The home directory is not always
 * writable, e.g. for the user of a web server. Therefore the module is loaded here with HOME
 * pointing to node_modules/.cache/playground-home (or PLAYGROUND_HOME). HOME is restored right
 * after, as Playwright itself looks for its browsers in the real home directory.
 */
async function loadPlayground() {
    // @ts-ignore
    const playgroundHome = process.env.PLAYGROUND_HOME || path.join(pluginRoot, 'node_modules', '.cache', 'playground-home');
    mkdirSync(playgroundHome, { recursive: true });

    // @ts-ignore
    const env = process.env;
    const realHome = env.HOME;
    const realUserProfile = env.USERPROFILE;
    env.HOME = playgroundHome;
    env.USERPROFILE = playgroundHome;
    try {
        return (await import('@wp-playground/cli')).runCLI;
    } finally {
        if (realHome === undefined) delete env.HOME; else env.HOME = realHome;
        if (realUserProfile === undefined) delete env.USERPROFILE; else env.USERPROFILE = realUserProfile;
    }
}

export const test = base.extend<{ visitor: Page; phpErrors: void }, { cli: Cli }>({
    // a page of somebody who is not logged in. "page" is always the administrator:
    // Playground logs every browser in, unless this cookie says it already did.
    // @ts-ignore
    visitor: async ({ browser, cli }, use) => {
        const context = await browser.newContext();
        await context.addCookies([
            { name: 'playground_auto_login_already_happened', value: '1', url: cli.serverUrl },
        ]);

        await use(await context.newPage());

        await context.close();
    },

    // every test fails if the plugin caused a PHP error, warning, notice or deprecation meanwhile.
    // Tests which provoke one on purpose can call clearPhpErrors() before they end.
    phpErrors: [
        // @ts-ignore
        async ({ cli }, use) => {
            await clearPhpErrors(cli);
            await use();
            const errors = await getPhpErrors(cli);
            // @ts-ignore
            base.expect(errors, 'PHP messages caused by the plugin (wp-content/debug.log)').toEqual([]);
        },
        { auto: true },
    ],

    // worker-scoped: runs once per test worker, not once per test.
    cli: [
        // @ts-ignore
        async ({}, use) => {
            preparePlugin();
            const runCLI = await loadPlayground();

            const server = await runCLI({
                command: 'server',
                // the database of each run is new and empty, nothing is kept between runs.
                mount: [
                    {
                        hostPath: pluginRoot,
                        vfsPath: `/wordpress/wp-content/plugins/${pluginSlug}`,
                    },
                ],
                blueprint: {
                    preferredVersions: {
                        // @ts-ignore
                        php: process.env.PLAYGROUND_PHP || '8.3',
                        // @ts-ignore
                        wp: process.env.PLAYGROUND_WP || 'latest',
                    },
                    login: true,
                    steps: [
                        {
                            // collect PHP messages in wp-content/debug.log, see "phpErrors" above.
                            step: 'defineWpConfigConsts',
                            consts: { WP_DEBUG: true, WP_DEBUG_LOG: true, WP_DEBUG_DISPLAY: false },
                        },
                        {
                            // pretty permalinks, so the archive and the single positions have their slugs.
                            step: 'setSiteOptions',
                            options: { permalink_structure: '/%postname%/' },
                        },
                        {
                            step: 'activatePlugin',
                            pluginPath: `${pluginSlug}/${pluginSlug}.php`,
                        },
                    ],
                },
            });

            // the very first request to a new Playground server is answered with a redirect
            // which also removes the cookie the "visitor" above relies on. Get it out of the way.
            await fetch(`${server.serverUrl}/`, { redirect: 'manual' });

            await use(server as unknown as Cli);

            await server[Symbol.asyncDispose]();
        },
        // starting Playground (and downloading WordPress the first time) takes longer than a test.
        { scope: 'worker', timeout: 300_000 },
    ],
});

// @ts-ignore
export { expect } from '@playwright/test';
