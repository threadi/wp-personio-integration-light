<?php
/**
 * File to handle the import-schedule.
 *
 * @package personio-intregation.
 */

namespace PersonioIntegrationLight\Plugin\Schedules;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Plugin\Schedules_Base;
use PersonioIntegrationLight\Plugin\Settings;

/**
 * Object for this schedule.
 */
class Import extends Schedules_Base {

	/**
	 * Name of this event.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_schedule_events';

	/**
	 * Initialize this schedule.
	 */
	public function __construct() {
		// get interval from settings.
		$this->interval = Settings::get_instance()->get_setting( 'personioIntegrationPositionScheduleInterval' );
	}

	/**
	 * Run this schedule.
	 *
	 * @return void
	 */
	public function run(): void {
		if ( 1 === absint( get_option( 'personioIntegrationEnablePositionSchedule', 0 ) ) ) {
			new \PersonioIntegrationLight\PersonioIntegration\Import();
		}
	}
}
