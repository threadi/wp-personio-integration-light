import { defineConfig, devices } from '@playwright/test';

/**
 * Playwright configuration for the end-to-end tests of Personio Integration Light.
 *
 * Every test worker starts its own WordPress Playground with this plugin mounted
 * (see tests/Playwright/fixtures.ts). Starting Playground takes a while and uses a
 * lot of memory, therefore the tests run in one worker, one after another.
 *
 * Run them with: npx playwright test
 */
// @ts-ignore
const isCI = !! process.env.CI;

export default defineConfig( {
	testDir: './tests/Playwright',
	testMatch: '**/*.spec.ts',

	// one Playground for all tests: they share the website, see helpers.ts.
	fullyParallel: false,
	workers: 1,

	// test.only must never end up in the repository.
	forbidOnly: isCI,

	// a failed test gets one more try in CI. Playwright starts a fresh website for it.
	retries: isCI ? 1 : 0,

	// a single test. Starting Playground is not part of it, see "cli" in fixtures.ts.
	// Playground is a lot slower than a real WordPress, especially on a shared server,
	// and some tests load several pages of the backend.
	timeout: 120_000,
	expect: { timeout: 20_000 },

	reporter: isCI
		? [ [ 'github' ], [ 'list' ], [ 'html', { open: 'never', outputFolder: 'playwright-report' } ] ]
		: [ [ 'list' ] ],

	outputDir: 'test-results',

	use: {
		trace: 'retain-on-failure',
		screenshot: 'only-on-failure',
		locale: 'en-US',
		timezoneId: 'Europe/Berlin',
		navigationTimeout: 60_000,
		actionTimeout: 30_000,
	},

	projects: [
		{
			name: 'chromium',
			use: {
				...devices[ 'Desktop Chrome' ],
				launchOptions: {
					// optional: use an already installed Chromium instead of the one of "npx playwright install".
					// @ts-ignore
					executablePath: process.env.PLAYWRIGHT_CHROMIUM_EXECUTABLE || undefined,
				},
			},
		},
	],
} );
