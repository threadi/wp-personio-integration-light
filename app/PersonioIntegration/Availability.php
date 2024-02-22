<?php
/**
 * File to handle availability checks for positions.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent also other direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Settings;

/**
 * Object to handle availability-checks for positions.
 */
class Availability {
	/**
	 * Instance of this object.
	 *
	 * @var ?Availability
	 */
	private static ?Availability $instance = null;

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
	public static function get_instance(): Availability {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Initialize this plugin.
	 *
	 * @return void
	 */
	public function init(): void {
		// use our own hooks.
		add_filter( 'personio_integration_settings', array( $this, 'add_settings' ) );
		add_filter( 'personio_integration_schedules', array( $this, 'add_schedule' ) );

		// bail if settings is not enabled.
		if( 1 !== absint( Settings::get_instance()->get_setting( 'personioIntegrationEnableAvailabilityCheck' ) ) ) {
			return;
		}

		// extend the position table.
		add_filter( 'manage_' . PersonioPosition::get_instance()->get_name() . '_posts_columns', array( $this, 'add_column' ) );
		add_action( 'manage_' . PersonioPosition::get_instance()->get_name() . '_posts_custom_column', array( $this, 'add_column_content' ), 10, 2 );
	}

	/**
	 * Add settings for this extension.
	 *
	 * @param array $settings List of settings.
	 *
	 * @return array
	 */
	public function add_settings( array $settings ): array {
		$settings['settings_section_import']['fields'] = Helper::add_array_in_array_on_position( $settings['settings_section_import']['fields'], 3, array( 'personioIntegrationEnableAvailabilityCheck' => array(
					'label' => __('Enable availability checks', 'wp-personio-integration'),
					'field' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
					'description' => __('If enabled the plugin will daily check the availability of position pages on Personio. You will be warned if a position is not available.', 'wp-personio-integration'),
					'register_attributes' => array(
						'type' => 'integer',
						'default'             => 1,
					),
					'source' => WP_PERSONIO_INTEGRATION_PLUGIN,
					'callback' => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsSavings\Availability', 'save' )
				)
			)
		);

		// return resulting list of settings.
		return $settings;
	}

	/**
	 * Run the checks for availability of positions.
	 *
	 * @return void
	 */
	public function run(): void {
		// bail if settings is not enabled.
		if( 1 !== absint( Settings::get_instance()->get_setting( 'personioIntegrationEnableAvailabilityCheck' ) ) ) {
			return;
		}

		// get log object
		$log = new Log();

		// loop through the positions and check each.
		foreach ( Positions::get_instance()->get_positions() as $position_obj ) {
			if( $position_obj instanceof Position ) {
				// define settings for second request to get the contents.
				$args     = array(
					'timeout'     => get_option( 'personioIntegrationUrlTimeout' ),
					'httpversion' => '1.1',
					'redirection' => 0,
				);
				$response = wp_remote_head( $position_obj->get_application_url(), $args );

				if ( is_wp_error( $response ) ) {
					// log possible error.
					$log->add_log( 'Error on request to get position availability: ' . $response->get_error_message(), 'error' );
				} elseif ( empty( $response ) ) {
					// log im result is empty.
					$log->add_log( 'Get empty response for position availability.', 'error' );
				} else {
					// get the http-status to check if call results in acceptable results.
					$http_status = $response['http_response']->get_status();

					// get extension to save the availability.
					$availability_extension = $position_obj->get_extension( 'PersonioIntegrationLight\PersonioIntegration\Extensions\Availability' );

					// bail if extension could not be loaded.
					if( ! ( $availability_extension instanceof Extensions\Availability ) ) {
						continue;
					}

					// if http-status is not 200 or 301, mark the position as not available.
					$availability_extension->set_availability( in_array( $http_status, array( 200, 301), true ) );
				}
			}
		}
	}

	/**
	 * Add our own schedule to the list.
	 *
	 * @param array $list_of_schedules List of schedules.
	 *
	 * @return array
	 */
	public function add_schedule( array $list_of_schedules ): array {
		// add the schedule-objekt.
		$list_of_schedules[] = 'PersonioIntegrationLight\Plugin\Schedules\Availability';

		// return resulting list.
		return $list_of_schedules;
	}

	/**
	 * Add columns to position-table in backend.
	 *
	 * @param array $columns List of columns.
	 *
	 * @return array
	 */
	public function add_column( array $columns ): array {
		return Helper::add_array_in_array_on_position( $columns, 2, array( 'personio_integration_position_availability' => __( 'Available on Personio', 'personio-integration-light' ) ) );
	}

	/**
	 * Add content to the column in the position-table in backend.
	 *
	 * @param string $column Name of the column.
	 * @param int    $post_id The ID of the WP_Post-object.
	 *
	 * @return void
	 */
	public function add_column_content( string $column, int $post_id ): void {
		// get position as object.
		$position_obj = Positions::get_instance()->get_position( $post_id );

		// show ID-column.
		if ( 'personio_integration_position_availability' === $column ) {
			if( $position_obj->get_extension( 'PersonioIntegrationLight\PersonioIntegration\Extensions\Availability' )->get_availability() ) {
				echo '<span class="dashicons dashicons-yes"></span>';
			}
			else {
				echo '<span class="dashicons dashicons-no"></span><span class="pro-marker">'.__( 'Use your own application form with Pro', 'personio-integration-light' ).'</span>';
			}
		}
	}
}
