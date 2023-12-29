<?php
/**
 * File for settings of this plugin.
 *
 * @package personio-integration-light
 */

/**
 * Add settings for admin-page via custom hook.
 * And add filter for each settings-field of our own plugin.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_admin_add_settings(): void {
	do_action( 'personio_integration_settings_add_settings' );

	// get settings-fields.
	global $wp_settings_fields;

	// loop through the fields.
	foreach ( $wp_settings_fields as $name => $sections ) {
		// filter for our own settings.
		if ( str_contains( $name, 'personioIntegration' ) ) {
			// loop through the sections of this setting.
			foreach ( $sections as $section ) {
				// loop through the field of this section.
				foreach ( $section as $field ) {
					$function_name = 'personio_integration_admin_sanitize_settings_field';
					if ( ! empty( $field['args']['sanitizeFunction'] ) && function_exists( $field['args']['sanitizeFunction'] ) ) {
						$function_name = $field['args']['sanitizeFunction'];
					}
					//add_filter( 'sanitize_option_' . $field['args']['fieldId'], $function_name, 10, 2 );
				}
			}
		}
	}
}
add_action( 'admin_init', 'personio_integration_admin_add_settings' );

/**
 * Sanitize string-field regarding its readonly-state.
 *
 * @param string $value The value of the field.
 * @param string $option The name of the field.
 * @return string
 * @noinspection PhpUnused
 */
function personio_integration_admin_sanitize_settings_field( string $value, string $option ): string {
	if ( empty( $value ) && ! empty( $_REQUEST[ $option . '_ro' ] ) ) {
		$value = sanitize_text_field( wp_unslash( $_REQUEST[ $option . '_ro' ] ) );
	}
	return $value;
}

/**
 * Sanitize array-field regarding its readonly-state.
 *
 * @param array $values  The values of the field.
 * @param string $option The name of the field.
 *
 * @return array
 * @noinspection PhpUnused
 */
function personio_integration_admin_sanitize_settings_field_array( array $values, string $option ): array {
	if ( empty( $values ) && ! empty( $_REQUEST[ $option . '_ro' ] ) ) {
		$values = explode( ',', sanitize_text_field( wp_unslash( $_REQUEST[ $option . '_ro' ] ) ) );
	}
	if ( is_null( $values ) ) {
		return array();
	}
	return $values;
}
