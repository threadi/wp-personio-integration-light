<?php
/**
 * File to handle every schedule in this plugin.
 *
 * @package personio-intregation.
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
	 * List of schedules.
	 *
	 * @var array
	 */
	private array $schedules;

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
		foreach ( $this->get_schedule_object_names() as $obj_name ) {
			$schedule_obj = new $obj_name();
			add_action( $schedule_obj->get_name(), array( $schedule_obj, 'run' ), 10, 0 );
		}

		// action to create all registered schedules.
		add_action( 'admin_action_personioPositionsCreateSchedules', array( $this, 'create_schedules_per_request' ) );
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
	private function get_schedule_object_names(): array {
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
}
