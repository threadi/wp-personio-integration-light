<?php
/**
 * File for handling the base object for each extension.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

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
	 * This extension can be enabled by user.
	 *
	 * Defaults to true as most extensions will be.
	 *
	 * @var bool
	 */
	protected bool $can_be_enabled_by_user = true;

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
	protected string $extension_category = '';

	/**
	 * List of extensions this extension requires.
	 *
	 * @var array<string>
	 */
	protected array $requires = array();

	/**
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Extensions_Base
	 */
	protected static ?Extensions_Base $instance = null;

	/**
	 * Constructor, not used as this a Singleton object.
	 */
	public function __construct() {
		// add global settings for each extension.
		add_filter( 'personio_integration_settings', array( $this, 'add_global_settings' ) );
	}

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
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {}

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
	 * @param string $name The name to set.
	 *
	 * @return void
	 */
	public function set_name( string $name ): void {
		$this->name = $name;
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
		if ( 0 === $state ) {
			$new_state = 1;

			// enable all extension this extension requires.
			foreach ( $this->get_required_extensions() as $extension_class_name ) {
				// bail if "get_instance" does not exist.
				if ( ! method_exists( $extension_class_name, 'get_instance' ) ) {
					continue;
				}

				// bail if "get_instance" is not callable.
				if ( ! is_callable( $extension_class_name . '::get_instance' ) ) {
					continue;
				}

				// get the object.
				$obj = call_user_func( $extension_class_name . '::get_instance' );

				// bail if this is not an extension object.
				if ( ! $obj instanceof self ) {
					continue;
				}

				// enable this extension as it is required.
				$obj->set_enabled();
			}
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

	/**
	 * Return whether this extension can be enabled by the user (true) or not (false).
	 *
	 * @return bool
	 */
	public function can_be_enabled_by_user(): bool {
		return $this->can_be_enabled_by_user;
	}

	/**
	 * Add the global settings for each extension.
	 *
	 * @param array<array<string,bool|string>> $settings List of settings.
	 *
	 * @return array<array<string,bool|string>>
	 */
	public function add_global_settings( array $settings ): array {
		// bail if not setting field is set.
		if ( empty( $this->get_settings_field_name() ) ) {
			return $settings;
		}

		// bail if setting does already exist.
		if ( ! empty( $settings['hidden_section']['fields'][ $this->get_settings_field_name() ] ) ) {
			return $settings;
		}

		// add global setting to enable or disable this extension.
		$settings['hidden_section']['fields'][ $this->get_settings_field_name() ] = array(
			'register_attributes' => array(
				'type'         => 'integer',
				'default'      => $this->is_default_enabled() ? 1 : 0,
				'show_in_rest' => true,
			),
			'source'              => $this->get_plugin_source(),
		);

		// return resulting list.
		return $settings;
	}

	/**
	 * Whether this extension is enabled by default (true) or not (false).
	 *
	 * @return bool
	 */
	protected function is_default_enabled(): bool {
		return false;
	}

	/**
	 * Base function for uninstalling any extensions.
	 *
	 * @return void
	 */
	public function uninstall(): void {}

	/**
	 * Set the plugin source for this page builder support.
	 *
	 * @return string
	 */
	protected function get_plugin_source(): string {
		return WP_PERSONIO_INTEGRATION_PLUGIN;
	}

	/**
	 * Return the state.
	 *
	 * @return bool
	 */
	public function is_active(): bool {
		return false;
	}

	/**
	 * Return list of required extensions.
	 *
	 * @return array<string>
	 */
	private function get_required_extensions(): array {
		return $this->requires;
	}

	/**
	 * Return whether this extension is required by another.
	 *
	 * @return bool
	 */
	protected function is_required(): bool {
		// get the actual class name.
		$class_name = get_class( $this );

		// get all extension and check if they required the actual one.
		foreach ( Extensions::get_instance()->get_extensions_as_objects() as $extension_obj ) {
			// bail if this extension does not require the actual one.
			if ( ! in_array( $class_name, $extension_obj->get_required_extensions(), true ) ) {
				continue;
			}

			// bail if this extension is not enabled.
			if ( ! $extension_obj->is_enabled() ) {
				continue;
			}

			// break the loop as an extension requires this one.
			return true;
		}
		return false;
	}

	/**
	 * Return link to change state of this extension.
	 *
	 * @return string
	 */
	public function get_toggle_state_link(): string {
		return add_query_arg(
			array(
				'action'    => 'personio_integration_change_extension_state',
				'extension' => $this->get_name(),
				'nonce'     => wp_create_nonce( 'personio-integration-extension-state' ),
			),
			get_admin_url() . 'admin.php'
		);
	}
}
