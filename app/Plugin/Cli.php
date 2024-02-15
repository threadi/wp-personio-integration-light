<?php
/**
 * File for cli-commands of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\Import;
use PersonioIntegrationLight\PersonioIntegration\Imports;
use PersonioIntegrationLight\Plugin\Cli\Helper;

/**
 * Handler for recruitment from HR Personio
 */
class Cli {

	use Helper;

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
		$logs->add_log( 'WP CLI-command deleteAll has been used.', 'success' );

		// delete taxonomies.
		$this->delete_taxonomies();

		// delete position.
		$this->delete_positions_from_db();
	}

	/**
	 * Remove all position from local database.
	 *
	 * @param array $args Argument to delete positions.
	 * @since  1.0.0
	 * @return void
	 */
	public function delete_positions( array $args ): void {
		// set arguments if empty.
		if ( empty( $args ) ) {
			$args = array( 'WP CLI-command deletePositions', '' );
		}

		// log this event.
		$logs = new Log();
		$logs->add_log( sprintf( '%s has been used%s.', $args[0], $args[1] ), 'success' );

		// delete them.
		$this->delete_positions_from_db();
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
		Uninstaller::get_instance()->run( $delete_data );

		/**
		 * Run additional task for uninstallation.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array $delete_data Marker to delete all data or not.
		 */
		do_action( 'personio_integration_uninstaller', $delete_data );

		Installer::get_instance()->activation();

		/**
		 * Run additional task for installation.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 */
		do_action( 'personio_integration_installer' );
	}
}
