<?php
/**
 * File to handle the import-schedule.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Schedules;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\Imports;
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
	 * Name of the option used to enable this event.
	 *
	 * @var string
	 */
	protected string $interval_option_name = 'personioIntegrationPositionScheduleInterval';

	/**
	 * Define the default interval.
	 *
	 * @var string
	 */
	protected string $default_interval = 'daily';

	/**
	 * Name of the log category.
	 *
	 * @var string
	 */
	protected string $log_category = 'import';

	/**
	 * Initialize this schedule.
	 */
	public function __construct() {
		// get interval from settings.
		$this->interval = Settings::get_instance()->get_setting( $this->get_interval_option_name() );
	}

	/**
	 * Run this schedule.
	 *
	 * @return void
	 */
	public function run(): void {
		// if debug mode is enabled log this event.
		if ( 1 === absint( get_option( 'personioIntegration_debug', 0 ) ) ) {
			$log = new Log();
			$log->add_log( __( 'Automatic import of positions starting.', 'personio-integration-light' ), 'success', 'import' );
		}

		// bail if import is not enabled.
		if ( ! $this->is_enabled() ) {
			$log = new Log();
			$log->add_log( __( 'Automatic import of positions is not enabled.', 'personio-integration-light' ), 'success', 'import' );
		}

		// run the import.
		Imports::get_instance()->run();

		// if debug mode is enabled log this event.
		if ( 1 === absint( get_option( 'personioIntegration_debug', 0 ) ) ) {
			$log = new Log();
			$log->add_log( __( 'Automatic import of positions ended.', 'personio-integration-light' ), 'success', 'import' );
		}
	}
}
