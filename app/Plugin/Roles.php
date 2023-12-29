<?php
/**
 * File to handle roles used from this plugin.
 *
 * @package personio-integration-light
 */

namespace App\Plugin;

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
			$personio_position_manager_role->add_cap( 'read_' . WP_PERSONIO_INTEGRATION_CPT );
			$personio_position_manager_role->add_cap( 'manage_' . WP_PERSONIO_INTEGRATION_CPT );
		}

		// get admin-role.
		$admin_role = get_role( 'administrator' );
		if ( ! is_null( $admin_role ) ) {
			$admin_role->add_cap( 'read_' . WP_PERSONIO_INTEGRATION_CPT );
			$admin_role->add_cap( 'manage_' . WP_PERSONIO_INTEGRATION_CPT );
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
			$role->remove_cap( 'manage_' . WP_PERSONIO_INTEGRATION_CPT );
			$role->remove_cap( 'read_' . WP_PERSONIO_INTEGRATION_CPT );
		}
	}
}
