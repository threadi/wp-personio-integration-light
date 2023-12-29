<?php
/**
 * File for handling uninstallation of this plugin.
 *
 * @package personio-integration-light
 */

namespace App\Plugin;

use App\Helper;

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
	 * @param array $delete_data
	 *
	 * @return void
	 */
	private function deactivation_tasks( array $delete_data ): void {
		// remove schedule.
		wp_clear_scheduled_hook( 'personio_integration_schudule_events' ); // TODO migrate wrong written name.

		// remove widgets.
		do_action( 'widgets_init' );

		// remove transients.
		// TODO use transients-object.
		foreach ( WP_PERSONIO_INTEGRATION_TRANSIENTS as $transient => $setting ) {
			delete_transient( $transient );
			delete_transient( 'pi-dismissed-' . md5( $transient ) );
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
			( new cli() )->delete_all();

			// remove options from settings.
			foreach ( Settings::get_instance()->get_settings() as $section ) {
				foreach ( $section['fields'] as $field_name => $field_setting ) {
					delete_option( $field_name );
				}
			}
		}

		// remove roles from our plugin.
		Roles::get_instance()->uninstall();

		// delete our custom database-tables.
		global $wpdb;
		$wpdb->query( sprintf( 'DROP TABLE IF EXISTS %s', esc_sql( $wpdb->prefix . 'personio_import_logs' ) ) );
	}
}
