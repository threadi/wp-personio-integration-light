<?php
/**
 * File to handle every schedule in this plugin.
 *
 * @package personio-intregation-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;

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
		if ( is_admin() ) {
			add_filter( 'personio_integration_schedule_our_events', array( $this, 'check_events' ) );
		}
		add_filter( 'personio_integration_settings', array( $this, 'add_settings' ) );

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
		add_filter( 'schedule_event', array( $this, 'add_schedule_to_list' ) );
		add_action( 'shutdown', array( $this, 'check_events_on_shutdown' ) );
	}

	/**
	 * Add settings for this extension.
	 *
	 * @param array $settings List of settings.
	 *
	 * @return array
	 */
	public function add_settings( array $settings ): array {
		if ( empty( $settings['settings_section_advanced']['fields'] ) ) {
			return $settings;
		}
		$settings['settings_section_advanced']['fields'] = Helper::add_array_in_array_on_position(
			$settings['settings_section_advanced']['fields'],
			8,
			array(
				'personioIntegrationEnableCronCheckInFrontend' => array(
					'label'               => __( 'Check for schedules in frontend', 'personio-integration-light' ),
					'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
					'description'         => __( 'If enabled the plugin will check our own schedules on each request in frontend. This could be slow the performance of your website.', 'personio-integration-light' ),
					'register_attributes' => array(
						'type'    => 'integer',
						'default' => 0,
					),
					'source'              => WP_PERSONIO_INTEGRATION_PLUGIN,
				),
			)
		);

		// return resulting list of settings.
		return $settings;
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
	 * @param array $our_events List of our own events.
	 *
	 * @return array
	 */
	public function check_events( array $our_events ): array {
		// bail if check should be disabled.
		$false = false;
		/**
		 * Disable the additional cron check.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 * @param bool $false True if check should be disabled.
		 *
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( apply_filters( 'personio_integration_disable_cron_check', $false ) ) {
			return $our_events;
		}

		// bail if plugin activation is running.
		if ( defined( 'PERSONIO_INTEGRATION_ACTIVATION_RUNNING' ) ) {
			return $our_events;
		}

		// check the schedule objects if they are set.
		foreach ( $this->get_schedule_object_names() as $object_name ) {
			$obj = new $object_name();
			if ( $obj instanceof Schedules_Base ) {
				// install if schedule is enabled and not in list of our schedules.
				if ( $obj->is_enabled() && ! isset( $our_events[ $obj->get_name() ] ) ) {
					// reinstall the missing event.
					$obj->install();

					// log this event if debug-mode is enabled.
					if ( 1 === absint( get_option( 'personioIntegration_debug' ) ) ) {
						$log = new Log();
						/* translators: %1$s will be replaced by the event name. */
						$log->add_log( sprintf( __( 'Missing cron event <i>%1$s</i> automatically re-installed.', 'personio-integration-light' ), esc_html( $obj->get_name() ) ), 'success', $obj->get_log_category() );
					}

					// re-run the check for WP-cron-events.
					$our_events = $this->get_wp_events();
				}

				// delete if schedule is in list of our events and not enabled.
				if ( ! $obj->is_enabled() && isset( $our_events[ $obj->get_name() ] ) ) {
					$obj->delete();

					// log this event if debug-mode is enabled.
					if ( 1 === absint( get_option( 'personioIntegration_debug' ) ) ) {
						$log = new Log();
						/* translators: %1$s will be replaced by the event name. */
						$log->add_log( sprintf( __( 'Not enabled cron event <i>%1$s</i> automatically removed.', 'personio-integration-light' ), esc_html( $obj->get_name() ) ), 'success', $obj->get_log_category() );
					}

					// re-run the check for WP-cron-events.
					$our_events = $this->get_wp_events();
				}
			}
		}

		// return resulting list.
		return $our_events;
	}

	/**
	 * Delete all our registered schedules.
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
		exit;
	}

	/**
	 * Return list of all schedule-object-names.
	 *
	 * @return array
	 */
	public function get_schedule_object_names(): array {
		// list of schedules: free version supports only one import-schedule.
		$list_of_schedules = array(
			'\PersonioIntegrationLight\Plugin\Schedules\Import',
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
					$our_events[ $event_name ] = array(
						'name'     => $event_name,
						'settings' => $event_settings,
					);
				}
			}
		}

		// return resulting list.
		return $our_events;
	}

	/**
	 * Run check for cronjobs in frontend, if enabled.
	 *
	 * @return void
	 */
	public function check_events_on_shutdown(): void {
		// bail if check is disabled.
		if ( 1 !== absint( get_option( 'personioIntegrationEnableCronCheckInFrontend' ) ) ) {
			return;
		}

		// run the check.
		$this->check_events( $this->get_events() );
	}

	/**
	 * Add schedule to our list of schedules.
	 *
	 * @param object|bool $event The event properties.
	 *
	 * @return object|bool
	 */
	public function add_schedule_to_list( object|bool $event ): object|bool {
		// bail if event is not an object.
		if ( ! is_object( $event ) ) {
			return $event;
		}

		// get our object.
		$schedule_obj = $this->get_schedule_object_by_name( $event->hook );

		// bail if this is not an event of our plugin.
		if ( ! $schedule_obj ) {
			return $event;
		}

		// get the actual list.
		$list = get_option( 'personio_integration_schedules' );
		if ( ! is_array( $list ) ) {
			$list = array();
		}
		$list[ $schedule_obj->get_name() ] = $schedule_obj->get_args();
		update_option( 'personio_integration_schedules', $list );

		// return the event object.
		return $event;
	}
}
