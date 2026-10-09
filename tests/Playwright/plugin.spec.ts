import { test, expect } from './fixtures';
import { completeSetup, php } from './helpers';

test.describe('Plugin', () => {
    test.beforeEach(async ({ cli }) => {
        await completeSetup(cli);
    });

    test('Plugin active & positions loading', async ({ page, cli }) => {
        await page.goto(`${cli.serverUrl}/wp-admin/edit.php?post_type=personioposition`);
        await expect(page.locator('#wpbody-content')).toBeVisible();
        await expect(page).toHaveTitle(/Positions/);
    });

    test('Plugin settings can be saved', async ({ page, cli }) => {
        await page.goto(`${cli.serverUrl}/wp-admin/edit.php?post_type=personioposition&page=personioPositions`);
        await page.getByRole('button', { name: 'Save', exact: true }).click();

        // the confirmation is on the page twice: as the visible notice and as a hidden
        // announcement for screen readers. Only the notice is what a user sees.
        await expect(page.getByTestId('snackbar')).toContainText('Settings saved');
    });

    test('the plugin can be deactivated and activated again', async ({ page, cli }) => {
        await page.goto(`${cli.serverUrl}/wp-admin/plugins.php`);
        const row = page.locator('tr[data-slug="personio-integration-light"]');

        await row.getByRole('link', { name: /^Deactivate/ }).click();
        await expect(page.locator('#message')).toContainText('Plugin deactivated.');
        await expect(row.getByRole('link', { name: /^Activate/ })).toBeVisible();

        await row.getByRole('link', { name: /^Activate/ }).click();
        await expect(page.locator('#message')).toContainText('Plugin activated.');
        await expect(row.getByRole('link', { name: /^Deactivate/ })).toBeVisible();

        // the positions and the settings survive a deactivation.
        expect(await php(cli, `echo get_option( 'personioIntegrationUrl' );`)).not.toBe('');
    });

    test('the post type and the taxonomies are registered', async ({ cli }) => {
        const result = await php(
            cli,
            `echo wp_json_encode( array(
                'post_type' => post_type_exists( 'personioposition' ),
                'office'    => taxonomy_exists( 'personioOffice' ),
                'version'   => defined( 'WP_PERSONIO_INTEGRATION_VERSION' ),
            ) );`,
        );
        expect(JSON.parse(result)).toEqual({ post_type: true, office: true, version: true });
    });
});
