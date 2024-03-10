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
class Availability extends Extensions_Base {
	/**
	 * The internal name of this extension.
	 *
	 * @var string
	 */
	protected string $name = 'availability';

	/**
	 * Name if the setting field which defines its state.
	 *
	 * @var string
	 */
	protected string $setting_field = 'personioIntegrationEnableAvailabilityCheck';

	/**
	 * Initialize this plugin.
	 *
	 * @return void
	 */
	public function init(): void {
		// use our own hooks.
		add_filter( 'personio_integration_settings', array( $this, 'add_settings' ) );
		add_filter( 'personio_integration_schedules', array( $this, 'add_schedule' ) );
		add_action( 'personio_integration_import_ended', array( $this, 'run' ) );
		add_filter( 'personio_integration_extensions_table_extension', array( $this, 'add_extension' ) );

		// bail if settings is not enabled.
		if ( $this->is_enabled() ) {
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
		$settings['settings_section_import']['fields'] = Helper::add_array_in_array_on_position(
			$settings['settings_section_import']['fields'],
			3,
			array(
				$this->get_settings_field_name() => array(
					'label'               => __( 'Enable availability checks', 'wp-personio-integration' ),
					'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
					'description'         => __( 'If enabled the plugin will daily check the availability of position pages on Personio. You will be warned if a position is not available.', 'wp-personio-integration' ),
					'register_attributes' => array(
						'type'    => 'integer',
						'default' => 1,
					),
					'source'              => WP_PERSONIO_INTEGRATION_PLUGIN,
					'callback'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsSavings\Availability', 'save' ),
				),
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
		if ( 1 !== absint( Settings::get_instance()->get_setting( 'personioIntegrationEnableAvailabilityCheck' ) ) ) {
			return;
		}

		// get list of positions.
		$positions      = Positions::get_instance()->get_positions();
		$position_count = count( $positions );

		/**
		 * Add max count on third party components (like Setup).
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param int $position_count
		 */
		do_action( 'personio_integration_import_max_count', $position_count );

		// set setup-label.
		update_option( 'wp_easy_setup_pi_step_label', __( 'Setup is checking the availability of each position.', 'personio-integration-light' ) );

		// set import-label.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_STATUS, __( 'We check the availability of each position.', 'personio-integration-light' ) );

		// get log object.
		$log = new Log();

		// show cli hint.
		$progress = Helper::is_cli() ? \WP_CLI\Utils\make_progress_bar('Run availability checks', count($positions)) : false;

		// loop through the positions and check each.
		foreach ( $positions as $position_obj ) {
			if ( $position_obj instanceof Position ) {
				// define settings for second request to get the contents.
				$args     = array(
					'timeout'     => get_option( 'personioIntegrationUrlTimeout' ),
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
					if ( ! ( $availability_extension instanceof Extensions\Availability ) ) {
						continue;
					}

					// if http-status is not 200, mark the position as not available.
					$availability_extension->set_availability( 200 === $http_status );
				}

				$count = 1;
				/**
				 * Add actual count on third party components (like Setup).
				 *
				 * @since 3.0.0 Available since 3.0.0.
				 *
				 * @param int $count The value to add.
				 */
				do_action( 'personio_integration_import_count', $count );
			}

			// show progress.
			$progress ? $progress->tick() : false;
		}

		// show progress.
		$progress ? $progress->finish() : false;
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
		return Helper::add_array_in_array_on_position( $columns, 2, array( 'personio_integration_position_availability' => __( 'Personio-page available', 'personio-integration-light' ) ) );
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
			if ( $position_obj->get_extension( 'PersonioIntegrationLight\PersonioIntegration\Extensions\Availability' )->get_availability() ) {
				$html = '<span class="dashicons dashicons-yes"></span>';
				/**
				 * Filter the availability "yes"-output.
				 *
				 * @since 3.0.0 Available since 3.0.0.
				 *
				 * @param string $html The output.
				 */
				echo wp_kses_post( apply_filters( 'personio_integration_light_position_availability_yes', $html ) );
			} else {
				// create dialog.
				$dialog = array(
					'className' => 'personio-integration-applications-hint',
					'title'     => __( 'Personio page not available', 'personio-integration-light' ),
					'texts'     => array(
						'<p>' . __( 'If the Personio page for this position is not available, no one will be able to apply for it directly.', 'personio-integration-light' ) . '</p>',
						/* translators: %1$s will be replaced by the link to the Personio account */
						'<p>' . sprintf( __( 'Check in your <a href="%1$s" target="_blank">Personio account (opens new window)</a> why the page is not available.<br>You may have only deactivated the career page.', 'personio-integration-light' ), esc_url( Helper::get_personio_login_url() ) ) . '</p>',
						'<p>' . __( 'With <strong>Personio Integration Pro</strong>, you can also enter applications directly in the WordPress website and transfer them to Personio.<br>The career page of a job in Personio does not need to be activated for this.', 'personio-integration-light' ) . '</p>',
					),
					'buttons'   => array(
						array(
							'action'  => 'window.open( "' . esc_url( Helper::get_pro_url() ) . '" );closeDialog();',
							'variant' => 'primary',
							'text'    => __( 'Get more info about Pro', 'personio-integration-light' ),
						),
						array(
							'action'  => 'closeDialog();',
							'variant' => 'secondary',
							'text'    => __( 'OK', 'personio-integration-light' ),
						),
					),
				);

				// show icon with helper.
				$html = '<span class="dashicons dashicons-no"></span><a class="pro-marker wp-easy-dialog" data-dialog="' . esc_attr( wp_json_encode( $dialog ) ) . '"><span class="dashicons dashicons-editor-help"></span></a>';
				/**
				 * Filter the availability "no"-output.
				 *
				 * @since 3.0.0 Available since 3.0.0.
				 *
				 * @param string $html The output.
				 */
				echo wp_kses_post( apply_filters( 'personio_integration_light_position_availability_no', $html ) );
			}
		}
	}

	/**
	 * Add this extension to the list of extensions.
	 *
	 * @param array $extensions List of extensions.
	 *
	 * @return array
	 */
	public function add_extension( array $extensions ): array {
		$extensions[] = array(
			'state' => false,
			'name' => 'Availability',
			'description' => __( 'Check each position for availability on your Personio career page.', 'personio-integration-light' )
		);

		return $extensions;
	}

	/**
	 * Return label of this extension.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Availability', 'personio-integration-light' );
	}

	/**
	 * Return whether this extension is enabled (true) or not (false).
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return 1 === absint( Settings::get_instance()->get_setting( 'personioIntegrationEnableAvailabilityCheck' ) );
	}

	/**
	 * Return the description for this extension.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return sprintf( __( 'Checks your positions for availability on your Personio career page. This ensures that applicants can reach the application form there. If a position is not available, you will be informed of this in the list of positions.', 'personio-integration-light' ), esc_url( PersonioPosition::get_instance()->get_link() ) );
	}
}
