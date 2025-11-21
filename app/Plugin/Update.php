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
use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\Extensions;
use PersonioIntegrationLight\PersonioIntegration\Imports\Xml;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\PersonioIntegration\Widgets;

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
	 * Constructor for this object.
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
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize the Updater.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'init', array( $this, 'run' ) );
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
		if ( ! Helper::is_development_mode_active() && version_compare( $installed_plugin_version, $db_plugin_version, '>' ) ) {
			if ( ! defined( 'PERSONIO_INTEGRATION_UPDATE_RUNNING ' ) ) {
				define( 'PERSONIO_INTEGRATION_UPDATE_RUNNING', 1 );
			}
			if ( version_compare( $db_plugin_version, '3.0.0', '<' ) ) {
				$this->version300();
			}
			if ( version_compare( $db_plugin_version, '3.1.0', '<' ) ) {
				$this->version310();
			}
			if ( version_compare( $db_plugin_version, '3.2.0', '<' ) ) {
				$this->version320();
			}
			if ( version_compare( $db_plugin_version, '4.0.0', '<' ) ) {
				$this->version400();
			}
			if ( version_compare( $db_plugin_version, '5.0.0', '<' ) ) {
				$this->version500();
			}

			// log that this update has been run.
			/* translators: %1$s and %2$s are replaced by the old and new version. */
			Log::get_instance()->add( sprintf( __( 'Personio Integration Light has been updated from %1$s to %2$s.', 'personio-integration-light' ), $db_plugin_version, $installed_plugin_version ), 'success', 'system' );

			// save the new plugin-version in DB.
			update_option( 'personioIntegrationVersion', $installed_plugin_version );

			// refresh permalinks.
			update_option( 'personio_integration_update_slugs', 1 );
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

		// initialize the settings.
		\PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings::get_instance()->activation();

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
		// enable the new help functions, if not already set.
		if ( ! get_option( 'personioIntegrationShowHelp' ) ) {
			update_option( 'personioIntegrationShowHelp', 1 );
		}
	}

	/**
	 * To run on update to version 4.0.0 or newer.
	 *
	 * @return void
	 */
	private function version400(): void {
		// get the actual value for setup and save it in the new field, if not already set.
		if ( ! get_option( 'esfw_completed' ) ) {
			update_option( 'esfw_completed', get_option( 'wp_easy_setup_completed' ) );
		}

		// clean the setup completed from multiple entries.
		$setup_completed = get_option( 'esfw_completed' );
		if ( is_array( $setup_completed ) ) {
			$setup_completed_new = array();
			foreach ( $setup_completed as $config_name ) {
				if ( in_array( $config_name, $setup_completed_new, true ) ) {
					continue;
				}
				$setup_completed_new[] = $config_name;
			}
			update_option( 'esfw_completed', $setup_completed_new );
		}

		// delete old setup options (except the main config as other plugins might use it).
		delete_option( 'wp_easy_setup_max_steps' );
		delete_option( 'wp_easy_setup_step' );
		delete_option( 'wp_easy_setup_step_label' );
		delete_option( 'wp_easy_setup_running' );
	}

	/**
	 * To run on update to version 5.0.0 or newer.
	 *
	 * @return void
	 */
	private function version500(): void {
		// enable the Import XML extension.
		Xml::get_instance()->set_enabled();

		// re-initiate each schedule to set the new intervals.
		Schedules::get_instance()->delete_all();
		Schedules::get_instance()->create_schedules();

		// enable new extensions.
		update_option( 'personioIntegrationPersonioAccountsStatus', 1 );

		// show hint to re-import positions.
		Positions::get_instance()->trigger_reimport_hint();

		// enable all widgets.
		foreach ( Widgets::get_instance()->get_widgets_as_objects() as $widget_obj ) {
			$widget_obj->set_enabled();
		}
	}
}
