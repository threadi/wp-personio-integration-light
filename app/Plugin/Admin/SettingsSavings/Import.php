<?php
/**
 * File to enable or disable the import of positions depending on new setting.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsSavings;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Object which saves the validated URL.
 */
class Import {
	/**
	 * Save the Personio-URL.
	 *
	 * @param string|null $old_value The old value of this field.
	 * @param string|null $new_value The new value of this field.
	 *
	 * @return string|null
	 * @noinspection PhpUnusedParameterInspection
	 */
	public static function save( string|null $old_value, string|null $new_value ): null|string {
		$import_schedule_obj = new \PersonioIntegrationLight\Plugin\Schedules\Import();
		if( 1 === absint( $new_value ) ) {
			$import_schedule_obj->install();
		}
		else {
			$import_schedule_obj->delete();
		}

		// return the new value to save it via WP.
		return $new_value;
	}
}
