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
	 * The label of this extension.
	 *
	 * @var string
	 */
	protected string $label = '';

	/**
	 * The description of this extension.
	 *
	 * @var string
	 */
	protected string $description = '';

	/**
	 * Mark this as pro-extension.
	 *
	 * @var bool
	 */
	private bool $pro = false;

	/**
	 * Name of the setting field which defines its state.
	 *
	 * @var string
	 */
	protected string $setting_field = '';

	/**
	 * Name of the settings-page where the tab resides.
	 *
	 * @var string
	 */
	protected string $setting_page = 'personioPositions';

	/**
	 * Name of the setting tab where the setting field is visible.
	 *
	 * @var string
	 */
	protected string $setting_tab = '';

	/**
	 * Internal name of the used category.
	 *
	 * @var string
	 */
	protected string $extension_category = 'general';

	/**
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Extensions_Base
	 */
	protected static ?Extensions_Base $instance = null;

	/**
	 * Constructor, not used as this a Singleton object.
	 */
	public function __construct() {}

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
	 * Set the internal name of this extension.
	 *
	 * @param string $string
	 *
	 * @return void
	 */
	public function set_name( string $string ): void {
		$this->name = $string;
	}

	/**
	 * Return label of this extension.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return $this->label;
	}

	/**
	 * Set the label of this extension.
	 *
	 * @param string $label The label.
	 *
	 * @return void
	 */
	public function set_label( string $label ): void {
		$this->label = $label;
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
	 * Return the category for this extension.
	 *
	 * @return string
	 */
	public function get_category(): string {
		return $this->extension_category;
	}

	/**
	 * Set the category to use for this extension.
	 *
	 * @param string $category The internal name of the category.
	 *
	 * @return void
	 */
	public function set_category( string $category ): void {
		$this->extension_category = $category;
	}

	/**
	 * Return the description for this extension.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * Set the description for this extension.
	 *
	 * @param string $description The description.
	 *
	 * @return void
	 */
	public function set_description( string $description ): void {
		$this->description = $description;
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
	 * Toggle the state of this extension.
	 *
	 * @return void
	 */
	public function set_enabled(): void {
		update_option( $this->get_settings_field_name(), 1 );
	}

	/**
	 * Toggle the state of this extension.
	 *
	 * @return void
	 */
	public function set_disabled(): void {
		update_option( $this->get_settings_field_name(), 0 );
	}

	/**
	 * Return the name of the settings field which defines the state of this extension.
	 *
	 * @return string
	 */
	public function get_settings_field_name(): string {
		return $this->setting_field;
	}

	/**
	 * Return whether this is a pro-extension.
	 *
	 * @return bool
	 */
	public function is_pro(): bool {
		return $this->pro;
	}

	/**
	 * Mark this extension as pro-extension.
	 *
	 * @param bool $pro True to mark as pro.
	 *
	 * @return void
	 */
	public function set_pro( bool $pro ): void {
		$this->pro = $pro;
	}

	/**
	 * Return the name of the settings-page.
	 *
	 * @return string
	 */
	public function get_settings_page(): string {
		return $this->setting_page;
	}

	/**
	 * Return the name of the setting-tab.
	 *
	 * @return string
	 */
	public function get_setting_tab(): string {
		return $this->setting_tab;
	}

	/**
	 * Return whether this extension has a custom state.
	 *
	 * @return bool
	 */
	public function has_custom_state(): bool {
		return ! empty( $this->get_custom_state() );
	}

	/**
	 * Return the custom state for this extension.
	 *
	 * @return string
	 */
	public function get_custom_state(): string {
		return '';
	}
}
