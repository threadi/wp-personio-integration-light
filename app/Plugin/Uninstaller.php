<?php
/**
 * File for handling uninstallation of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PageBuilder\Page_Builders;
use PersonioIntegrationLight\PersonioIntegration\Extensions;
use PersonioIntegrationLight\PersonioIntegration\Imports;
use PersonioIntegrationLight\PersonioIntegration\Post_Type;
use PersonioIntegrationLight\PersonioIntegration\Post_Types;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\PersonioIntegration\Taxonomies;
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
		// set deactivation runner to enable.
		define( 'PERSONIO_INTEGRATION_DEACTIVATION_RUNNING', 1 );

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
		global $wpdb;

		// remove schedules.
		Schedules::get_instance()->delete_all();

		// remove widgets.
		Widgets::get_instance()->uninstall();

		// remove transients.
		foreach ( Transients::get_instance()->get_transients() as $transient_obj ) {
			$transient_obj->delete();
		}

		// remove plugin update transient.
		delete_transient( 'personio_integration_light_plugin_update_notices' );

		// delete all plugin-data.
		if ( ! empty( $delete_data[0] ) && 1 === absint( $delete_data[0] ) ) {
			// initialize the extensions to call their uninstall routines.
			Extensions::get_instance()->init();

			// reset Personio- and language-specific settings.
			Imports::get_instance()->reset_personio_settings();

			// delete taxonomies.
			Taxonomies::get_instance()->delete_all();

			// delete position.
			PersonioPosition::get_instance()->delete_positions();

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

			// remove setup-options.
			\wpEasySetup\Setup::get_instance()->uninstall();

			// remove user meta for each cpt we provide.
			foreach ( Post_Types::get_instance()->get_post_types() as $post_type ) {
				$obj = call_user_func( $post_type . '::get_instance' );
				if ( $obj instanceof Post_Type && $obj->is_from_plugin( WP_PERSONIO_INTEGRATION_PLUGIN ) ) {
					$wpdb->delete( $wpdb->usermeta, array( 'meta_key' => $obj->get_name() ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
				}
			}

			// uninstall extensions.
			Extensions::get_instance()->uninstall();
		}

		// remove roles from our plugin.
		Roles::get_instance()->uninstall();

		// delete our custom database-tables.
		Init::get_instance()->delete_db_tables();
	}

	/**
	 * Return list of options this plugin is using which are not configured via @file Settings.php.
	 *
	 * @return array
	 */
	private function get_options(): array {
		return array(
			WP_PERSONIO_INTEGRATION_IMPORT_RUNNING,
			WP_PERSONIO_INTEGRATION_IMPORT_ERRORS,
			WP_PERSONIO_INTEGRATION_OPTION_COUNT,
			WP_PERSONIO_INTEGRATION_OPTION_MAX,
			WP_PERSONIO_INTEGRATION_IMPORT_STATUS,
			WP_PERSONIO_INTEGRATION_DELETE_RUNNING,
			WP_PERSONIO_INTEGRATION_DELETE_STATUS,
			WP_PERSONIO_INTEGRATION_TRANSIENTS_LIST,
			'personioIntegrationLightInstallDate',
			'personio_integration_settings',
			'personio_integration_schedules',
		);
	}
}
