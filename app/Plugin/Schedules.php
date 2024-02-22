<?php
/**
 * File to handle every schedule in this plugin.
 *
 * @package personio-intregation-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent also other direct access.
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
		// loop through our own events.
		foreach ( $this->get_events() as $event ) {
			// get the schedule object.
			$schedule_obj = $this->get_schedule_object_by_name( $event['name'] );
			if( $schedule_obj instanceof Schedules_Base ) {
				// set attributes in object, if available.
				if( ! empty($event['settings'][array_key_first( $event['settings'])]['args']) ) {
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
		$our_events = array();
		foreach( _get_cron_array() as $events ) {
			foreach( $events as $event_name => $event_settings ) {
				if( str_contains( $event_name, 'personio_integration' ) ) {
					$our_events[] = array( 'name' => $event_name, 'settings' => $event_settings );
				}
			}
		}
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
			if( $schedule_obj instanceof Schedules_Base ) {
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
			if( $schedule_obj instanceof Schedules_Base ) {
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
			'PersonioIntegrationLight\Plugin\Schedules\Import'
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
		foreach( $this->get_schedule_object_names() as $object_name ) {
			$obj = new $object_name();
			if( $obj instanceof Schedules_Base && $name === $obj->get_name() ) {
				return $obj;
			}
		}
		return false;
	}
}
