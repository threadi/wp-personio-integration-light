<?php
/**
 * File for handling updates of this plugin.
 *
 * @package personio-integration-light
 */

namespace App\Plugin;

use App\Helper;

/**
 * Helper-function for updates of this plugin.
 *
 * TODO testen
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

	public function init(): void {
		add_action( 'plugins_loaded', array( $this, 'run' ) );
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
					$this->version123();
					$this->version205();
					$this->version211();
					$this->version227();
					$this->version240();
					$this->version250();
					$this->version255();
					break;
			}

			// save new plugin-version in DB.
			update_option( 'personioIntegrationVersion', $installed_plugin_version );
		}
	}

	/**
	 * To run on update to (exact) version 1.2.3.
	 *
	 * @return void
	 */
	public function version123(): void {
		// set max age for log entries in days.
		if ( ! get_option( 'personioIntegrationTemplateBackToListButton' ) ) {
			update_option( 'personioIntegrationTemplateBackToListButton', 0 );
		}

		// update db-version.
		update_option( 'personioIntegrationVersion', WP_PERSONIO_INTEGRATION_VERSION );
	}

	/**
	 * To run on update to (exact) version 2.0.3
	 *
	 * @return void
	 */
	public function version203(): void {
		// set max age for log entries in days.
		if ( ! get_option( 'personioIntegrationUrl' ) ) {
			update_option( 'personioIntegrationUrl', '', true );
		}
	}

	/**
	 * To run on update to (exact) version 2.0.5
	 *
	 * @return void
	 */
	public function version205(): void {
		// take care that import schedule is installed and active.
		helper::set_import_schedule();

		// set initial value for debug to disabled if not set.
		if ( ! get_option( 'personioIntegration_debug' ) ) {
			update_option( 'personioIntegration_debug', 0 );
		}

		// set initial value for debug to disabled if not set.
		if ( ! get_option( 'personioIntegrationTemplateBackToListUrl' ) ) {
			update_option( 'personioIntegrationTemplateBackToListUrl', '' );
		}

		// set initial value for debug to disabled if not set.
		if ( ! get_option( 'personioIntegrationEnableFilter' ) ) {
			update_option( 'personioIntegrationEnableFilter', 0 );
		}
	}

	/**
	 * To run on update to (exact) version 2.1.1
	 *
	 * @return void
	 */
	public function version211(): void {
		$query  = array(
			'post_type'   => 'wp_template',
			'post_name'   => 'archive-' . WP_PERSONIO_INTEGRATION_CPT,
			'post_status' => 'any',
			'fields'      => 'ids',
		);
		$result = new WP_Query( $query );
		if ( 0 === $result->post_count ) {
			$archive_template = '
            <!-- wp:template-part {"slug":"header"} /-->

            <!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
            <main class="wp-block-group" style="margin-top:var(--wp--preset--spacing--70);margin-bottom:var(--wp--preset--spacing--70)"><!-- wp:query-title {"type":"archive","showPrefix":false,"align":"wide","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"}}}} /-->

            <!-- wp:wp-personio-integration/filter-list {"filter":["recruitingCategory","office","schedule"],"blockId":"061798b9-b51f-4903-8c3e-8d6d0e617011"} /-->

            <!-- wp:wp-personio-integration/list {"blockId":"ae57e576-b490-4ae6-b5cf-7cef4c9a8102"} /--></main>
            <!-- /wp:group -->

            <!-- wp:template-part {"slug":"footer","theme":"twentytwentythree","tagName":"footer"} /-->';
			$array            = array(
				'post_type'    => 'wp_template',
				'post_status'  => 'publish',
				'post_name'    => 'archive-' . WP_PERSONIO_INTEGRATION_CPT,
				'post_content' => $archive_template,
				'post_title'   => 'Archive: Stelle',
			);
			wp_insert_post( $array );
		}
	}

	/**
	 * To run on update to (exact) version 2.2.7.
	 *
	 * @return void
	 */
	public function version227(): void {
		// enable search extension.
		if ( ! get_option( 'personioIntegrationExtendSearch' ) ) {
			update_option( 'personioIntegrationExtendSearch', 1 );
		}
	}

	/**
	 * To run on update to (exact) version 2.4.0.
	 *
	 * @return void
	 */
	public function version240(): void {
		// set install-date if not set.
		if ( ! get_option( 'personioIntegrationLightInstallDate' ) ) {
			update_option( 'personioIntegrationLightInstallDate', time() );
		}
	}

	/**
	 * To run on update to (exact) version 2.5.0.
	 *
	 * @return void
	 */
	public function version250(): void {
		// add user role to manage positions if it does not exist.
		$personio_position_manager_role = get_role( 'manage_personio_positions' );
		if ( null === $personio_position_manager_role ) {
			$personio_position_manager_role = add_role( 'manage_personio_positions', __( 'Manage Personio-based Positions', 'personio-integration-light' ) );
		}
		$personio_position_manager_role->add_cap( 'read' ); // to enter wp-admin.
		$personio_position_manager_role->add_cap( 'read_' . WP_PERSONIO_INTEGRATION_CPT );
		$personio_position_manager_role->add_cap( 'manage_' . WP_PERSONIO_INTEGRATION_CPT );

		// get admin-role.
		$admin_role = get_role( 'administrator' );
		$admin_role->add_cap( 'read_' . WP_PERSONIO_INTEGRATION_CPT );
		$admin_role->add_cap( 'manage_' . WP_PERSONIO_INTEGRATION_CPT );
	}

	/**
	 * To run on update to (exact) version 2.5.5.
	 *
	 * @return void
	 */
	public function version255(): void {
		// set default jobdescription-template for detail-page.
		if ( ! get_option( 'personioIntegrationTemplateJobDescription' ) ) {
			update_option( 'personioIntegrationTemplateJobDescription', 'default' );
		}
	}

	/**
	 * To run on update to (exact) version 2.6.0.
	 *
	 * @return void
	 */
	public function version260(): void {
		// set default archive-template.
		if ( ! get_option( 'personioIntegrationTemplateContentListingTemplate' ) ) {
			update_option( 'personioIntegrationTemplateContentListingTemplate', 'default' );
		}
	}
}
