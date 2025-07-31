<?php
/**
 * File to validate a schedule interval setting.
 *
 * @package wp-personio-integration
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsValidation;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Object which validates the given schedule interval.
 */
class ScheduleInterval {
	/**
	 * Validate the setting for the number-field.
	 *
	 * @param string|null $value New value.
	 *
	 * @return string
	 */
	public static function validate( null|string $value ): string {
		// get option.
		$option = str_replace( 'sanitize_option_', '', current_filter() );

		// bail if value is empty.
		if ( empty( $value ) ) {
			add_settings_error( $option, $option, __( 'An interval has to be set.', 'personio-integration-light' ) );
			return '';
		}

		// check if the given interval exists.
		$intervals = wp_get_schedules();
		if ( empty( $intervals[ $value ] ) ) {
			/* translators: %1$s will be replaced by the name of the used interval */
			add_settings_error( $option, $option, sprintf( __( 'The given interval %1$s does not exists.', 'personio-integration-light' ), esc_html( $value ) ) );
		}

		// return the value.
		return $value;
	}
}
