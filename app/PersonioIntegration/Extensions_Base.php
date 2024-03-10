<?php
/**
 * File for handling the base object for each extension.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Object to handle positions.
 */
class Extensions_Base {
	/**
	 * The internal name of this extension.
	 *
	 * @var string
	 */
	protected string $name = '';

	/**
	 * Name if the setting field which defines its state.
	 *
	 * @var string
	 */
	protected string $setting_field = '';

	/**
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Extensions_Base
	 */
	protected static ?Extensions_Base $instance = null;

	/**
	 * Constructor, not used as this a Singleton object.
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
	public static function get_instance(): Extensions_Base {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Return internal name of this extension.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Return label of this extension.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return '';
	}

	/**
	 * Return whether this extension is enabled (true) or not (false).
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return false;
	}

	/**
	 * Return the description for this extension.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return '';
	}

	/**
	 * Toggle the state of this extension.
	 *
	 * @return void
	 */
	public function toggle_state(): void {
		// get actual value.
		$state = absint( get_option( $this->get_settings_field_name() ) );

		// define opposite.
		$new_state = 0;
		if( 0 === $state ) {
			$new_state = 1;
		}

		// save it.
		update_option( $this->get_settings_field_name(), $new_state );
	}

	/**
	 * Return the name of the settings field which defines the state of this extension.
	 *
	 * @return string
	 */
	public function get_settings_field_name(): string {
		return $this->setting_field;
	}
}
