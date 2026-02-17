<?php
/**
 * File for handling site health options of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Plugin\Schedules\Import;
use WP_REST_Server;

/**
 * Helper-function for Site Health options of this plugin.
 */
class Site_Health {
	/**
	 * Instance of this object.
	 *
	 * @var ?Site_Health
	 */
	private static ?Site_Health $instance = null;

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
	public static function get_instance(): Site_Health {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize the site health support.
	 *
	 * @return void
	 */
	public function init(): void {
		// add debug info.
		add_filter( 'debug_information', array( $this, 'add_debug_info' ) );

		// register REST API.
		add_action( 'rest_api_init', array( $this, 'add_rest_api' ) );

		// add checks.
		add_filter( 'site_status_tests', array( $this, 'add_checks' ) );
	}

	/**
	 * Return list of endpoints the site health should use for our plugin.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private function get_endpoints(): array {
		$list = array(
			array(
				'namespace' => 'personio/v1',
				'route'     => '/import_cron_checks/',
				'callback'  => array( $this, 'import_cron_checks' ),
				'args'      => array(),
			),
		);

		/**
		 * Filter the endpoints for Site Health this plugin is using.
		 *
		 * Hint: these are just arrays which define the endpoints.
		 *
		 * @param array<int,array<string,mixed>> $list List of endpoints.
		 */
		return apply_filters( 'personio_integration_site_health_endpoints', $list );
	}

	/**
	 * Register each rest api endpoints for site health checks.
	 *
	 * @return void
	 */
	public function add_rest_api(): void {
		foreach ( $this->get_endpoints() as $check ) {
			// bail if no callback is set.
			if ( empty( $check['callback'] ) ) {
				continue;
			}

			// check if args are set.
			if ( ! isset( $check['args'] ) ) {
				$check['args'] = array();
			}

			// register the route.
			register_rest_route(
				$check['namespace'],
				$check['route'],
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => $check['callback'],
					'args'                => $check['args'],
					'permission_callback' => function () {
						return current_user_can( 'manage_options' );
					},
				)
			);
		}
	}

	/**
	 * Return result after checking cronjob-states.
	 *
	 * @return array<string,mixed>
	 * @noinspection PhpUnused
	 */
	public function import_cron_checks(): array {
		// define default results.
		$result = array(
			'label'       => __( 'Personio Integration Import Cron Check', 'personio-integration-light' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( 'Personio Integration Light', 'personio-integration-light' ),
				'color' => 'gray',
			),
			'description' => __( 'Running cronjobs help to import new positions from Personio automatically.<br><strong>All ok with the cronjob!</strong>', 'personio-integration-light' ),
			'actions'     => '',
			'test'        => 'personio_integration_rest_api_import_cron_checks',
		);

		// get scheduled event.
		$schedule_obj    = new Import();
		$scheduled_event = $schedule_obj->get_event();

		// event does not exist => show error.
		if ( false === $scheduled_event ) {
			$url                   = add_query_arg(
				array(
					'action' => 'personioPositionsCreateSchedules',
					'nonce'  => wp_create_nonce( 'wp-personio-integration-create-schedules' ),
				),
				get_admin_url() . 'admin.php'
			);
			$result['status']      = 'recommended';
			$result['description'] = __( 'Cronjob to import new Positions from Personio does not exist!', 'personio-integration-light' );
			$result['actions']     = '<p><a href="' . $url . '" class="button button-primary">' . __( 'Recreate the schedules', 'personio-integration-light' ) . '</a></p>';

			// return this result.
			return $result;
		}

		// if the scheduled event exists, check if the next run is in the past.
		if ( $scheduled_event->timestamp < time() ) { // @phpstan-ignore property.notFound
			$result['status'] = 'recommended';
			/* translators: %1$s will be replaced by the date of the planned next schedule run (which is in the past) */
			$result['description'] = sprintf( __( 'Cronjob to import new Positions from Personio should have been run at %1$s, but was not executed!<br><strong>Please check the cron-system of your WordPress-installation.</strong>', 'personio-integration-light' ), Helper::get_format_date_time( gmdate( 'Y-m-d H:i:s', $scheduled_event->timestamp ) ) );

			// return this result.
			return $result;
		}

		// return result.
		return $result;
	}

	/**
	 * Add custom status-check for running cronjobs of our own plugin.
	 * Only if Personio-URL is set.
	 *
	 * @param array<string,mixed> $statuses List of tests to run.
	 * @return array<string,mixed>
	 */
	public function add_checks( array $statuses ): array {
		if ( Helper::is_personio_url_set() ) {
			$statuses['async']['personio_integration_import_cron_checks'] = array(
				'label'    => __( 'Personio Integration Import Cron Check', 'personio-integration-light' ),
				'test'     => rest_url( 'personio/v1/import_cron_checks' ),
				'has_rest' => true,
			);
		}

		// return the statuses.
		return $statuses;
	}

	/**
	 * Add our own debug information to site health.
	 *
	 * @param array<string,mixed> $debug_information List of debug information for the actual project.
	 *
	 * @return array<string,mixed>
	 */
	public function add_debug_info( array $debug_information ): array {
		$debug_information['personio-integration-light'] = array(
			'label'  => Helper::get_plugin_name(),
			'fields' => array(),
		);

		// loop through all settings and add them as fields if their export is allowed.
		foreach ( Settings::get_instance()->get_settings() as $setting ) {
			// bail if the source of this setting is not the light plugin.
			if ( is_string( $setting->get_custom_var( 'source' ) ) ) {
				continue;
			}

			// get the value.
			$value = $setting->get_value();

			// mask value if it is an email.
			if ( is_string( $value ) && is_email( $value ) ) {
				$value = 'masked';
			}

			// create the entry.
			$entry = array(
				'label'   => $setting->get_name(),
				'value'   => $value,
				'private' => $setting->is_export_prevented(),
			);

			// add it to the list.
			$debug_information['personio-integration-light']['fields'][ $setting->get_name() ] = $entry;
		}

		// return the resulting list of debug information.
		return $debug_information;
	}
}
