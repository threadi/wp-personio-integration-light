<?php
/**
 * File to handle a single text field for classic settings.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingFields;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Initialize the field.
 */
class ProHint {

	/**
	 * Get the output.
	 *
	 * @param array $attributes The settings for this field.
	 *
	 * @return void
	 */
	public static function get( array $attributes ): void {
		// show optional hint for our Pro-version.
		if ( ! empty( $attributes['pro_hint'] ) ) {
			$message = $attributes['pro_hint'];
			/**
			 * Show hint for Pro-plugin with individual text.
			 *
			 * @since 1.0.0 Available since first release.
			 *
			 * @param string $message The individual text.
			 */
			do_action( 'personio_integration_admin_show_pro_hint', $message );
		}
	}
}
