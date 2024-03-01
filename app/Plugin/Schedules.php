<?php
/**
 * File to handle every schedule in this plugin.
 *
 * @package personio-intregation-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent also other direct access.
use PersonioIntegrationLight\Log;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The object which handles schedules.
 */
class Schedules {
	/**
	 * Instance of this object.
	 *
	 * @var ?Schedules
	 */
	private static ?Schedules $instance = null;

	/**
	 * Constructor for Schedules-Handler.
	 */
	private function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() { }

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Schedules {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Initialize all schedules of this plugin.
	 *
	 * @return void
	 */
	public function init(): void {
		// use our own hooks.
		add_filter( 'personio_integration_schedule_our_events', array( $this, 'check_events' ) );

		// loop through our own events.
		foreach ( $this->get_events() as $event ) {
			// get the schedule object.
			$schedule_obj = $this->get_schedule_object_by_name( $event['name'] );
			if ( $schedule_obj instanceof Schedules_Base ) {
				// set attributes in object, if available.
				if ( ! empty( $event['settings'][ array_key_first( $event['settings'] ) ]['args'] ) ) {
					$schedule_obj->set_args( $event['settings'][ array_key_first( $event['settings'] ) ]['args'] );
				}

				// define action hook to run the schedule.
				add_action( $schedule_obj->get_name(), array( $schedule_obj, 'run' ), 10, 0 );
			}
		}

		// action to create all registered schedules.
		add_action( 'admin_action_personioPositionsCreateSchedules', array( $this, 'create_schedules_per_request' ) );
	}

	/**
	 * Get our own active events from WP-list.
	 *
	 * @return array
	 */
	private function get_events(): array {
		// get our own events from events list in WordPress.
		$our_events = $this->get_wp_events();

		/**
		 * Filter the list of our own events,
		 * e.g. to check if all which are enabled in setting are active.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array $our_events List of our own events in WP-cron.
		 */
		return apply_filters( 'personio_integration_schedule_our_events', $our_events );
	}

	/**
	 * Check the available events with the ones which should be active.
	 *
	 * Re-installs missing events. Log this event.
	 *
	 * Does only run in wp-admin, not frontend.
	 *
	 * @param array $our_events
	 *
	 * @return array
	 */
	public function check_events( array $our_events ): array {
		if( is_admin() ) {
			foreach( $this->get_schedule_object_names() as $object_name ) {
				$obj = new $object_name();
				if( $obj instanceof Schedules_Base ) {
					if ( $obj->is_enabled() && ! isset( $our_events[$obj->get_name()] ) ) {

						// reinstall the missing event.
						$obj->install();

						// log this event.
						$log = new Log();
						$log->add_log( sprintf( __( 'Missing cron event %1$s automatically re-installed.', 'personio-integration-light' ), esc_html( $obj->get_name() ) ), 'success' );

						// re-run the check for WP-cron-events.
						$our_events = $this->get_wp_events();
					}
				}
			}
		}

		// return resulting list.
		return $our_events;
	}

	/**
	 * Delete all registered schedules.
	 *
	 * @return void
	 */
	public function delete_all(): void {
		foreach ( $this->get_schedule_object_names() as $obj_name ) {
			$schedule_obj = new $obj_name();
			if ( $schedule_obj instanceof Schedules_Base ) {
				$schedule_obj->delete();
			}
		}
	}

	/**
	 * Create our schedules per request.
	 *
	 * @return void
	 */
	public function create_schedules(): void {
		// install the schedules if they do not exist atm.
		foreach ( $this->get_schedule_object_names() as $obj_name ) {
			$schedule_obj = new $obj_name();
			if ( $schedule_obj instanceof Schedules_Base ) {
				$schedule_obj->install();
			}
		}
	}

	/**
	 * Create our schedules per request.
	 *
	 * @return void
	 */
	public function create_schedules_per_request(): void {
		check_ajax_referer( 'wp-personio-integration-create-schedules', 'nonce' );

		// create schedules.
		$this->create_schedules();

		// redirect user.
		wp_safe_redirect( isset( $_SERVER['HTTP_REFERER'] ) ? wp_unslash( $_SERVER['HTTP_REFERER'] ) : '' );
	}

	/**
	 * Return list of all schedule-object-names.
	 *
	 * @return array
	 */
	public function get_schedule_object_names(): array {
		// list of schedules: free version supports only one import-schedule.
		$list_of_schedules = array(
			'PersonioIntegrationLight\Plugin\Schedules\Import',
		);

		/**
		 * Add custom schedule-objects to use.
		 *
		 * This must be objects based on PersonioIntegrationLight\Plugin\Schedules_Base.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array $list_of_schedules List of additional schedules.
		 */
		return apply_filters( 'personio_integration_schedules', $list_of_schedules );
	}

	/**
	 * Get schedule object by its name.
	 *
	 * @param string $name The name of the object.
	 *
	 * @return false|Schedules_Base
	 */
	private function get_schedule_object_by_name( string $name ): false|Schedules_Base {
		foreach ( $this->get_schedule_object_names() as $object_name ) {
			$obj = new $object_name();
			if ( $obj instanceof Schedules_Base && $name === $obj->get_name() ) {
				return $obj;
			}
		}
		return false;
	}

	/**
	 * Get our own events from WP-cron-event-list.
	 *
	 * @return array
	 */
	private function get_wp_events(): array {
		$our_events = array();
		foreach ( _get_cron_array() as $events ) {
			foreach ( $events as $event_name => $event_settings ) {
				if ( str_contains( $event_name, 'personio_integration' ) ) {
					$our_events[$event_name] = array(
						'name'     => $event_name,
						'settings' => $event_settings,
					);
				}
			}
		}

		// return resulting list.
		return $our_events;
	}
}