<?php
/**
 * File to enable or disable the availability check for positions depending on new setting.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsSavings;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Object which saves the import schedule.
 */
class Availability {
	/**
	 * Save the new setting.
	 *
	 * @param ?string $value The value to save.
	 *
	 * @return string|null
	 */
	public static function save( ?string $value ): null|string {
		$import_schedule_obj = new \PersonioIntegrationLight\Plugin\Schedules\Availability();
		if ( 1 === absint( $value ) ) {
			$import_schedule_obj->install();
		} else {
			$import_schedule_obj->delete();
		}

		// return the new value to save it via WP.
		return $value;
	}
}
