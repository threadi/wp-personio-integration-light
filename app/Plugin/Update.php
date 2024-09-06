<?php
/**
 * File for handling updates of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Extensions;

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
			define( 'PERSONIO_INTEGRATION_UPDATE_RUNNING', 1 );
			$this->version300();
			$this->version310();
			$this->version320();

			// save new plugin-version in DB.
			update_option( 'personioIntegrationVersion', $installed_plugin_version );
		}
	}

	/**
	 * To run on update to version 3.0.0 or newer.
	 *
	 * @return void
	 */
	private function version300(): void {
		// get extensions.
		Extensions::get_instance()->init();

		// delete old wrong named interval.
		wp_clear_scheduled_hook( 'personio_integration_schudule_events' );

		// delete old transients.
		$old_transients = array(
			'personio_integration_elementor',
			'personio_integration_divi',
			'personio_integration_wpbakery',
			'personio_integration_beaver',
			'personio_integration_siteorigin',
			'personio_integration_themify',
			'personio_integration_avada',
		);
		foreach ( $old_transients as $transient ) {
			delete_transient( $transient );
		}

		// set default settings for new options.
		$settings_obj = Settings::get_instance();
		$settings_obj->set_settings();
		foreach ( $settings_obj->get_settings() as $section_settings ) {
			foreach ( $section_settings['fields'] as $field_name => $field_settings ) {
				if ( ! empty( $field_settings['register_attributes']['default'] ) && ! get_option( $field_name ) ) {
					update_option( $field_name, $field_settings['register_attributes']['default'], true );
				}
			}
		}

		// create our schedules.
		Schedules::get_instance()->create_schedules();

		// if Personio-URL is set, set setup and intro to complete.
		if ( Helper::is_personio_url_set() ) {
			$setup_obj = Setup::get_instance();
			$setup_obj->set_completed( $setup_obj->get_setup_name() );
			Intro::get_instance()->set_closed();
		}
	}

	/**
	 * To run on update to version 3.1.0 or newer.
	 *
	 * @return void
	 */
	private function version310(): void {
		// update db tables.
		Init::get_instance()->install_db_tables();
	}

	/**
	 * To run on update to version 3.2.0 or newer.
	 *
	 * @return void
	 */
	private function version320(): void {
		// enable the new help functions.
		update_option( 'personioIntegrationShowHelp', 1 );
	}
}
