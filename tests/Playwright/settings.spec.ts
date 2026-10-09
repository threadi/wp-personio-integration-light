import { test, expect } from './fixtures';
import { completeSetup, personioUrl, php } from './helpers';

const settingsPath = '/wp-admin/edit.php?post_type=personioposition&page=personioPositions';

test.describe('Settings', () => {
    test.beforeEach(async ({ cli }) => {
        await completeSetup(cli);
        await php(cli, `update_option( 'personioIntegrationMainLanguage', 'en' );`);
    });

    test('Loading settings page without error', async ({ page, cli }) => {
        await page.goto(`${cli.serverUrl}${settingsPath}`);
        await expect(page.getByRole('heading', { level: 1 })).toContainText('Personio Integration Light Settings');
        await expect(page.getByRole('textbox', { name: 'Personio URL' })).toHaveValue(personioUrl);
    });

    for (const tab of ['Templates', 'Import', 'Additional settings']) {
        test(`the tab "${tab}" can be opened`, async ({ page, cli }) => {
            await page.goto(`${cli.serverUrl}${settingsPath}`);
            await page.getByRole('tab', { name: tab, exact: true }).click();
            await expect(page.getByRole('tab', { name: tab, exact: true })).toHaveAttribute('aria-selected', 'true');
            await expect(page.getByRole('tabpanel', { name: tab })).toBeVisible();
            await expect(page.getByRole('button', { name: 'Save', exact: true })).toBeVisible();
        });
    }

    test('a changed setting is saved', async ({ page, cli }) => {
        await page.goto(`${cli.serverUrl}${settingsPath}`);
        await page.getByRole('radio', { name: 'German' }).check();
        await page.getByRole('button', { name: 'Save', exact: true }).click();
        await expect(page.getByTestId('snackbar')).toContainText('Settings saved');

        expect(await php(cli, `echo get_option( 'personioIntegrationMainLanguage' );`)).toBe('de');

        // and it is still selected after loading the page again.
        await page.reload();
        await expect(page.getByRole('radio', { name: 'German' })).toBeChecked();
    });

    test('a URL which is not from Personio is not saved', async ({ page, cli }) => {
        await page.goto(`${cli.serverUrl}${settingsPath}`);
        const url = page.getByRole('textbox', { name: 'Personio URL' });
        await url.fill('https://example.com');
        await page.getByRole('button', { name: 'Save', exact: true }).click();
        await expect(page.getByTestId('snackbar')).toBeVisible();

        // the valid URL from before is kept.
        expect(await php(cli, `echo get_option( 'personioIntegrationUrl' );`)).toBe(personioUrl);
        await page.reload();
        await expect(url).toHaveValue(personioUrl);
    });
});
