<?php
/**
 * Tests for the shared schedule machinery used by ALL schedules:
 * PersonioIntegrationLight\Plugin\Schedules_Base (install/delete/reset,
 * interval-aware rescheduling, invalid-interval fallback) and the reconcile
 * logic in PersonioIntegrationLight\Plugin\Schedules::check_events().
 *
 * Because every concrete schedule (Import, Availability, ApiAccessToken, Report,
 * and the Pro schedules) is a thin subclass of Schedules_Base, testing the base
 * plus the manager here covers the scheduling behaviour of all of them without
 * duplicating a near-identical test per schedule.
 *
 * No test waits for a cron to fire: we assert on the scheduled STATE in WP's
 * cron array (wp_next_scheduled / wp_get_schedule).
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Tests\Unit\Scenarios;

use PersonioIntegrationLight\Plugin\Schedules;
use PersonioIntegrationLight\Plugin\Schedules_Base;
use PersonioIntegrationLight\Tests\PersonioTestCase;

/**
 * A minimal, controllable schedule used as a fixture. It is injected into the
 * manager via the 'personio_integration_schedules' filter.
 */
class Fixture_Schedule extends Schedules_Base {
	/**
	 * The event name.
	 *
	 * @var string
	 */
	protected string $name = 'personio_integration_test_schedule';

	/**
	 * The interval option name.
	 *
	 * @var string
	 */
	protected string $interval_option_name = 'personio_integration_test_interval';

	/**
	 * The enable option name.
	 *
	 * @var string
	 */
	protected string $option_name = 'personio_integration_test_enable';

	/**
	 * The default interval.
	 *
	 * @var string
	 */
	protected string $default_interval = 'personio_integration_daily';

	/**
	 * The log category.
	 *
	 * @var string
	 */
	protected string $log_category = 'system';

	/**
	 * Initialize this schedule (read interval from the option, like the real ones).
	 */
	public function __construct() {
		$this->interval = (string) get_option( $this->get_interval_option_name() );
		if ( '' === $this->interval ) {
			$this->interval = $this->get_default_interval();
		}
	}
}

/**
 * Object to test the shared schedule lifecycle and reconcile logic.
 */
class SchedulesLifecycle extends PersonioTestCase {
	/**
	 * The hook name.
	 */
	private const HOOK        = 'personio_integration_test_schedule';

	/**
	 * The interval name.
	 */
	private const INT_OPT     = 'personio_integration_test_interval';

	/**
	 * The name of the option to enable the cron.
	 */
	private const ENABLE_OPT  = 'personio_integration_test_enable';

	/**
	 * The name of value A.
	 */
	private const INT_A       = 'personio_integration_daily';

	/**
	 * The name of value B.
	 */
	private const INT_B       = 'personio_integration_weekly';

	/**
	 * The name of the default value.
	 */
	private const INT_DEFAULT = 'personio_integration_daily';

	/**
	 * The class name of the fixture class.
	 */
	private const FIXTURE     = '\\PersonioIntegrationLight\\Tests\\Unit\\Scenarios\\Fixture_Schedule';

	/**
	 * Prepare the environment.
	 *
	 * @return void
	 */
	/**
	 * Prepare the environment.
	 *
	 * @return void
	 */
	public function set_up(): void {
		// allow schedules to be installed.
		add_filter( 'personio_integration_light_setup_is_completed', '__return_true' );

		// register the intervals these tests use.
		add_filter( 'cron_schedules', array( $this, 'register_test_intervals' ) );

		// make the fixture known to the manager.
		add_filter( 'personio_integration_schedules', array( $this, 'add_fixture_schedule' ) );

		// clean state.
		delete_option( self::INT_OPT );
		delete_option( self::ENABLE_OPT );
		wp_unschedule_hook( self::HOOK );
	}

	/**
	 * Clean up.
	 *
	 * @return void
	 */
	public function tear_down(): void {
		wp_unschedule_hook( self::HOOK );
		remove_filter( 'personio_integration_light_setup_is_completed', '__return_true' );
		remove_filter( 'cron_schedules', array( $this, 'register_test_intervals' ) );
		remove_filter( 'personio_integration_schedules', array( $this, 'add_fixture_schedule' ) );
		delete_option( self::INT_OPT );
		delete_option( self::ENABLE_OPT );
	}

	/**
	 * Register the test intervals.
	 *
	 * @param array<string,array<string,mixed>> $schedules The existing schedules.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function register_test_intervals( array $schedules ): array {
		$schedules[ self::INT_A ]       = array( 'interval' => 300, 'display' => 'A' );
		$schedules[ self::INT_B ]       = array( 'interval' => HOUR_IN_SECONDS, 'display' => 'B' );
		$schedules[ self::INT_DEFAULT ] = array( 'interval' => 300, 'display' => 'default' );
		return $schedules;
	}

	/**
	 * Add the fixture schedule to the manager's list.
	 *
	 * @param array<string> $list The schedule class names.
	 *
	 * @return array<string>
	 */
	public function add_fixture_schedule( array $list ): array {
		$list[] = self::FIXTURE;
		return $list;
	}

	/**
	 * Count the scheduled events for a hook (to detect duplicates).
	 *
	 * @param string $hook The hook name.
	 *
	 * @return int
	 */
	private function count_scheduled( string $hook ): int {
		$count = 0;
		foreach ( (array) _get_cron_array() as $events ) {
			if ( is_array( $events ) && isset( $events[ $hook ] ) ) {
				$count += count( $events[ $hook ] );
			}
		}
		return $count;
	}

	// --- Schedules_Base lifecycle (covers every schedule) ---------------------

	/**
	 * install() must create the event with the configured interval.
	 *
	 * @return void
	 */
	public function test_install_creates_schedule(): void {
		update_option( self::INT_OPT, self::INT_A );

		( new Fixture_Schedule() )->install();

		$this->assertNotFalse( wp_next_scheduled( self::HOOK ) );
		$this->assertSame( self::INT_A, wp_get_schedule( self::HOOK ) );
	}

	/**
	 * install() must be idempotent: calling it twice leaves exactly one event.
	 *
	 * @return void
	 */
	public function test_install_is_idempotent(): void {
		update_option( self::INT_OPT, self::INT_A );

		( new Fixture_Schedule() )->install();
		( new Fixture_Schedule() )->install();

		$this->assertSame( 1, $this->count_scheduled( self::HOOK ) );
		$this->assertSame( self::INT_A, wp_get_schedule( self::HOOK ) );
	}

	/**
	 * Changing the interval must reschedule an existing event (Point 1).
	 *
	 * @return void
	 */
	public function test_interval_change_reschedules_existing_event(): void {
		update_option( self::INT_OPT, self::INT_A );
		( new Fixture_Schedule() )->install();
		$this->assertSame( self::INT_A, wp_get_schedule( self::HOOK ) );

		update_option( self::INT_OPT, self::INT_B );
		( new Fixture_Schedule() )->install();

		$this->assertSame( self::INT_B, wp_get_schedule( self::HOOK ) );
		$this->assertSame( 1, $this->count_scheduled( self::HOOK ), 'Rescheduling must not create a duplicate.' );
	}

	/**
	 * An unregistered interval must fall back to the default (Point 2).
	 *
	 * @return void
	 */
	public function test_invalid_interval_falls_back_to_default(): void {
		// precondition: the fallback can only work if the default interval is a
		// registered schedule. If THIS assertion fails, the problem is the test's
		// interval registration; if it passes but the ones below fail, the problem
		// is that install() does not use get_scheduled_interval() (Point 2) for the
		// actual wp_schedule_event() call.
		$this->assertArrayHasKey( self::INT_DEFAULT, wp_get_schedules(), 'Test setup: default interval must be registered.' );

		update_option( self::INT_OPT, 'personio_integration_does_not_exist' );

		( new Fixture_Schedule() )->install();

		$this->assertNotFalse( wp_next_scheduled( self::HOOK ), 'An invalid interval must not prevent scheduling.' );
		$this->assertSame( self::INT_DEFAULT, wp_get_schedule( self::HOOK ) );
	}

	/**
	 * delete() must remove the event.
	 *
	 * @return void
	 */
	public function test_delete_removes_schedule(): void {
		update_option( self::INT_OPT, self::INT_A );
		( new Fixture_Schedule() )->install();
		$this->assertNotFalse( wp_next_scheduled( self::HOOK ) );

		( new Fixture_Schedule() )->delete();
		$this->assertFalse( wp_next_scheduled( self::HOOK ) );
	}

	/**
	 * reset() must delete and re-create the event.
	 *
	 * @return void
	 */
	public function test_reset_reinstalls_schedule(): void {
		update_option( self::INT_OPT, self::INT_A );
		( new Fixture_Schedule() )->install();

		( new Fixture_Schedule() )->reset();

		$this->assertNotFalse( wp_next_scheduled( self::HOOK ) );
		$this->assertSame( self::INT_A, wp_get_schedule( self::HOOK ) );
		$this->assertSame( 1, $this->count_scheduled( self::HOOK ) );
	}

	// --- Schedules::check_events() reconcile ----------------------------------

	/**
	 * check_events() must install a missing but enabled schedule.
	 *
	 * @return void
	 */
	public function test_check_events_installs_missing_enabled(): void {
		update_option( self::ENABLE_OPT, 1 );
		update_option( self::INT_OPT, self::INT_A );
		$this->assertFalse( wp_next_scheduled( self::HOOK ) );

		$manager = Schedules::get_instance();
		$manager->reconcile_events( $manager->get_wp_events() );

		$this->assertNotFalse( wp_next_scheduled( self::HOOK ) );
		$this->assertSame( self::INT_A, wp_get_schedule( self::HOOK ) );
	}

	/**
	 * check_events() must remove a present but disabled schedule.
	 *
	 * @return void
	 */
	public function test_check_events_deletes_disabled_present(): void {
		update_option( self::INT_OPT, self::INT_A );
		( new Fixture_Schedule() )->install();
		$this->assertNotFalse( wp_next_scheduled( self::HOOK ) );

		update_option( self::ENABLE_OPT, 0 );

		$manager = Schedules::get_instance();
		$manager->reconcile_events( $manager->get_wp_events() );

		$this->assertFalse( wp_next_scheduled( self::HOOK ), 'A disabled schedule must be removed by the reconcile.' );
	}

	/**
	 * check_events() must heal an interval drift on an existing enabled schedule.
	 *
	 * @return void
	 */
	public function test_check_events_heals_interval_drift(): void {
		update_option( self::ENABLE_OPT, 1 );
		update_option( self::INT_OPT, self::INT_A );
		( new Fixture_Schedule() )->install();
		$this->assertSame( self::INT_A, wp_get_schedule( self::HOOK ) );

		// change the configured interval without rescheduling directly.
		update_option( self::INT_OPT, self::INT_B );

		$manager = Schedules::get_instance();
		$manager->reconcile_events( $manager->get_wp_events() );

		$this->assertSame( self::INT_B, wp_get_schedule( self::HOOK ), 'The reconcile must apply the changed interval.' );
		$this->assertSame( 1, $this->count_scheduled( self::HOOK ) );
	}
}
