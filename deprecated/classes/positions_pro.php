<?php
/**
 * File to represent the old positions_pro-object from < 3.0.0.
 * This is here to prevent errors during updating both plugins. No pro-functions included.
 *
 * @deprecated since 3.0.0
 * @package personio-integration-light
 */

namespace personioIntegration;

class positions_pro {
	/**
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?positions_pro
	 */
	protected static ?positions_pro $instance = null;

	/**
	 * Constructor, not used as this a Singleton object.
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
	public static function get_instance(): positions_pro {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	public function get_position_pro( $id ) {
		_deprecated_function( __FUNCTION__, '3.0.0', '\PersonioIntegrationPro\PersonioIntegration\Position' );
		return new \personioIntegration\position( $id );
	}
}
