<?php
/**
 * File to handle the import-schedule.
 *
 * @package personio-integration-light
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
	 * Name of the option used to enable this event.
	 *
	 * @var string
	 */
	protected string $option_name = 'personioIntegrationEnablePositionSchedule';

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
		if ( $this->is_enabled() ) {
			new \PersonioIntegrationLight\PersonioIntegration\Import();
		}
	}
}
