/**
 * Helpers for the Playwright tests: prepare the website through PHP instead of clicking.
 *
 * All tests of one worker share one website (see fixtures.ts). Each test therefore says
 * what it needs, and does not rely on what a test before it left behind - Playwright
 * starts a fresh website whenever a test fails.
 */

// what we use of the Playground the fixture starts.
export type Cli = {
    serverUrl: string;
    playground: { run: (options: { code: string }) => Promise<{ text: string; errors?: string }> };
};

/**
 * The Personio URL used in the tests. It never leaves the website: the must-use plugin
 * of mockPersonio() answers every request to it with the XML of tests/Data.
 */
export const personioUrl = 'https://personio-integration-test.jobs.personio.com';

/**
 * Where the plugin, and with it tests/Data, is mounted in Playground.
 */
const pluginDir = '/wordpress/wp-content/plugins/personio-integration-light';

/**
 * Run PHP in the website, as administrator. Returns what the code prints.
 *
 * Values are handed over as "$args", so no test has to build PHP strings from its data.
 */
export async function php(cli: Cli, code: string, args: Record<string, unknown> = {}): Promise<string> {
    // @ts-ignore
    const encoded = Buffer.from(JSON.stringify(args)).toString('base64');
    const result = await cli.playground.run({
        code: `<?php
            require_once '/wordpress/wp-load.php';
            wp_set_current_user( get_users( array( 'role' => 'administrator', 'number' => 1 ) )[0]->ID );
            $args = json_decode( base64_decode( '${encoded}' ), true );
            ${code}`,
    });

    return result.text;
}

/**
 * Answer all requests to the test Personio URL inside the website with the XML files of
 * tests/Data, so neither the tests nor the import need the real Personio.
 *
 * - the default URL delivers tests/Data/positions.xml
 * - "?language=…" is kept, every language gets the same positions
 */
export async function mockPersonio(cli: Cli): Promise<void> {
    await php(
        cli,
        `wp_mkdir_p( WPMU_PLUGIN_DIR );
        file_put_contents( WPMU_PLUGIN_DIR . '/personio-mock.php', $args['code'] );`,
        {
            code: `<?php
/**
 * Plugin Name: Personio mock for the Playwright tests
 */
add_filter(
	'pre_http_request',
	static function ( $response, array $parsed_args, string $url ) {
		if ( ! str_starts_with( $url, ${JSON.stringify(personioUrl)} ) ) {
			return $response;
		}
		$body = '';
		if ( 'HEAD' !== $parsed_args['method'] ) {
			$body = (string) file_get_contents( ${JSON.stringify(pluginDir + '/tests/Data/positions.xml')} );
		}
		return array(
			'headers'  => array( 'last-modified' => gmdate( 'D, d M Y H:i:s' ) . ' GMT' ),
			'body'     => $body,
			'response' => array( 'code' => 200, 'message' => 'OK' ),
			'cookies'  => array(),
			'filename' => null,
		);
	},
	10,
	3
);
`,
        },
    );
}

/**
 * Set the Personio URL.
 */
export async function setPersonioUrl(cli: Cli, url: string = personioUrl): Promise<void> {
    await php(cli, `update_option( 'personioIntegrationUrl', $args['url'] ); flush_rewrite_rules();`, { url });
}

/**
 * Make the website look like the setup has been run: Personio URL set, setup marked as
 * completed. Only then the plugin shows its settings, the list of positions and the import.
 */
export async function completeSetup(cli: Cli): Promise<void> {
    await mockPersonio(cli);
    await setPersonioUrl(cli);
    await php(
        cli,
        `$completed = get_option( 'esfw_completed', array() );
        if ( ! is_array( $completed ) ) {
            $completed = array();
        }
        if ( ! in_array( 'personio-integration-light', $completed, true ) ) {
            $completed[] = 'personio-integration-light';
        }
        update_option( 'esfw_completed', $completed );
        // the intro tour would lie above every page of the plugin. It has its own test.
        update_option( 'personio_integration_intro', 1 );`,
    );
}

/**
 * Put the website back to before the setup: no Personio URL, no positions, setup not run.
 */
export async function resetSetup(cli: Cli): Promise<void> {
    await deletePositions(cli);
    await php(
        cli,
        `delete_option( 'personioIntegrationUrl' );
        delete_option( 'esfw_completed' );
        delete_option( 'esfw_step' );
        delete_option( 'esfw_max_steps' );
        delete_option( 'personio_integration_intro' );`,
    );
}

/**
 * Import the positions of tests/Data/positions.xml. Returns how many positions exist afterwards.
 */
export async function importPositions(cli: Cli): Promise<number> {
    await completeSetup(cli);
    const count = await php(
        cli,
        `update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 );
        ( new \\PersonioIntegrationLight\\PersonioIntegration\\Imports\\Xml() )->run();
        echo count( \\PersonioIntegrationLight\\PersonioIntegration\\Positions::get_instance()->get_positions() );`,
    );

    return parseInt(count.trim(), 10);
}

/**
 * Delete all positions.
 */
export async function deletePositions(cli: Cli): Promise<void> {
    await php(
        cli,
        `update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, 0 );
        \\PersonioIntegrationLight\\PersonioIntegration\\PostTypes\\PersonioPosition::get_instance()->delete_positions();`,
    );
}

/**
 * Return title and URL of the imported positions, as the website lists them.
 */
export async function getPositions(cli: Cli): Promise<Array<{ id: number; title: string; url: string }>> {
    const json = await php(
        cli,
        `$list = array();
        foreach ( \\PersonioIntegrationLight\\PersonioIntegration\\Positions::get_instance()->get_positions() as $position ) {
            $list[] = array(
                'id'    => $position->get_id(),
                'title' => $position->get_title(),
                'url'   => $position->get_link(),
            );
        }
        echo wp_json_encode( $list );`,
    );

    return JSON.parse(json);
}

/**
 * Create a published page with the given content. Returns its URL.
 */
export async function createPage(cli: Cli, title: string, content: string): Promise<string> {
    return php(
        cli,
        `$id = wp_insert_post( array( 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => $args['title'], 'post_content' => $args['content'] ) );
        echo get_permalink( $id );`,
        { title, content },
    );
}

/**
 * Where WordPress writes PHP errors to (see "defineWpConfigConsts" in fixtures.ts).
 */
const debugLog = '/wordpress/wp-content/debug.log';

/**
 * Empty the PHP error log.
 */
export async function clearPhpErrors(cli: Cli): Promise<void> {
    await php(cli, `@file_put_contents( $args['file'], '' );`, { file: debugLog });
}

/**
 * Return the PHP errors, warnings, notices and deprecations caused by this plugin since
 * the last clearPhpErrors(). Messages of WordPress itself or of other plugins are ignored.
 */
export async function getPhpErrors(cli: Cli): Promise<string[]> {
    const log = await php(cli, `echo (string) @file_get_contents( $args['file'] );`, { file: debugLog });

    return log
        .split(/\n(?=\[)/)
        .filter((entry) => /PHP (Fatal error|Parse error|Warning|Notice|Deprecated|Recoverable fatal error)/.test(entry))
        .filter((entry) => entry.includes('/plugins/personio-integration-light/'));
}
