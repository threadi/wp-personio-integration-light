import { test, expect } from './fixtures';
import { mockPersonio, personioUrl, php, resetSetup } from './helpers';

/**
 * The setup a new installation starts with: enter the Personio URL, import, done.
 */
test.describe('Setup', () => {
    test.beforeEach(async ({ cli }) => {
        await resetSetup(cli);
        await mockPersonio(cli);
    });

    test('a new installation shows the setup instead of the positions', async ({ page, cli }) => {
        await page.goto(`${cli.serverUrl}/wp-admin/admin.php?page=personioPositions`);
        await expect(page.getByRole('heading', { level: 1 })).toContainText('Personio Integration Light Setup');
        await expect(page.getByRole('textbox', { name: 'Personio URL' })).toBeVisible({ timeout: 30_000 });

        // the list of positions is only available after the setup.
        await page.goto(`${cli.serverUrl}/wp-admin/edit.php?post_type=personioposition`);
        await expect(page.locator('body')).toContainText('Sorry, you are not allowed');
    });

    test('the dashboard asks to run the setup', async ({ page, cli }) => {
        await page.goto(`${cli.serverUrl}/wp-admin/`);
        const start = page.getByRole('link', { name: 'Start setup' });
        await expect(start).toBeVisible();
        await expect(start).toHaveAttribute('href', /page=personioPositions/);
    });

    test('a URL which is not from Personio is rejected', async ({ page, cli }) => {
        await page.goto(`${cli.serverUrl}/wp-admin/admin.php?page=personioPositions`);
        const url = page.getByRole('textbox', { name: 'Personio URL' });
        await expect(url).toBeVisible({ timeout: 30_000 });

        await url.fill('https://example.com');
        await expect(page.getByText('is not a Personio-URL')).toBeVisible();

        // still the first step, nothing has been saved.
        await expect(url).toBeVisible();
        expect(await php(cli, `echo get_option( 'personioIntegrationUrl' );`)).toBe('');
    });

    test('the setup imports the positions and starts the intro', async ({ page, cli }) => {
        test.slow();

        await page.goto(`${cli.serverUrl}/wp-admin/admin.php?page=personioPositions`);
        const url = page.getByRole('textbox', { name: 'Personio URL' });
        await expect(url).toBeVisible({ timeout: 30_000 });
        await url.fill(personioUrl);

        // wait for the check of the URL, which runs while typing.
        const validation = page.waitForResponse((response) => response.url().includes('/validate-field'));
        await url.blur();
        await validation;

        await page.getByRole('button', { name: 'Continue' }).click();

        // the second step imports the positions and then offers to finish the setup.
        const completed = page.getByRole('button', { name: 'Completed' });
        await expect(completed).toBeEnabled({ timeout: 60_000 });
        await completed.click();

        // the setup forwards to the list of positions, where the intro starts.
        await page.waitForURL(/edit\.php\?post_type=personioposition/, { timeout: 30_000 });
        await expect(page.getByRole('dialog')).toContainText('Intro');
        await expect(page.locator('.displaying-num').first()).toHaveText('11 items');

        expect(await php(cli, `echo get_option( 'personioIntegrationUrl' );`)).toBe(personioUrl);
    });
});
