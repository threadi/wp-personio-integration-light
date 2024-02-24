<?php
/**
 * File for handling site health options of this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Admin;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Personio;
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
			),
			array(
				'namespace' => 'personio/v1',
				'route'     => '/url_availability_checks/',
				'callback'  => array( $this, 'url_availability_checks' ),
			),
		);

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
	 * Add rest api endpoints.
	 *
	 * @return void
	 */
	public function add_rest_api(): void {
		foreach ( $this->get_endpoints() as $check ) {
			register_rest_route(
				$check['namespace'],
				$check['route'],
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => $check['callback'],
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
	 * @return array
	 * @noinspection PhpUnused
	 */
	public function url_availability_checks(): array {
		// get Personio-object.
		$personio_obj = new Personio( Helper::get_personio_url() );

		// define default results.
		$result = array(
			'label'       => __( 'Personio Integration URL availability Check', 'personio-integration-light' ),
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

		// -> return error if URL is not set.
		if ( ! Helper::is_personio_url_set() ) {
			$result['status'] = 'recommended';
			/* translators: %1$s will be replaced by the settings-URL, %4$s will be replaced by the URL to login on Personio */
			$result['description'] = sprintf( __( 'No Personio-URL set!<br><strong>Please enter the URL <a href="%1$s">in the plugin-settings</a>.<br>Also check if you have enabled the XML-API in your <a href="%2$s" target="_blank">Personio-account (opens new window)</a> under Settings > Recruiting > Career Page > Activations.</strong>', 'personio-integration-light' ), esc_url( Helper::get_settings_url() ), esc_url( Helper::get_personio_login_url() ) );
		} else {
			// -> should return HTTP-Status 200.
			$response = wp_remote_get(
				$personio_obj->get_xml_url(),
				array(
					'timeout'     => 30,
					'redirection' => 0,
				)
			);
			// get the body with the contents.
			$body = wp_remote_retrieve_body( $response );
			if ( ( is_array( $response ) && ! empty( $response['response']['code'] ) && 200 !== $response['response']['code'] ) || str_starts_with( $body, '<!doctype html>' ) ) {
				$result['status'] = 'recommended';
				/* translators: %1$s and %2$s will be replaced by the Personio-URL, %3$s will be replaced by the settings-URL, %4$s will be replaced by the URL to login on Personio */
				$result['description'] = sprintf( __( 'The Personio-URL <a href="%1$s" target="_blank">%2$s (opens new window)</a> is not available for the import of positions!<br><strong>Please check if you have entered the correct URL <a href="%3$s">in the plugin-settings</a>.<br>Also check if you have enabled the XML-API in your <a href="%4$s" target="_blank">Personio-account (opens new window)</a> under Settings > Recruiting > Career Page > Activations.</strong>', 'personio-integration-light' ), esc_url( $personio_obj->get_url() ), esc_url( $personio_obj->get_url() ), esc_url( Helper::get_settings_url() ), esc_url( Helper::get_personio_login_url() ) );
			}
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
		$statuses['async']['personio_integration_url_availability_check'] = array(
			'label'    => __( 'Personio Integration URL availability check', 'personio-integration-light' ),
			'test'     => rest_url( 'personio/v1/url_availability_checks' ),
			'has_rest' => true,
		);
		return $statuses;
	}
}
