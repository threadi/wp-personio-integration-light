<?php
/**
 * File to handle the report-schedule.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Schedules;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Plugin\Schedules_Base;

/**
 * Object for this schedule.
 */
class Report extends Schedules_Base {

	/**
	 * Name of this event.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_schedule_report';

	/**
	 * Name of the option used to enable this event.
	 *
	 * @var string
	 */
	protected string $option_name = 'personio_integration_email_report';

	/**
	 * Name of the option used for the interval of this event.
	 *
	 * @var string
	 */
	protected string $interval_option_name = 'personio_integration_email_interval_report';

	/**
	 * Define the default interval.
	 *
	 * @var string
	 */
	protected string $default_interval = 'weekly';

	/**
	 * Initialize this schedule.
	 */
	public function __construct() {
		// get interval from settings.
		$this->interval = get_option( $this->get_interval_option_name() );
	}

	/**
	 * Run this schedule.
	 *
	 * @return void
	 */
	public function run(): void {
		$report_mail_object = new \PersonioIntegrationLight\Plugin\Emails\Report();
		$report_mail_object->send();
	}
}
