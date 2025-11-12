<?php
/**
 * File to handle the schedule to update the access token for the Personio API v2.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Schedules;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Api;
use PersonioIntegrationLight\Plugin\Schedules_Base;

/**
 * Object for this schedule.
 */
class ApiAccessToken extends Schedules_Base {

	/**
	 * Name of this event.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_schedule_api_access_token';

	/**
	 * Name of the option used to enable this event.
	 *
	 * @var string
	 */
	protected string $option_name = 'personioIntegrationEnableApiAccessToken';

	/**
	 * Name of the log category.
	 *
	 * @var string
	 */
	protected string $log_category = 'api';

	/**
	 * Initialize this schedule.
	 */
	public function __construct() {
		$this->interval = 'personio_integration_daily';
	}

	/**
	 * Run this schedule.
	 *
	 * @return void
	 */
	public function run(): void {
		// bail if setting is not enabled.
		if ( ! $this->is_enabled() ) {
			return;
		}

		// run the token update.
		Api::get_instance()->update_access_token();
	}

	/**
	 * Install this schedule.
	 *
	 * @return void
	 */
	public function install(): void {
		// bail if it is not enabled.
		if ( ! $this->is_enabled() ) {
			return;
		}

		// install this schedule.
		parent::install();
	}
}
