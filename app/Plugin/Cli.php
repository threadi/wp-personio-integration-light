<?php
/**
 * File for cli-commands of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

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
	 * @since        1.0.0
	 *
	 * @param array $delete_data Marker to delete all data or not.
	 *
	 * @return void
	 * @noinspection PhpUnused
	 **/
	public function reset_plugin( array $delete_data = array() ): void {
		// run uninstaller tasks.
		Uninstaller::get_instance()->run( $delete_data );

		/**
		 * Run additional tasks for uninstallation.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array $delete_data Marker to delete all data or not.
		 */
		do_action( 'personio_integration_uninstaller', $delete_data );

		// run installer tasks.
		Installer::get_instance()->activation();

		/**
		 * Run additional tasks for installation.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 */
		do_action( 'personio_integration_installer' );
	}
}
