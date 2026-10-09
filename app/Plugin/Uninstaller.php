<?php
/**
 * File for handling uninstallation of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
\defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Dependencies\easyTransientsForWordPress\Transient;
use PersonioIntegrationLight\Dependencies\easyTransientsForWordPress\Transients;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Extensions;
use PersonioIntegrationLight\PersonioIntegration\Personio_Accounts;
use PersonioIntegrationLight\PersonioIntegration\Post_Type;
use PersonioIntegrationLight\PersonioIntegration\Post_Types;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\PersonioIntegration\Statistics;
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
	 * Marker whether data shared by all blogs (user meta, crypt key) should be removed.
	 *
	 * @var bool
	 */
	private bool $remove_global_data = false;

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
	public static function get_instance(): Uninstaller {
		if ( \is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Remove all plugin-data.
	 *
	 * Either via uninstall or via cli.
	 *
	 * If running network-wide on a multisite, the setting "personioIntegrationDeleteOnUninstall"
	 * of each single blog is used to decide whether all data in this blog should be deleted.
	 *
	 * @param array<int> $delete_data Marker to delete all data (not used if running network-wide on multisite).
	 * @param bool       $network_wide True to clean up every site of the network (only uninstall.php does this).
	 *
	 * @return void
	 */
	public function run( array $delete_data = array(), bool $network_wide = false ): void {
		// set deactivation runner to enable.
		if ( ! \defined( 'PERSONIO_INTEGRATION_DEACTIVATION_RUNNING' ) ) {
			\define( 'PERSONIO_INTEGRATION_DEACTIVATION_RUNNING', 1 );
		}

		if ( $network_wide && is_multisite() ) {
			// global data (user meta and the crypt key are shared by all blogs) is only removed
			// during the uninstallation if every blog wants its data to be deleted.
			$this->remove_global_data = \defined( 'WP_UNINSTALL_PLUGIN' );
			foreach ( Helper::get_blogs( true ) as $blog_id ) {
				if ( 1 !== absint( get_blog_option( $blog_id, 'personioIntegrationDeleteOnUninstall', 0 ) ) ) {
					$this->remove_global_data = false;
				}
			}

			// loop through the blogs.
			foreach ( Helper::get_blogs( true ) as $blog_id ) {
				// switch to the blog.
				switch_to_blog( $blog_id );

				// run tasks for deactivation in this single blog with the setting of this blog.
				$this->deinstallation_tasks( array( absint( get_option( 'personioIntegrationDeleteOnUninstall', 0 ) ) ), true );

				// switch back to the original blog.
				restore_current_blog();
			}
			return;
		}

		// global data is only removed during the uninstallation of a single-site-install, not by a reset.
		$this->remove_global_data = \defined( 'WP_UNINSTALL_PLUGIN' ) && ! is_multisite() && ! empty( $delete_data[0] ) && 1 === absint( $delete_data[0] );

		// simply run the tasks on single-site-install.
		$this->deinstallation_tasks( $delete_data, false );
	}

	/**
	 * Define the tasks to run during deactivation.
	 *
	 * @param array<int> $delete_data Whether all data should be removed or not (should be an array with value 1 for "yes").
	 * @param bool       $network_wide True to clean up every site of the network (only uninstall.php does this).
	 *
	 * @return void
	 */
	private function deinstallation_tasks( array $delete_data, bool $network_wide = false ): void { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		// delete all plugin-data.
		if ( ! empty( $delete_data[0] ) && 1 === absint( $delete_data[0] ) ) {
			// initialize the plugin.
			Init::get_instance()->init();

			// run the init hooks to set all settings.
			Settings::get_instance()->add_the_settings();
			Schedules::get_instance()->add_the_settings();
			foreach ( Extensions::get_instance()->get_extensions_as_objects() as $page_builder ) {
				$page_builder->add_the_settings();
			}
			Emails::get_instance()->add_the_settings();
			Statistics::get_instance()->add_the_settings();

			// get the settings object.
			$settings_obj = Settings::get_instance()->get_settings_object();

			// enable the settings.
			$settings_obj->activation();

			// clean managed settings.
			$settings_obj->delete_settings();

			// initialize the extensions to call their uninstalling routines later.
			Extensions::get_instance()->init();

			// reset Personio- and language-specific settings.
			Personio_Accounts::get_instance()->reset_personio_settings();

			// delete taxonomies.
			Taxonomies::get_instance()->delete_all();

			// delete positions.
			PersonioPosition::get_instance()->delete_positions();

			// remove custom options.
			foreach ( $this->get_options() as $option ) {
				delete_option( $option );
			}

			// remove user meta for each cpt we provide.
			foreach ( Post_Types::get_instance()->get_post_types() as $post_type ) {
				// create the classname.
				$classname = $post_type . '::get_instance';

				// bail if the classname is not callable.
				if ( ! \is_callable( $classname ) ) {
					continue;
				}

				// get the object.
				$obj = $classname();

				// bail if the object is not our own post_type.
				if ( ! $obj instanceof Post_Type ) {
					continue;
				}

				// bail if the post-type is not from this plugin.
				if ( ! $obj->is_from_plugin( WP_PERSONIO_INTEGRATION_PLUGIN ) ) {
					continue;
				}

				// delete the settings of this object from user meta (user meta is shared by all blogs in a multisite).
				if ( ! is_multisite() || $this->remove_global_data ) {
					delete_metadata( 'user', 0, 'manageedit-' . $obj->get_name() . 'columnshidden', '', true );
				}
			}

			// remove custom user meta (user meta is shared by all blogs in a multisite).
			if ( ! is_multisite() || $this->remove_global_data ) {
				foreach ( $this->get_user_meta_keys() as $meta_key ) {
					delete_metadata( 'user', 0, $meta_key, '', true );
				}
			}

			// remove custom transients.
			delete_transient( 'personio_integration_api_token' );

			// uninstall extensions.
			Extensions::get_instance()->uninstall_all();

			// remove the crypt data (e.g., the key) only during the uninstallation, if no encrypted data is left in any blog.
			if ( $this->remove_global_data ) {
				Crypt::get_instance()->uninstall();
			}
		}

		// remove the throttle transients of our schedules.
		foreach ( Schedules::get_instance()->get_schedule_object_names() as $schedule_object_name ) {
			// bail if the class does not exist.
			if ( ! class_exists( $schedule_object_name ) ) {
				continue;
			}

			// get the object.
			$schedule_obj = new $schedule_object_name();

			// bail if the object is not a Schedules_Base object.
			if ( ! $schedule_obj instanceof Schedules_Base ) {
				continue;
			}

			// delete the transient.
			delete_transient( 'personio_integration_schedule_failed_' . md5( $schedule_obj->get_name() ) );
		}

		// remove schedules.
		Schedules::get_instance()->delete_all();

		// remove widgets.
		Widgets::get_instance()->uninstall_all();

		// remove transients.
		foreach ( Transients::get_instance()->get_transients( false, true ) as $transient_obj ) {
			// bail if the object is not ours.
			if ( ! $transient_obj instanceof Transient ) { // @phpstan-ignore instanceof.alwaysTrue
				continue;
			}

			// delete it.
			$transient_obj->delete();
			$transient_obj->delete_dismiss();
		}

		// remove plugin update transient.
		delete_transient( 'personio_integration_light_plugin_update_notices' );

		// force to reload the permalink cache.
		delete_option( 'rewrite_rules' );

		// remove setup-options.
		Setup::get_instance()->uninstall();

		// remove roles from our plugin.
		Roles::get_instance()->uninstall();

		// delete our custom database-tables.
		Init::get_instance()->delete_db_tables();
	}

	/**
	 * Return list of options this plugin is using, which are not configured via @file Settings.php.
	 *
	 * @return array<string>
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
			WP_PERSONIO_INTEGRATION_DELETE_ERRORS,
			WP_PERSONIO_INTEGRATION_TRANSIENTS_LIST,
			WP_PERSONIO_INTEGRATION_IMPORT_NEW_POSITIONS,
			WP_PERSONIO_INTEGRATION_IMPORT_DELETED_POSITIONS,
			WP_PERSONIO_INTEGRATION_DELETE_COUNT,
			WP_PERSONIO_INTEGRATION_DELETE_MAX,
			'personioIntegrationLightInstallDate',
			'personio_integration_settings',
			'personio_integration_intro',
			'personioIntegrationPageBuilder',
			'personioIntegrationLicenseKey',
			'personioIntegrationInstallationId',
			'personioIntegrationClientId',
			'personioIntegrationApiSecret',
			'personio_integration_update_running',
			\PersonioIntegrationLight\PageBuilder\Gutenberg\Template_Styles::OPTION,
		);
	}

	/**
	 * Return list of user meta keys this plugin is using.
	 *
	 * @return array<string>
	 */
	private function get_user_meta_keys(): array {
		return array(
			'personio-integration-acknowledge-costs-loading',
		);
	}
}
