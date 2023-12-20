<?php
/**
 * Plugin Name:       Personio Integration Light
 * Description:       Provides recruiting handling for Personio.
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Version:           @@VersionNumber@@
 * Author:            laOlaWeb
 * Author URI:        https://laolaweb.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       personio-integration-light
 *
 * @package personio-integration-light
 */

use personioIntegration\installer;

// set version number.
const WP_PERSONIO_INTEGRATION_VERSION = '@@VersionNumber@@';

// save plugin-path.
const WP_PERSONIO_INTEGRATION_PLUGIN = __FILE__;

// embed necessary files.
require_once __DIR__ . '/inc/autoload.php';
require_once __DIR__ . '/inc/constants.php';
require_once __DIR__ . '/inc/init.php';
require_once __DIR__ . '/inc/frontend.php';
require_once __DIR__ . '/inc/pagebuilder/gutenberg.php';

// only in admin.
if ( is_admin() ) {
	require_once __DIR__ . '/inc/admin.php';
	// include all settings-files.
	foreach ( glob( plugin_dir_path( WP_PERSONIO_INTEGRATION_PLUGIN ) . 'inc/settings/*.php' ) as $filename ) {
		include $filename;
	}
}

/**
 * On plugin activation.
 */
function personio_integration_on_activation(): void {
	Installer::activation();
}
register_activation_hook( WP_PERSONIO_INTEGRATION_PLUGIN, 'personio_integration_on_activation' );

/**
 * On plugin deactivation.
 *
 * @return void
 */
function personio_integration_on_deactivation(): void {
	wp_clear_scheduled_hook( 'personio_integration_schudule_events' );
}
register_deactivation_hook( WP_PERSONIO_INTEGRATION_PLUGIN, 'personio_integration_on_deactivation' );

/**
 * Register WP Cli.
 *
 * @noinspection PhpUnused
 * @noinspection PhpUndefinedClassInspection
 */
function personio_integration_cli_register_commands(): void {
	WP_CLI::add_command( 'personio', 'personioIntegration\Cli' );
}
add_action( 'cli_init', 'personio_integration_cli_register_commands' );
