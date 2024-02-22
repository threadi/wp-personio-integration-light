<?php
/**
 * File to handle the availability-schedule.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Schedules;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Plugin\Schedules_Base;

/**
 * Object for this schedule.
 */
class Availability extends Schedules_Base {

	/**
	 * Name of this event.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_schedule_availability';

	/**
	 * Initialize this schedule.
	 */
	public function __construct() {
		// get interval from settings.
		$this->interval = 'daily';
	}

	/**
	 * Run this schedule.
	 *
	 * @return void
	 */
	public function run(): void {
		if ( 1 === absint( get_option( 'personioIntegrationEnableAvailabilityCheck', 0 ) ) ) {
			\PersonioIntegrationLight\PersonioIntegration\Availability::get_instance()->run();
		}
	}
}
