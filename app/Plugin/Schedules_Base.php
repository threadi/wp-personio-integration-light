<?php
/**
 * File for an object as a base object for each schedule.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Log;

/**
 * Define the base object for schedules.
 */
class Schedules_Base {
	/**
	 * Name of this event.
	 *
	 * @var string
	 */
	protected string $name = '';

	/**
	 * Name of the option used to enable this event.
	 *
	 * @var string
	 */
	protected string $option_name = '';

	/**
	 * Name of the option used to define the interval for this event.
	 *
	 * @var string
	 */
	protected string $interval_option_name = '';

	/**
	 * Name of the log category.
	 *
	 * @var string
	 */
	protected string $log_category = 'schedule';

	/**
	 * Interval of this event.
	 *
	 * @var string
	 */
	protected string $interval;

	/**
	 * Default interval of this event.
	 *
	 * @var string
	 */
	protected string $default_interval;

	/**
	 * Arguments for the schedule-event.
	 *
	 * @var list<mixed>
	 */
	protected array $args = array();

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
		$interval = $this->interval;
		$instance = $this;
		/**
		 * Filter the interval to a single schedule.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 * @param string $interval The interval.
		 * @param Schedules_Base $instance The schedule-object.
		 */
		return apply_filters( 'personio_integration_light_schedule_interval', $interval, $instance );
	}

	/**
	 * Set the interval for this schedule.
	 *
	 * @param string $interval The interval to set (e.g. "daily").
	 *
	 * @return void
	 */
	public function set_interval( string $interval ): void {
		$this->interval = $interval;
	}

	/**
	 * Run a single schedule.
	 *
	 * @return void
	 */
	public function run(): void {}

	/**
	 * Install this schedule if it does not exist atm.
	 *
	 * @return void
	 */
	public function install(): void {
		// bail if setup has not been completed.
		if ( ! Setup::get_instance()->is_completed() ) {
			return;
		}

		// bail if "personio_sqlite" is set.
		if ( 1 === absint( get_option( 'personio_sqlite', 0 ) ) ) {
			return;
		}

		// determine the interval to use: validated against the registered
		// cron-schedules, with a fallback to the default if it is unknown.
		$interval = $this->get_scheduled_interval();

		// if the schedule already exists, only (re)schedule it if its interval
		// changed - otherwise there is nothing to do.
		if ( wp_next_scheduled( $this->get_name(), $this->get_args() ) ) {
			// the recurrence currently stored in WP-cron for this event.
			$current_interval = wp_get_schedule( $this->get_name(), $this->get_args() );

			// nothing to do if the interval is unchanged.
			if ( $current_interval === $interval ) {
				return;
			}

			// the interval changed: remove the existing event so it is recreated
			// below with the new interval. wp_schedule_event() would otherwise
			// refuse to change the interval of an already-scheduled event.
			$this->delete();

			// log the re-schedule.
			/* translators: %1$s will be replaced by the name of the schedule, %2$s by the old interval, %3$s by the new interval. */
			Log::get_instance()->add( sprintf( __( 'Interval of schedule %1$s changed from %2$s to %3$s - rescheduling.', 'personio-integration-light' ), $this->get_name(), (string) $current_interval, $interval ), 'info', $this->get_log_category() );
		}

		// create the schedule.
		$result = wp_schedule_event( time(), $interval, $this->get_name(), $this->get_args(), true );

		// log event if the schedule could not be created.
		if ( is_wp_error( $result ) ) { // @phpstan-ignore function.impossibleType
			/* translators: %1$s will be replaced by the name of the schedule. */
			Log::get_instance()->add( sprintf( __( 'Error during creation of schedule %1$s:', 'personio-integration-light' ), $this->get_name() ) . ' <code>' . wp_json_encode( wp_json_encode( $result->get_error_messages() ) ) . '</code>', 'info', $this->get_log_category() );
		}
	}

	/**
	 * Delete a single schedule.
	 *
	 * @return void
	 */
	public function delete(): void {
		// delete the schedule and get the result.
		$result = wp_clear_scheduled_hook( $this->get_name(), $this->get_args() );

		// log event if the schedule could not be deleted.
		if ( is_wp_error( $result ) ) { // @phpstan-ignore function.impossibleType
			Log::get_instance()->add( __( 'Error during deleting of schedule:', 'personio-integration-light' ) . ' <code>' . wp_json_encode( $result->get_error_message() ) . '</code>', 'info', $this->get_log_category() );
		}
	}

	/**
	 * Return the event.
	 *
	 * @return false|object
	 */
	public function get_event(): false|object {
		// bail if the function does not exist.
		if ( ! function_exists( 'wp_get_scheduled_event' ) ) {
			return false;
		}

		// return the event.
		return wp_get_scheduled_event( $this->get_name(), $this->get_args() );
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

	/**
	 * Return the arguments for the schedule-event.
	 *
	 * @return list<mixed>
	 */
	public function get_args(): array {
		return $this->args;
	}

	/**
	 * Set the arguments for the schedule-event.
	 *
	 * @param list<mixed> $args The args to set for the hook-event of this schedule.
	 *
	 * @return void
	 */
	public function set_args( array $args ): void {
		$this->args = $args;
	}

	/**
	 * Return the option name, which enabled this schedule.
	 *
	 * @return string
	 */
	protected function get_option_name(): string {
		return $this->option_name;
	}

	/**
	 * Return whether the schedule has an option name configured.
	 *
	 * @return bool
	 */
	private function has_option_name(): bool {
		return ! empty( $this->get_option_name() );
	}

	/**
	 * Return whether this schedule should be enabled and active according to configuration.
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		$false    = false;
		$instance = $this;
		/**
		 * Filter whether to activate this schedule.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param bool $false True if this object should NOT be enabled.
		 * @param Schedules_Base $instance Actual object.
		 *
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( apply_filters( 'personio_integration_schedule_enabling', $false, $instance ) ) {
			return false;
		}

		// bail with true if no setting is configured.
		if ( ! $this->has_option_name() ) {
			return true;
		}

		// return the state of this schedule according to configuration.
		return 1 === absint( get_option( $this->get_option_name() ) );
	}

	/**
	 * Return the interval option name.
	 *
	 * @return string
	 */
	public function get_interval_option_name(): string {
		return $this->interval_option_name;
	}

	/**
	 * Return the interval option name.
	 *
	 * @return string
	 */
	public function get_default_interval(): string {
		return $this->default_interval;
	}

	/**
	 * Return the log category for this schedule.
	 *
	 * @return string
	 */
	public function get_log_category(): string {
		return $this->log_category;
	}

	/**
	 * Return the interval to schedule this event with.
	 *
	 * Validates the configured interval (incl. the value returned by the
	 * "personio_integration_light_schedule_interval" filter) against the
	 * registered WP cron-schedules. If the configured interval is not registered
	 * - e.g. it is left over from another (now inactive) plugin - we fall back to
	 * the class default so the schedule keeps running instead of failing silently
	 * with an "Event schedule does not exist" error.
	 *
	 * @return string
	 */
	protected function get_scheduled_interval(): string {
		// get the interval of this schedule.
		$interval = $this->get_interval();

		// get all schedules.
		$schedules = Intervals::get_instance()->get_intervals_for_settings();

		// use the configured interval if it is registered.
		if ( isset( $schedules[ $interval ] ) ) {
			return $interval;
		}

		// otherwise fall back to the class default, but only if that one is
		// actually registered. If neither is available we keep the configured
		// value and let wp_schedule_event() report the error (as before).
		if ( isset( $this->default_interval, $schedules[ $this->default_interval ] ) && '' !== $this->default_interval ) {
			// log the fallback so the misconfiguration is visible.
			/* translators: %1$s will be replaced by the invalid interval, %2$s by the name of the schedule, %3$s by the fallback interval. */
			Log::get_instance()->add( sprintf( __( 'The configured interval %1$s for schedule %2$s is not registered - falling back to %3$s.', 'personio-integration-light' ), '<code>' . $interval . '</code>', '<code>' . $this->get_name() . '</code>', '<code>' . $this->default_interval . '</code>' ), 'info', $this->get_log_category() );

			// return the default interval.
			return $this->default_interval;
		}

		// use the configured value if nothing better available.
		return $interval;
	}
}
