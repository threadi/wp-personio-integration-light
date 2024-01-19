<?php
/**
 * File for cli-commands of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\Import;
use PersonioIntegrationLight\Plugin\Cli\Helper;

/**
 * Handler for recruitment from HR Personio
 */
class Cli {

	use Helper;

	/**
	 * Get actual open positions from Personio.
	 *
	 * @since  1.0.0
	 * @return void
	 * @noinspection PhpUnused
	 */
	public function get_positions(): void {
		new Import();
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
	 * @param array $delete_data Marker to delete all data or not.
	 * @since        1.0.0
	 * @return void
	 */
	public function reset_plugin( array $delete_data = array() ): void {
		Uninstaller::get_instance()->run( $delete_data );
		Installer::get_instance()->activation();
	}
}
