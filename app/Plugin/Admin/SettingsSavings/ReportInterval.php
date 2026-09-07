<?php
/**
 * File to enable or disable the report.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin\SettingsSavings;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Plugin\Schedules\Report;

/**
 * Object which saves the report schedule.
 */
class ReportInterval {
	/**
	 * Save the new setting.
	 *
	 * @param ?string $value The value to save.
	 *
	 * @return string|null
	 */
	public static function save( ?string $value ): null|string {
		$report_schedule_obj = new Report();
		$report_schedule_obj->set_interval( get_option( 'personio_integration_email_interval_report' ) );
		if ( 1 === absint( $value ) ) {
			$report_schedule_obj->reset();
		} else {
			$report_schedule_obj->delete();
		}

		// return the new value to save it via WP.
		return $value;
	}
}
