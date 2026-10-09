import { test, expect } from './fixtures';
import { completeSetup, deletePositions, getPositions, importPositions } from './helpers';

const listPath = '/wp-admin/edit.php?post_type=personioposition';

/**
 * The list of positions in the backend.
 */
test.describe('Positions in the backend', () => {
    test('the list shows all imported positions', async ({ page, cli }) => {
        expect(await importPositions(cli)).toBe(11);

        await page.goto(`${cli.serverUrl}${listPath}`);
        await expect(page.locator('.displaying-num').first()).toHaveText('11 items');
        await expect(page.getByRole('link', { name: 'Go to Market Manager', exact: true })).toBeVisible();
        await expect(page.locator('#the-list')).toContainText('2140235');
    });

    test('positions can be searched', async ({ page, cli }) => {
        await importPositions(cli);

        await page.goto(`${cli.serverUrl}${listPath}`);
        await page.locator('#post-search-input').fill('Marketing');
        await page.getByRole('button', { name: 'Search Position' }).click();

        await expect(page.locator('.displaying-num').first()).toHaveText('2 items');
        await expect(page.locator('#the-list')).toContainText('SEA Marketing Managers');
        await expect(page.locator('#the-list')).toContainText('SEO Marketing Managers');
    });

    test('without positions the list offers the import', async ({ page, cli }) => {
        await completeSetup(cli);
        await deletePositions(cli);

        await page.goto(`${cli.serverUrl}${listPath}`);
        await expect(page.locator('#the-list')).toContainText('Start import');
        await expect(page.getByRole('link', { name: 'now', exact: true })).toHaveAttribute('href', /action=personioPositionsImport/);
    });

    test('the import can be started in the backend', async ({ page, cli }) => {
        test.slow();
        await completeSetup(cli);
        await deletePositions(cli);

        await page.goto(`${cli.serverUrl}${listPath}`);
        await page.getByRole('link', { name: 'Run import' }).first().click();

        // a dialog asks first, then shows the progress and the end of the import.
        const dialog = page.getByRole('dialog');
        await expect(dialog).toContainText('Do you really want to import open positions from');
        await dialog.getByRole('button', { name: 'Yes' }).click();
        await expect(dialog).toContainText('Positions has been imported', { timeout: 60_000 });
        expect(await getPositions(cli)).toHaveLength(11);

        // "OK" closes the dialog. The list then shows the positions.
        await dialog.getByRole('button', { name: 'OK' }).click();
        await expect(page.locator('.displaying-num').first()).toHaveText('11 items');
    });

    test('a single position can be viewed in the backend', async ({ page, cli }) => {
        await importPositions(cli);
        const position = (await getPositions(cli)).find((entry) => entry.title === 'Programme Advisor');

        await page.goto(`${cli.serverUrl}/wp-admin/post.php?post=${position?.id}&action=edit`);
        await expect(page.locator('body')).toContainText('Programme Advisor');
        await expect(page.locator('body')).not.toContainText('Sorry, you are not allowed');
    });
});
