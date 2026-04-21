<?php
/**
 * This file defines the deprecated settings usage of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use easySettingsForWordPress\Page;

/**
 * Object, which handles the deprecated settings usage of this plugin.
 */
class DeprecatedSettings {
	/**
	 * Instance of the actual object.
	 *
	 * @var ?DeprecatedSettings
	 */
	private static ?DeprecatedSettings $instance = null;

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
	 * Return an instance of this object as a singleton.
	 *
	 * @return DeprecatedSettings
	 */
	public static function get_instance(): DeprecatedSettings {
		if ( is_null( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 * Return false for request of the page to prevent any other processing.
	 *
	 * @return Page
	 */
	public function get_page(): Page {
		return new Page( Settings::get_instance()->get_settings_object() );
	}

	/**
	 * Return false for request of the section to prevent any other processing.
	 *
	 * @param mixed $section_obj The parameter used.
	 *
	 * @return bool
	 */
	public function set_section( mixed $section_obj ): bool {
		if ( empty( $section_obj ) ) {
			return true;
		}
		return false;
	}

	/**
	 * Return false for request of the setting to prevent any other processing.
	 *
	 * @return DeprecatedSetting
	 */
	public function add_setting(): DeprecatedSetting {
		return new DeprecatedSetting();
	}

	/**
	 * Return a new settings-page object.
	 *
	 * @param mixed $settings_obj The parameter used.
	 *
	 * @return Page
	 */
	public function add_page( mixed $settings_obj ): Page {
		if ( empty( $settings_obj ) ) {
			return new Page( Settings::get_instance()->get_settings_object() );
		}
		return new Page( Settings::get_instance()->get_settings_object() );
	}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param string $type The parameter used.
	 *
	 * @return void
	 */
	public function set_type( string $type ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param string $type The parameter used.
	 *
	 * @return void
	 */
	public function set_description( string $type ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param string $type The parameter used.
	 *
	 * @return void
	 */
	public function set_placeholder( string $type ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param mixed $type The parameter used.
	 *
	 * @return void
	 */
	public function set_sanitize_callback( mixed $type ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param mixed $type The parameter used.
	 *
	 * @return void
	 */
	public function set_title( mixed $type ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param mixed $type The parameter used.
	 *
	 * @return void
	 */
	public function set_options( mixed $type ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param mixed $type The parameter used.
	 *
	 * @return void
	 */
	public function add_depend( mixed $type ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param mixed $type The parameter used.
	 *
	 * @return void
	 */
	public function set_button_title( mixed $type ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param mixed $type The parameter used.
	 *
	 * @return void
	 */
	public function set_button_url( mixed $type ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param mixed $type The parameter used.
	 *
	 * @return void
	 */
	public function add_class( mixed $type ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param mixed $type The parameter used.
	 *
	 * @return void
	 */
	public function add_data( mixed $type ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param mixed $type The parameter used.
	 *
	 * @return void
	 */
	public function set_readonly( mixed $type ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param mixed $type The parameter used.
	 *
	 * @return void
	 */
	public function get_setting( mixed $type ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @param mixed $type The parameter used.
	 *
	 * @return void
	 */
	public function set_menu_parent_slug( mixed $type ): void {}

	/**
	 * Pseudo-set the type for the setting.
	 *
	 * @return void
	 */
	public function display(): void {}
}
