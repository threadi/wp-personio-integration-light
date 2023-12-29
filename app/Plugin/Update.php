<?php
/**
 * File for handling updates of this plugin.
 *
 * @package personio-integration-light
 */

namespace App\Plugin;

use App\Helper;
use WP_Query;

/**
 * Helper-function for updates of this plugin.
 */
class Update {
	/**
	 * Instance of this object.
	 *
	 * @var ?Update
	 */
	private static ?Update $instance = null;

	/**
	 * Constructor for Init-Handler.
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() { }

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Update {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Initialize the Updater.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'plugins_loaded', array( $this, 'run' ) );
	}

	/**
	 * Wrapper to run all version-specific updates, which are in this class.
	 *
	 * @return void
	 */
	public static function run_all_updates(): void {
		$obj = self::get_instance();
		$obj->version300();

		// reset import-flag.
		delete_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING );
	}

	/**
	 * Run check for updates.
	 *
	 * @return void
	 */
	public function run(): void {
		// get installed plugin-version (version of the actual files in this plugin).
		$installed_plugin_version = WP_PERSONIO_INTEGRATION_VERSION;

		// get db-version (version which was last installed).
		$db_plugin_version = get_option( 'personioIntegrationVersion', '1.0.0' );

		// compare version if we are not in development-mode.
		// TODO better solution for env-mode.
		if ( '@@VersionNumber@@' !== $installed_plugin_version && version_compare( $installed_plugin_version, $db_plugin_version, '>' ) ) {
			// TODO cleanup.
			switch ( $db_plugin_version ) {
				case '1.2.3':
					// nothing to do as 1.2.3 is the first version with this update-check.
					break;
				default:
					$this->version300();
					break;
			}

			// save new plugin-version in DB.
			update_option( 'personioIntegrationVersion', $installed_plugin_version );
		}
	}

	/**
	 * To run on update to (exact) version 3.0.0.
	 *
	 * @return void
	 */
	private function version300() {
	}
}
