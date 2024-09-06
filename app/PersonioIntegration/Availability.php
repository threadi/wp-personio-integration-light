<?php
/**
 * File to handle availability checks for positions.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Settings;
use PersonioIntegrationLight\Plugin\Setup;

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
	 * Name of the setting field which defines its state.
	 *
	 * @var string
	 */
	protected string $setting_field = 'personioIntegrationEnableAvailabilityCheckStatus';

	/**
	 * Name if the setting tab where the setting field is visible.
	 *
	 * @var string
	 */
	protected string $setting_tab = 'import';

	/**
	 * Internal name of the used category.
	 *
	 * @var string
	 */
	protected string $extension_category = 'positions';

	/**
	 * Initialize this plugin.
	 *
	 * @return void
	 */
	public function init(): void {
		// add as extension.
		add_filter( 'personio_integration_extensions_table_extension', array( $this, 'add_extension' ) );

		// bail if extension is not enabled.
		if ( ! $this->is_enabled() && ! defined( 'PERSONIO_INTEGRATION_UPDATE_RUNNING' ) && ! defined( 'PERSONIO_INTEGRATION_DEACTIVATION_RUNNING' ) ) {
			return;
		}

		// use our own hooks.
		add_filter( 'personio_integration_schedules', array( $this, 'add_schedule' ) );
		add_action( 'personio_integration_import_ended', array( $this, 'run' ) );
		add_filter( 'personio_integration_settings', array( $this, 'add_settings' ) );
		add_filter( 'personio_integration_log_categories', array( $this, 'add_log_categories' ) );

		// extend the position table.
		add_filter( 'manage_' . PersonioPosition::get_instance()->get_name() . '_posts_columns', array( $this, 'add_column' ) );
		add_action( 'manage_' . PersonioPosition::get_instance()->get_name() . '_posts_custom_column', array( $this, 'add_column_content' ), 10, 2 );

		// AJAX-requests.
		add_action( 'wp_ajax_personio_run_availability_check', array( $this, 'single_check_via_request' ) );
		add_action( 'wp_ajax_personio_get_availability_check_info', array( $this, 'get_single_check_status' ) );

		// misc.
		add_action( 'admin_enqueue_scripts', array( $this, 'add_js' ), PHP_INT_MAX );
	}

	/**
	 * Add settings for this extension.
	 *
	 * @param array $settings List of settings.
	 *
	 * @return array
	 */
	public function add_settings( array $settings ): array {
		if ( empty( $settings['settings_section_import']['fields'] ) ) {
			return $settings;
		}
		$settings['settings_section_import_other']['fields']['personioIntegrationEnableAvailabilityCheck'] = array(
			'label'               => __( 'Enable availability checks', 'personio-integration-light' ),
			'field'               => array( 'PersonioIntegrationLight\Plugin\Admin\SettingFields\Checkbox', 'get' ),
			'description'         => __( 'If enabled the plugin will daily check the availability of position pages on Personio. You will be warned if a position is not available.', 'personio-integration-light' ),
			'register_attributes' => array(
				'type'    => 'integer',
				'default' => 1,
			),
			'source'              => WP_PERSONIO_INTEGRATION_PLUGIN,
			'callback'            => array( 'PersonioIntegrationLight\Plugin\Admin\SettingsSavings\Availability', 'save' ),
		);

		// return resulting list of settings.
		return $settings;
	}

	/**
	 * Run the checks for availability of multiple positions.
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
		Setup::get_instance()->set_process_label( __( 'Setup is checking the availability of each position.', 'personio-integration-light' ) );

		// set import-label.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_STATUS, __( 'We check the availability of each position.', 'personio-integration-light' ) );

		// show cli hint.
		$progress = Helper::is_cli() ? \WP_CLI\Utils\make_progress_bar( 'Run availability checks', count( $positions ) ) : false;

		// loop through the positions and check each.
		foreach ( $positions as $position_obj ) {
			if ( $position_obj instanceof Position ) {
				// run check for this single position.
				$this->run_single_check( $position_obj );

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
		$list_of_schedules[] = '\PersonioIntegrationLight\Plugin\Schedules\Availability';

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
				$html = '<a class="dashicons dashicons-yes personio-integration-availability-check" data-post-id="' . esc_attr( $position_obj->get_id() ) . '" href="#" title="' . __( 'Available', 'personio-integration-light' ) . '"></a>';
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
						'<p><strong>' . __( 'If the Personio page for this position is not available, no one will be able to apply for it directly.', 'personio-integration-light' ) . '</strong></p>',
						'<p>' . __( 'We will check the availability after every import of positions for you.', 'personio-integration-light' ) . '</p>',
						/* translators: %1$s will be replaced by the link to the Personio account */
						'<p>' . sprintf( __( 'Check in your <a href="%1$s" target="_blank">Personio account (opens new window)</a> why the page is not available.<br>You may have only deactivated the career page.', 'personio-integration-light' ), esc_url( Helper::get_personio_login_url() ) ) . '</p>',
						'<p>' . __( 'With <strong>Personio Integration Pro</strong>, you can also enter applications directly in the WordPress website and transfer them to Personio.<br>The career page of a position in Personio does not need to be enabled for this.', 'personio-integration-light' ) . '</p>',
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
				$html = '<a class="dashicons dashicons-no personio-integration-availability-check" href="#" data-post-id="' . esc_attr( $position_obj->get_id() ) . '" title="' . __( 'Not available', 'personio-integration-light' ) . '"></a> <a class="pro-marker wp-easy-dialog" data-dialog="' . esc_attr( wp_json_encode( $dialog ) ) . '"><span class="dashicons dashicons-editor-help"></span></a>';
				/**
				 * Filter the availability "no"-output.
				 *
				 * @since 3.0.0 Available since 3.0.0.
				 *
				 * @param string $html The output.
				 * @param Position $position_obj The position as object.
				 */
				echo wp_kses_post( apply_filters( 'personio_integration_light_position_availability_no', $html, $position_obj ) );
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
			'state'       => false,
			'name'        => 'Availability',
			'description' => __( 'Check each position for availability on your Personio career page.', 'personio-integration-light' ),
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
		return 1 === absint( Settings::get_instance()->get_setting( $this->get_settings_field_name() ) );
	}

	/**
	 * Return the description for this extension.
	 *
	 * @return string
	 */
	public function get_description(): string {
		/* translators: %1$s will be replaced by the URL for the positions list. */
		return sprintf( __( 'Checks your positions for availability on your Personio career page. This ensures that applicants can reach the application form there. If a position is not available, you will be informed of this in the <a href="%1$s">list of positions</a>.', 'personio-integration-light' ), esc_url( PersonioPosition::get_instance()->get_link() ) );
	}

	/**
	 * Whether this extension is enabled by default (true) or not (false).
	 *
	 * @return bool
	 */
	protected function is_default_enabled(): bool {
		return true;
	}

	/**
	 * Add own JS for backend.
	 *
	 * @return void
	 */
	public function add_js(): void {
		// backend-JS.
		wp_enqueue_script(
			'personio-integration-admin-availability',
			Helper::get_plugin_url() . 'admin/availability.js',
			array( 'jquery', 'wp-easy-dialog' ),
			Helper::get_file_version( Helper::get_plugin_path() . 'admin/availability.js' ),
			true
		);

		// add php-vars to our js-script.
		wp_localize_script(
			'personio-integration-admin-availability',
			'personioIntegrationLightAvailabilityJsVars',
			array(
				'ajax_url'                     => admin_url( 'admin-ajax.php' ),
				'pro_url'                      => Helper::get_pro_url(),
				'availability_nonce'           => wp_create_nonce( 'personio-integration-availability-nonce' ),
				'get_availability_check_nonce' => wp_create_nonce( 'personio-integration-availability-info-nonce' ),
				'title_check_in_progress'      => __( 'Checking availability', 'personio-integration-light' ),
				'lbl_ok'                       => __( 'OK', 'personio-integration-light' ),
				'title_check_success'          => __( 'Availability checked', 'personio-integration-light' ),
				'txt_check_success'            => __( 'The check has been run.', 'personio-integration-light' ),
			)
		);
	}

	/**
	 * Run single check via AJAX-request.
	 *
	 * @return void
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
	 */
	public function single_check_via_request(): void {
		// check none.
		check_ajax_referer( 'personio-integration-availability-nonce', 'nonce' );

		// mark as running.
		update_option( 'personio_integration_availability_check_running', 1 );

		// get post-id.
		$post_id = absint( filter_input( INPUT_POST, 'post', FILTER_SANITIZE_NUMBER_INT ) );

		if ( $post_id > 0 ) {
			$this->run_single_check( Positions::get_instance()->get_position( $post_id ) );
		}

		// remove mark as running.
		delete_option( 'personio_integration_availability_check_running' );

		// return nothing.
		wp_die();
	}

	/**
	 * Return info about running check for availability of single position.
	 *
	 * @return void
	 */
	public function get_single_check_status(): void {
		$is_running = 1 === absint( get_option( 'personio-integration-availability-info-nonce', 0 ) );

		wp_send_json(
			array(
				'running' => $is_running,
				'status'  => $is_running ? __( 'Check is running ..', 'personio-integration-light' ) : __( 'Check has been run.', 'personio-integration-light' ),
			)
		);
	}

	/**
	 * Run check of single position.
	 *
	 * @param Position $position_obj The object of the position.
	 *
	 * @return void
	 */
	private function run_single_check( Position $position_obj ): void {
		// get log object.
		$log = new Log();

		// define settings for second request to get the contents.
		$args     = array(
			'timeout'     => get_option( 'personioIntegrationUrlTimeout' ),
			'redirection' => 0,
		);
		$response = wp_remote_head( $position_obj->get_application_url(), $args );

		if ( is_wp_error( $response ) ) {
			// log possible error.
			$log->add_log( 'Error on request to get position availability: ' . $response->get_error_message(), 'error', 'availability' );
		} elseif ( empty( $response ) ) {
			// log im result is empty.
			$log->add_log( 'Get empty response for position availability.', 'error', 'availability' );
		} else {
			// get the http-status to check if call results in acceptable results.
			$http_status = $response['http_response']->get_status();

			// get extension to save the availability.
			$availability_extension = $position_obj->get_extension( 'PersonioIntegrationLight\PersonioIntegration\Extensions\Availability' );

			// bail if extension could not be loaded.
			if ( ! ( $availability_extension instanceof Extensions\Availability ) ) {
				return;
			}

			// if http-status is not 200, mark the position as not available.
			$availability_extension->set_availability( 200 === $http_status );
		}
	}

	/**
	 * Toggle the state of this extension and reset its schedule.
	 *
	 * @return void
	 */
	public function toggle_state(): void {
		parent::toggle_state();

		// get the schedule object.
		$schedule_obj = new \PersonioIntegrationLight\Plugin\Schedules\Availability();

		// get the actual state.
		$state = absint( get_option( $this->get_settings_field_name() ) );

		// enable or disable the schedule depending on state.
		if ( 1 === $state ) {
			$schedule_obj->install();
		} else {
			$schedule_obj->delete();
		}
	}

	/**
	 * Add import categories.
	 *
	 * @param array $categories List of categories.
	 *
	 * @return array
	 */
	public function add_log_categories( array $categories ): array {
		// add categories we need for our cpt.
		$categories['availability'] = __( 'Availability', 'personio-integration-light' );

		// return resulting list.
		return $categories;
	}
}
