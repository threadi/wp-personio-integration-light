<?php
/**
 * File for handling updates of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Plugin\Schedules\Import;

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
		if (
			(
				(
					function_exists( 'wp_is_development_mode' ) && false === wp_is_development_mode( 'plugin' )
				)
				|| ! function_exists( 'wp_is_development_mode' )
			)
			&& version_compare( $installed_plugin_version, $db_plugin_version, '>' )
		) {
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
	private function version300(): void {
		// delete old wrong names interval.
		wp_clear_scheduled_hook( 'personio_integration_schudule_events' );

		// install new one.
		$schedule_obj = new Import();
		$schedule_obj->install();

		// set default settings for new options.
		foreach ( Settings::get_instance()->get_settings() as $section_settings ) {
			foreach ( $section_settings['fields'] as $field_name => $field_settings ) {
				if ( ! empty( $field_settings['default'] ) && ! get_option( $field_name ) ) {
					update_option( $field_name, $field_settings['default'], true );
				}
			}
		}

		// if Personio-URL is set, set setup and intro to complete.
		if ( Helper::is_personio_url_set() ) {
			Setup::get_instance()->set_completed();
			Intro::get_instance()->set_closed();
		}

		// set install-date if not set.
		if ( ! get_option( 'personioIntegrationLightInstallDate' ) ) {
			update_option( 'personioIntegrationLightInstallDate', time() );
		}
	}
}
