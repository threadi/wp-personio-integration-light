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
class TextHints {

	/**
	 * Get the output.
	 *
	 * @param array $attributes The settings for this field.
	 *
	 * @return void
	 */
	public static function get( array $attributes ): void {
		if ( ! empty( $attributes['description'] ) ) {
			if ( ! empty( $attributes['highlight'] ) ) {
				echo '<p class="personio-integration-hint">' . wp_kses_post( $attributes['description'] ) . '</p>';
			} else {
				echo '<p>' . wp_kses_post( $attributes['description'] ) . '</p>';
			}
		}
	}
}
