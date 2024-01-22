<?php
/**
 * File for handling uninstallation of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Widgets\Widgets;

/**
 * Helper-function for plugin-activation and -deactivation.
 */
class Uninstaller {
	/**
	 * Instance of this object.
	 *
	 * @var ?Uninstaller
	 */
	private static ?Uninstaller $instance = null;

	/**
	 * Constructor for Init-Handler.
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Uninstaller {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Remove all plugin-data.
	 *
	 * Either via uninstall or via cli.
	 *
	 * @param array $delete_data Marker to delete all data.
	 * @return void
	 */
	public function run( array $delete_data = array() ): void {
		if ( is_multisite() ) {
			// get original blog id.
			$original_blog_id = get_current_blog_id();

			// loop through the blogs.
			foreach ( Helper::get_blogs() as $blog_id ) {
				// switch to the blog.
				switch_to_blog( $blog_id->blog_id );

				// run tasks for deactivation in this single blog.
				$this->deactivation_tasks( $delete_data );
			}

			// switch back to original blog.
			switch_to_blog( $original_blog_id );
		} else {
			// simply run the tasks on single-site-install.
			$this->deactivation_tasks( $delete_data );
		}
	}

	/**
	 * Define the tasks to run during deactivation.
	 *
	 * @param array $delete_data Whether all data should be removed or not (should be an array with value 1 for yes).
	 *
	 * @return void
	 */
	private function deactivation_tasks( array $delete_data ): void {
		// remove schedules.
		Schedules::get_instance()->delete_all();

		// remove widgets.
		Widgets::get_instance()->uninstall();

		// remove transients.
		foreach ( Transients::get_instance()->get_transients() as $transient_obj ) {
			$transient_obj->delete();
		}

		/**
		 * Delete manuel transients.
		 */
		foreach ( WP_PERSONIO_INTEGRATION_TRANSIENTS as $transient_name => $settings ) {
			delete_transient( $transient_name );
		}

		// delete all plugin-data.
		if ( ! empty( $delete_data[0] ) && 1 === absint( $delete_data[0] ) ) {
			// remove language-specific options.
			foreach ( Languages::get_instance()->get_languages() as $key => $lang ) {
				delete_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION . $key );
				delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 . $key );
				delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP . $key );
			}

			// delete all collected data.
			( new Cli() )->delete_all();

			// remove options from settings.
			$settings_obj = Settings::get_instance();
			$settings_obj->set_settings();
			foreach ( $settings_obj->get_settings() as $section_settings ) {
				foreach ( $section_settings['fields'] as $field_name => $field_settings ) {
					delete_option( $field_name );
				}
			}

			// remove manuel options.
			foreach ( $this->get_options() as $option ) {
				delete_option( $option );
			}
		}

		// remove roles from our plugin.
		Roles::get_instance()->uninstall();

		// delete our custom database-tables.
		global $wpdb;
		$wpdb->query( sprintf( 'DROP TABLE IF EXISTS %s', esc_sql( $wpdb->prefix . 'personio_import_logs' ) ) );
	}

	/**
	 * Return list of options this plugin is using which are not configured via @file Settins.php.
	 *
	 * @return array
	 */
	private function get_options(): array {
		return array(
			WP_PERSONIO_INTEGRATION_IMPORT_RUNNING,
			WP_PERSONIO_INTEGRATION_IMPORT_ERRORS,
			WP_PERSONIO_INTEGRATION_OPTION_COUNT,
			WP_PERSONIO_INTEGRATION_OPTION_MAX,
			'personioIntegrationPositionScheduleInterval',
			'personioIntegrationVersion',
			'personioTaxonomyDefaults',
			'personio_integration_transients',
			'personioIntegrationLightInstallDate',
			'wp_easy_setup_pi_max_steps',
			'wp_easy_setup_pi_step',
			'wp_easy_setup_pi_step_label',
			'wp_easy_setup_pi_running',
			'wp_easy_setup_completed',
		);
	}
}
