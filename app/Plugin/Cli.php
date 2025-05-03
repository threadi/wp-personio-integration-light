<?php
/**
 * File for cli-commands of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\Imports;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\PersonioIntegration\Taxonomies;

/**
 * Handler for recruitment from HR Personio
 */
class Cli {
	/**
	 * Import actual open positions from Personio.
	 *
	 * @since  1.0.0
	 * @return void
	 * @noinspection PhpUnused
	 */
	public function import_positions(): void {
		$imports_obj = Imports::get_instance();
		$imports_obj->run();
	}

	/**
	 * Cleanup the database from plugin-data.
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function delete_all(): void {
		// log this event.
		$logs = new Log();
		$logs->add_log( 'WP CLI-command delete_all has been used.', 'success', 'cli' );

		// delete all taxonomies.
		Taxonomies::get_instance()->delete_all();

		// delete all positions.
		PersonioPosition::get_instance()->delete_positions();
	}

	/**
	 * Remove all position from local database.
	 *
	 * @since  1.0.0
	 * @return void
	 * @noinspection PhpUnused
	 */
	public function delete_positions(): void {
		PersonioPosition::get_instance()->delete_positions();
	}

	/**
	 * Resets all settings of this plugin.
	 *
	 * [--delete-all]
	 * : Reset all data and not just settings.
	 *
	 * [--not-light]
	 * : Prevent reset of light plugin.
	 *
	 * @since        1.0.0
	 *
	 * @param array<string,string> $attributes Marker to delete all data or not.
	 * @param array<string,string> $options List of options.
	 *
	 * @return void
	 * @noinspection PhpUnused
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function reset_plugin( array $attributes = array(), array $options = array() ): void {
		// check for delete all marker.
		$delete_all = isset( $options['delete-all'] ) ? 1 : 0;

		// run uninstaller tasks.
		if ( ! isset( $options['not-light'] ) ) {
			Uninstaller::get_instance()->run( array( $delete_all ) );
		}

		/**
		 * Run additional tasks for uninstallation via WP CLI.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 * @updated 4.0.0 Using options instead of attributes.
		 *
		 * @param array $options Options used to call this command.
		 */
		do_action( 'personio_integration_uninstaller', $options );

		// run installer tasks.
		Installer::get_instance()->activation();

		/**
		 * Run additional tasks for installation via WP CLI.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 */
		do_action( 'personio_integration_installer' );
	}
}
