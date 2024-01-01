<?php
/**
 * File for handling installation of this plugin.
 *
 * @package personio-integration-light
 */

namespace App\Plugin;

use App\Helper;
use App\Log;
use App\Plugin\Schedules\Import;

/**
 * Helper-function for plugin-activation and -deactivation.
 */
class Installer {

	/**
	 * Instance of this object.
	 *
	 * @var ?Installer
	 */
	private static ?Installer $instance = null;

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
	public static function get_instance(): Installer {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Activate the plugin.
	 *
	 * Either via activation-hook or via cli-plugin-reset.
	 *
	 * @return void
	 */
	public function activation(): void {
		if ( is_multisite() ) {
			// get original blog id.
			$original_blog_id = get_current_blog_id();

			// loop through the blogs.
			foreach ( Helper::get_blogs() as $blog_id ) {
				// switch to the blog.
				switch_to_blog( $blog_id->blog_id );

				// run tasks for activation in this single blog.
				$this->activation_tasks();
			}

			// switch back to original blog.
			switch_to_blog( $original_blog_id );
		} else {
			// simply run the tasks on single-site-install.
			$this->activation_tasks();
		}
	}

	/**
	 * Define the tasks to run during activation.
	 *
	 * @return void
	 */
	private function activation_tasks(): void {
		$error = false;

		// check if simplexml is available on this system.
		if ( ! function_exists( 'simplexml_load_string' ) ) {
			$transient_obj = Transients::get_instance()->add();
			$transient_obj->set_dismissible_days( 0 );
			$transient_obj->set_name( 'personio_integration_no_simplexml' );
			$transient_obj->set_message( '<strong>'.__( 'Plugin was not activated!', 'personio-integration-light' ).'</strong><br>'.__( 'The PHP extension simplexml is missing on the system. Please contact your hoster about this.', 'personio-integration-light' ) );
			$transient_obj->set_type( 'error' );
			$transient_obj->save();
			$error = true;
		}

		if ( false === $error ) {
			// set interval to daily if it is not set atm.
			if ( ! get_option( 'personioIntegrationPositionScheduleInterval' ) ) {
				update_option( 'personioIntegrationPositionScheduleInterval', 'daily' );
			}

			// install import schedule.
			$import_obj = new Import();
			$import_obj->install();

			// set default settings.
			foreach ( Settings::get_instance()->get_settings() as $section_settings ) {
				foreach ( $section_settings['fields'] as $field_name => $field_settings ) {
					if ( ! empty( $field_settings['default'] ) && ! get_option( $field_name ) ) {
						update_option( $field_name, $field_settings['default'], true );
					}
				}
			}

			// install the roles we use.
			Roles::get_instance()->install();

			// run all updates.
			Update::run_all_updates();

			// save the current DB-version of this plugin.
			update_option( 'personioIntegrationVersion', WP_PERSONIO_INTEGRATION_VERSION );

			// refresh permalinks.
			set_transient( 'personio_integration_update_slugs', 1 );

			// initialize Log-database-table.
			$log = new Log();
			$log->create_table();

			\App\Helper::is_cli() ? \WP_CLI::success( 'Personio Integration Light activated. Thank you for using our plugin.' ) : false;
		}
	}
}
