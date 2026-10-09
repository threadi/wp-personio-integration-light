import { test, expect } from './fixtures';
import { createPage, getPositions, importPositions, personioUrl } from './helpers';

/**
 * What visitors of the website see.
 */
test.describe('Positions in the frontend', () => {
    test.beforeEach(async ({ cli }) => {
        expect(await importPositions(cli)).toBe(11);
    });

    test('the archive lists the positions, at most 10 in Light', async ({ visitor, cli }) => {
        const response = await visitor.goto(`${cli.serverUrl}/positions/`);
        expect(response?.status()).toBe(200);

        // 11 positions are imported, Light shows 10 of them.
        await expect(visitor.locator('article[id^="post-"] .entry-title')).toHaveCount(10);
        await expect(visitor.getByRole('link', { name: 'Go to Market Manager', exact: true })).toBeVisible();
    });

    test('the archive links to the single positions', async ({ visitor, cli }) => {
        await visitor.goto(`${cli.serverUrl}/positions/`);
        await visitor.getByRole('link', { name: 'Programme Advisor', exact: true }).click();

        await expect(visitor).toHaveURL(/\/position\/programme-advisor\/$/);
        await expect(visitor.getByRole('heading', { name: 'Programme Advisor' }).first()).toBeVisible();
    });

    test('a single position shows its description and the application button', async ({ visitor, cli }) => {
        const position = (await getPositions(cli)).find((entry) => entry.title === 'Copy of Junior Compliance and Immigrations Manager');
        expect(position).toBeDefined();

        const response = await visitor.goto(position!.url);
        expect(response?.status()).toBe(200);

        await expect(visitor.getByRole('heading', { name: position!.title }).first()).toBeVisible();
        await expect(visitor.locator('body')).toContainText('Your mission');
        await expect(visitor.locator('body')).toContainText('The International Affairs Office Manager');

        // the button leads to the application form at Personio.
        const apply = visitor.getByRole('link', { name: 'Apply for this position' }).first();
        await expect(apply).toBeVisible();
        await expect(apply).toHaveAttribute('href', new RegExp(`^${personioUrl.replace(/\./g, '\\.')}/job/2140231`));
    });

    test('a position which does not exist answers with 404', async ({ visitor, cli }) => {
        const response = await visitor.goto(`${cli.serverUrl}/position/this-position-does-not-exist/`);
        expect(response?.status()).toBe(404);
    });

    test('the shortcode [personioPositions] lists positions on a page', async ({ visitor, cli }) => {
        const url = await createPage(cli, 'Jobs', '[personioPositions limit="3"]');

        await visitor.goto(url);
        await expect(visitor.locator('article[id^="post-"] .entry-title')).toHaveCount(3);
    });

    test('the shortcode [personioPosition] shows one position', async ({ visitor, cli }) => {
        const url = await createPage(cli, 'One job', '[personioPosition personioid="2140229"]');

        await visitor.goto(url);
        await expect(visitor.locator('body')).toContainText('Programme Advisor');
        await expect(visitor.locator('body')).not.toContainText('Go to Market Manager');
    });

    test('the list can be filtered', async ({ visitor, cli }) => {
        const url = await createPage(cli, 'Jobs with filter', '[personioPositions showfilter="1" filter="office" filtertype="select"]');

        await visitor.goto(url);
        const filter = visitor.locator('form.personio-position-filter');
        await expect(filter).toBeVisible();

        const office = filter.locator('select').first();
        await expect(office.locator('option', { hasText: 'Amsterdam' })).toHaveCount(1);
        await office.selectOption({ label: 'Amsterdam' });

        // the filter is a plain form: "Search" loads the page again with the filter in the URL.
        await Promise.all([
            visitor.waitForURL(/personiofilter/),
            filter.getByRole('button', { name: 'Search' }).click(),
        ]);

        // only "Social Media (Working Student)" is in Amsterdam.
        await expect(visitor.locator('article[id^="post-"] .entry-title')).toHaveCount(1);
        await expect(visitor.locator('article[id^="post-"] .entry-title')).toContainText('Social Media (Working Student)');
    });

    test('visitors can not open the backend of the plugin', async ({ visitor, cli }) => {
        await visitor.goto(`${cli.serverUrl}/wp-admin/edit.php?post_type=personioposition`);
        await expect(visitor).toHaveURL(/wp-login\.php/);
    });
});
