<?php
/**
 * File to handle the import-schedule.
 *
 * @package personio-intregation.
 */

namespace App\Plugin\Schedules;

use App\Plugin\Schedules_Base;

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
		// set interval from settings.
		$this->interval = get_option( 'personioIntegrationPositionScheduleInterval' );
	}

	/**
	 * Install this schedule, if it does not exist atm.
	 *
	 * @return void
	 */
	public function install(): void {
		if ( ! wp_next_scheduled( $this->get_name() ) ) {
			wp_schedule_event( time(), $this->get_interval(), $this->get_name() );
		}
	}

	/**
	 * Run this schedule.
	 *
	 * @return void
	 */
	public function run(): void {
		if ( 1 === absint( get_option( 'personioIntegrationEnablePositionSchedule', 0 ) ) ) {
			new \App\PersonioIntegration\Import();
		}
	}
}
