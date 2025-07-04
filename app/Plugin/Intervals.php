<?php
/**
 * File with handler for our own intervals.
 *
 * Hint: we use our own intervals to prevent intervals of other plugins from being used.
 * This would be an unnecessary dependency that leads to missing execution of our own schedules.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

/**
 * Object for our own intervals.
 */
class Intervals {
	/**
	 * Instance of this object.
	 *
	 * @var ?Intervals
	 */
	private static ?Intervals $instance = null;

	/**
	 * Constructor for this object.
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Intervals {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize all schedules of this plugin.
	 *
	 * @return void
	 */
	public function init(): void {
		add_filter( 'cron_schedules', array( $this, 'add_intervals' ) );
	}

	/**
	 * Return list of our own intervals.
	 *
	 * @return array
	 */
	private function get_intervals(): array {
		$list = array(
			'\PersonioIntegrationLight\Plugin\Intervals\Daily',
			'\PersonioIntegrationLight\Plugin\Intervals\Weekly',
		);

		return apply_filters( 'personio_integration_light_intervals', $list );
	}

	/**
	 * Return the list of available intervals as objects.
	 *
	 * @return array<int,Interval_Base>
	 */
	public function get_intervals_as_objects(): array {
		// define the list for the objects.
		$list = array();

		// loop through our compatibility-checks.
		foreach ( $this->get_intervals() as $interval_class_name ) {
			// get the class name.
			$class_name = $interval_class_name . '::get_instance';

			// bail if it is not callable.
			if ( ! is_callable( $class_name ) ) {
				continue;
			}

			// get the object.
			$obj = $class_name();

			// bail if object is not a Compatibilities_Base.
			if ( ! $obj instanceof Interval_Base ) {
				continue;
			}

			// add to the list.
			$list[] = $obj;
		}

		// return the resulting list.
		return $list;
	}

	/**
	 * Return the list of intervals for settings.
	 *
	 * Sorted by its time.
	 *
	 * @return array
	 */
	public function get_intervals_for_settings(): array {
		// define the list for the entries.
		$list = array();

		foreach( $this->get_intervals_as_objects() as $obj ) {
			$list[ $this->get_prefix() . $obj->get_name() ] = $obj->get_title();
		}

		// return the resulting list.
		return $list;
	}

	/**
	 * Add our own intervals to the WordPress-list of intervals.
	 *
	 * @param array<string,array<string,mixed>> $intervals List of intervals.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	public function add_intervals( array $intervals ): array {
		foreach( $this->get_intervals_as_objects() as $obj ) {
			$intervals[ $this->get_prefix() . $obj->get_name() ] = array(
				'interval' => $obj->get_time(),
				'display' => $obj->get_title()
			);
		}
		return $intervals;
	}

	/**
	 * Return the prefix we use for each of our own interval names.
	 *
	 * @return string
	 */
	public function get_prefix(): string {
		return 'personio_integration_';
	}
}
