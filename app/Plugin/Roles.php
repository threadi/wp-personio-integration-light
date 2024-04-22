<?php
/**
 * File to handle roles used from this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;

/**
 * Object to handle roles.
 */
class Roles {
	/**
	 * Instance of this object.
	 *
	 * @var ?Roles
	 */
	private static ?Roles $instance = null;

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
	public static function get_instance(): Roles {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'admin_init', array( $this, 'allow_save_settings' ) );
	}

	/**
	 * Install the roles we use.
	 *
	 * @return void
	 */
	public function install(): void {
		// add user role to manage positions if it does not exist.
		$personio_position_manager_role = get_role( 'manage_personio_positions' );
		if ( is_null( $personio_position_manager_role ) ) {
			$personio_position_manager_role = add_role( 'manage_personio_positions', __( 'Manage Personio-based Positions', 'personio-integration-light' ) );
		}
		if ( ! is_null( $personio_position_manager_role ) ) {
			$personio_position_manager_role->add_cap( 'read' ); // to enter wp-admin.
			$personio_position_manager_role->add_cap( 'read_' . PersonioPosition::get_instance()->get_name() );
			$personio_position_manager_role->add_cap( 'manage_' . PersonioPosition::get_instance()->get_name() );
		}

		// get admin-role.
		$admin_role = get_role( 'administrator' );
		if ( ! is_null( $admin_role ) ) {
			$admin_role->add_cap( 'read_' . PersonioPosition::get_instance()->get_name() );
			$admin_role->add_cap( 'manage_' . PersonioPosition::get_instance()->get_name() );
		}
	}

	/**
	 * Remove the role we use.
	 *
	 * @return void
	 */
	public function uninstall(): void {
		/**
		 * Remove our own role.
		 */
		remove_role( 'manage_personio_positions' );

		/**
		 * Remove our capabilities from other roles.
		 */
		global $wp_roles;
		foreach ( $wp_roles->roles as $role_name => $settings ) {
			$role = get_role( $role_name );
			$role->remove_cap( 'manage_' . PersonioPosition::get_instance()->get_name() );
			$role->remove_cap( 'read_' . PersonioPosition::get_instance()->get_name() );
		}
	}

	/**
	 * Allow our own capability to save settings.
	 *
	 * @return void
	 */
	public function allow_save_settings(): void {
		$settings_pages = array(
			'personioIntegrationMainSettings',
			'personioIntegrationPositionsTemplates',
			'personioIntegrationPositionsImportExport',
			'personioIntegrationPositionsAdvanced',
		);
		foreach ( apply_filters( 'personio_integration_admin_settings_pages', $settings_pages ) as $settings_page ) {
			add_filter(
				'option_page_capability_' . $settings_page,
				function () {
					return 'manage_' . PersonioPosition::get_instance()->get_name();
				},
				10,
				0
			);
		}
	}
}
