<?php
/**
 * File as base for each schedule.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent also other direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define the base object for schedules.
 */
class Schedules_Base {
	/**
	 * Name of this event.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_schedule_events';

	/**
	 * Interval of this event.
	 *
	 * @var string
	 */
	protected string $interval;

	/**
	 * Return the name of this schedule.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Return the interval of this schedule.
	 *
	 * @return string
	 */
	public function get_interval(): string {
		return $this->interval;
	}

	/**
	 * Set the interval for this schedule.
	 *
	 * @param string $interval
	 *
	 * @return void
	 */
	public function set_interval( string $interval ): void {
		$this->interval = $interval;
	}

	/**
	 * Install this schedule, if it does not exist atm.
	 *
	 * @return void
	 */
	public function install(): void {}

	/**
	 * Run a single schedule.
	 *
	 * @return void
	 */
	public function run(): void {}

	/**
	 * Delete a single schedule.
	 *
	 * @return void
	 */
	public function delete(): void {
		wp_clear_scheduled_hook( $this->get_name() );
	}

	/**
	 * Return the event attributes.
	 *
	 * @return false|object
	 */
	public function get_event(): false|object {
		return wp_get_scheduled_event( $this->get_name() );
	}

	/**
	 * Reset this schedule.
	 *
	 * @return void
	 */
	public function reset(): void {
		$this->delete();
		$this->install();
	}
}
