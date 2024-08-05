<?php
/**
 * File to save the setting for import page builder templates.
 *
 * @package wp-personio-integration
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsSavings;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PageBuilder\Page_Builders;
use PersonioIntegrationLight\PageBuilder\PageBuilder_Base;
use PersonioIntegrationLight\Plugin\Settings;

/**
 * Object which saves the page builder import setting for templates and run it if enabled.
 */
class PageBuilder {
	/**
	 * Save the marker.
	 *
	 * @param string|null $value The value to save.
	 *
	 * @return string
	 */
	public static function save( string|null $value ): string {
		// bail if value is not 1.
		if ( 1 !== absint( $value ) ) {
			return $value;
		}

		// get option.
		$option = str_replace( 'pre_update_option_', '', current_filter() );

		// get the depends-field-name as this is the field which enables this setting.
		$field_settings = Settings::get_instance()->get_settings_for_field( $option );

		// bail if no settings or page_builder is found.
		if ( empty( $field_settings ) || empty( $field_settings['page_builder'] ) ) {
			return $value;
		}

		// get the page builder object.
		$page_builder_obj_to_use = false;
		foreach ( Page_Builders::get_instance()->get_page_builders() as $page_builder ) {
			$page_builder_obj = call_user_func( $page_builder . '::get_instance' );
			if ( $page_builder_obj instanceof PageBuilder_Base && $page_builder_obj->get_name() === $field_settings['page_builder'] ) {
				$page_builder_obj_to_use = $page_builder_obj;
			}
		}

		// bail if no page builder could be found.
		if ( false === $page_builder_obj_to_use ) {
			return $value;
		}

		// import the templates.
		$page_builder_obj_to_use->set_enabled();

		// return the new value to save it via WP.
		return $value;
	}
}
