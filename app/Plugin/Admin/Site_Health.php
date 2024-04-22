<?php
/**
 * File for handling site health options of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Imports;
use PersonioIntegrationLight\PersonioIntegration\Personio;
use PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationUrl;
use PersonioIntegrationLight\Plugin\Schedules\Import;
use WP_REST_Request;
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
	 * Constructor for Init-Handler.
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
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Initialize the site health support.
	 *
	 * @return void
	 */
	public function init(): void {
		// register REST API.
		add_action( 'rest_api_init', array( $this, 'add_rest_api' ) );

		// add checks.
		add_filter( 'site_status_tests', array( $this, 'add_checks' ) );
	}

	/**
	 * Get list of endpoints the site health should use for our plugin.
	 *
	 * @return array
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

		// add one check for each Personio URL.
		foreach ( Imports::get_instance()->get_personio_urls() as $personio_url ) {
			$list[] = array(
				'namespace' => 'personio/v1',
				'route'     => '/url_availability_checks_' . md5( $personio_url ) . '/',
				'callback'  => array( $this, 'url_availability_checks' ),
				'args'      => array( array( 'personio_url' => $personio_url ) ),
			);
		}

		/**
		 * Filter the endpoints for Site Health this plugin is using.
		 *
		 * Hint: these are just arrays which define the endpoints.
		 *
		 * @param array $list List of endpoints.
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
	 * @return array
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
			/* translators: %1$s will be replaced by the URL to recreate the schedule */
			$result['actions'] = sprintf( '<p><a href="%1$s" class="button button-primary">Recreate the schedules</a></p>', $url );

			// return this result.
			return $result;
		}

		// if scheduled event exist, check if next run is in the past.
		if ( $scheduled_event->timestamp < time() ) {
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
	 * Check the Personio-URL availability.
	 *
	 * @param WP_REST_Request $request The request-object.
	 *
	 * @return array
	 */
	public function url_availability_checks( WP_REST_Request $request ): array {
		// get attributes to detect the requested Personio URL.
		$args = $request->get_attributes();

		// bail with error if no settings found.
		if ( empty( $args ) || empty( $args['args'] ) || empty( $args['args'][0]['personio_url'] ) ) {
			return array(
				'label'       => __( 'Personio URL availability check', 'personio-integration-light' ),
				'status'      => 'recommended',
				'badge'       => array(
					'label' => __( 'Personio Integration Light', 'personio-integration-light' ),
					'color' => 'gray',
				),
				'description' => __( 'Missing Personio URL for check. Something is wrong with your plugin configuration.', 'personio-integration-light' ),
				'action'      => '',
				'test'        => 'personio_integration_rest_api_url_availability_check',
			);
		}

		// get Personio-object for requested URL.
		$personio_obj = new Personio( $args['args'][0]['personio_url'] );

		// define default results.
		$result = array(
			'label'       => __( 'Personio URL availability Check', 'personio-integration-light' ),
			'status'      => 'good',
			'badge'       => array(
				'label' => __( 'Personio Integration Light', 'personio-integration-light' ),
				'color' => 'gray',
			),
			/* translators: %1$s and %2$s will be replaced by the Personio-URL */
			'description' => sprintf( __( 'The Personio-URL <a href="%1$s" target="_blank">%2$s (opens new window)</a> is necessary to import new positions.<br><strong>All ok with the URL!</strong>', 'personio-integration-light' ), esc_url( $personio_obj->get_url() ), esc_url( $personio_obj->get_url() ) ),
			'actions'     => '',
			'test'        => 'personio_integration_rest_api_url_availability_check',
		);

		// request the HTTP-header of XML-API for the given Personio URL.
		if ( ! PersonioIntegrationUrl::check_url( $personio_obj->get_url() ) ) {
			$result['status'] = 'recommended';
			/* translators: %1$s and %2$s will be replaced by the Personio-URL, %3$s will be replaced by the settings-URL, %4$s will be replaced by the URL to login on Personio */
			$result['description'] = sprintf( __( 'The Personio-URL <a href="%1$s" target="_blank">%2$s (opens new window)</a> is not available for the import of positions!<br><strong>Please check if you have entered the correct URL <a href="%3$s">in the plugin-settings</a>.<br>Also check if you have enabled the XML-API in your <a href="%4$s" target="_blank">Personio-account (opens new window)</a> under Settings > Recruiting > Career Page > Activations.</strong>', 'personio-integration-light' ), esc_url( $personio_obj->get_url() ), esc_url( $personio_obj->get_url() ), esc_url( Helper::get_settings_url() ), esc_url( Helper::get_personio_login_url() ) );
		}

		// return result.
		return $result;
	}

	/**
	 * Add custom status-check for running cronjobs of our own plugin.
	 * Only if Personio-URL is set.
	 *
	 * @param array $statuses List of tests to run.
	 * @return array
	 */
	public function add_checks( array $statuses ): array {
		if ( Helper::is_personio_url_set() ) {
			$statuses['async']['personio_integration_import_cron_checks'] = array(
				'label'    => __( 'Personio Integration Import Cron Check', 'personio-integration-light' ),
				'test'     => rest_url( 'personio/v1/import_cron_checks' ),
				'has_rest' => true,
			);
		}

		// one check for each Personio URL.
		foreach ( Imports::get_instance()->get_personio_urls() as $personio_url ) {
			$statuses['async'][ 'personio_integration_url_availability_check_' . md5( $personio_url ) ] = array(
				'label'    => __( 'Personio Integration URL availability check', 'personio-integration-light' ),
				'test'     => rest_url( 'personio/v1/url_availability_checks_' . md5( $personio_url ) ),
				'has_rest' => true,
			);
		}
		return $statuses;
	}
}
