<?php
/**
 * File for handling installation of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Extensions;

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
	 * Constructor for this object.
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
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Activate the plugin.
	 *
	 * Either via activation-hook or via cli-plugin-reset.
	 *
	 * @return void
	 */
	public function activation(): void {
		// mark activation runner as running.
		define( 'PERSONIO_INTEGRATION_ACTIVATION_RUNNING', 1 );

		if ( is_multisite() ) {
			// loop through the blogs.
			foreach ( Helper::get_blogs() as $blog_id ) {
				// switch to the blog.
				switch_to_blog( $blog_id->blog_id );

				// run tasks for activation in this single blog.
				$this->activation_tasks();
			}

			// switch back to original blog.
			restore_current_blog();
		} else {
			// simply run the tasks on single-site-install.
			$this->activation_tasks();
		}
	}

	/**
	 * Define the tasks to run during activation of the plugin.
	 *
	 * Hint: do not run anything regarding extensions. This will be done at the end of the setup.
	 *
	 * @return void
	 */
	private function activation_tasks(): void {
		// mimik that setup has been completed.
		add_filter( 'personio_integration_light_setup_is_completed', '__return_true' );

		// run normal plugin init.
		Init::get_instance()->init();

		// install our db tables.
		Init::get_instance()->install_db_tables();

		// install schedules.
		Schedules::get_instance()->create_schedules();

		// add roles during installation.
		Roles::get_instance()->install();

		// add the main settings.
		Settings::get_instance()->add_the_settings();

		// add the settings from all extensions.
		Extensions::get_instance()->activation();

		// initiate the settings.
		\PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings::get_instance()->activation();

		// set marker to refresh permalinks.
		update_option( 'personio_integration_update_slugs', 1 );

		// show success message on cli.
		Helper::is_cli() ? \WP_CLI::success( 'Personio Integration Light activated. Thank you for using our plugin :-)' ) : false;
	}
}
