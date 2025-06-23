<?php
/**
 * File to validate the list of emails from MultiField field.
 *
 * @package wp-personio-integration
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsValidation;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;

/**
 * Object which validates the list of emails from MultiField field.
 */
class Emails {
	/**
	 * Validate the setting for the number-field.
	 *
	 * @param array<int,string>|null $values New value.
	 *
	 * @return array<int,string>
	 */
	public static function validate( null|array $values ): array {
		// if it is not an array, create one.
		if ( ! is_array( $values ) ) {
			$values = array();
		}

		$errors = get_settings_errors();
		/**
		 * If a result-entry already exists, do nothing here.
		 *
		 * @see https://core.trac.wordpress.org/ticket/21989
		 */
		if ( Helper::check_if_setting_error_entry_exists_in_array( 'personioIntegrationUrls', $errors ) ) {
			return $values;
		}

		// get option.
		$option = str_replace( 'sanitize_option_', '', current_filter() );

		// calculate value counts.
		$value_counts = array_count_values( $values );

		// check each entry.
		foreach ( $values as $index => $value ) {
			// remove empty entries.
			if ( empty( $value ) ) {
				unset( $values[ $index ] );
				continue;
			}

			// cleanup the string.
			$value = trim( $value );

			// remove double ones one time.
			if ( ! empty( $value_counts[ $value ] ) && $value_counts[ $value ] > 1 ) {
				unset( $values[ $index ] );
				$value_counts = array_count_values( $values );
				continue;
			}

			// check if given string is a valid email.
			if( ! filter_var($value, FILTER_VALIDATE_EMAIL) ) {
				add_settings_error( $option, $option, __( 'The value entered does not appear to be an email address!', 'wp-personio-integration' ) );
				unset( $values[ $index ] );
			}
		}

		// return the values.
		return $values;
	}
}
