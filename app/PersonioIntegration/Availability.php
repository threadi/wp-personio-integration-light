<?php
/**
 * File to handle availability checks for positions.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Checkbox;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Page;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Tab;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationUrl;
use PersonioIntegrationLight\Plugin\Setup;
use WP_REST_Request;

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
	protected string $setting_sub_tab = 'availability';

	/**
	 * Internal name of the used category.
	 *
	 * @var string
	 */
	protected string $extension_category = 'positions';

	/**
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Availability
	 */
	private static ?Availability $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Availability {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {
		// bail if extension is not enabled.
		if ( ! defined( 'PERSONIO_INTEGRATION_ACTIVATION_RUNNING' ) && ! defined( 'PERSONIO_INTEGRATION_UPDATE_RUNNING' ) && ! defined( 'PERSONIO_INTEGRATION_DEACTIVATION_RUNNING' ) && ! $this->is_enabled() ) {
			return;
		}

		// add the settings.
		add_action( 'init', array( $this, 'add_the_settings' ), 20 );

		// use our own hooks.
		add_filter( 'personio_integration_schedules', array( $this, 'add_schedule' ) );
		add_action( 'personio_integration_import_ended', array( $this, 'run' ) );
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
	 * @return void
	 */
	public function add_the_settings(): void {
		// get settings object.
		$settings_obj = Settings::get_instance();

		// get the main settings page.
		$main_settings_page = $settings_obj->get_page( 'personioPositions' );

		// bail if page could not be loaded.
		if ( ! $main_settings_page instanceof Page ) {
			return;
		}

		// get the extensions tab.
		$extension_tab = $main_settings_page->get_tab( $this->get_setting_tab() );

		// bail if tab could not be loaded.
		if ( ! $extension_tab instanceof Tab ) {
			return;
		}

		// add our own tab.
		$availability_tab = $extension_tab->add_tab( $this->get_setting_sub_tab(), 10 );
		$availability_tab->set_title( __( 'Availability', 'personio-integration-light' ) );
		$extension_tab->set_default_tab( $availability_tab );

		// add the section.
		$availability_section = $availability_tab->add_section( 'settings_section_availability', 10 );
		$availability_section->set_title( __( 'Settings for availability check', 'personio-integration-light' ) );

		// add setting.
		$automatic_import_setting = $settings_obj->add_setting( 'personioIntegrationEnableAvailabilityCheck' );
		$automatic_import_setting->set_section( $availability_section );
		$automatic_import_setting->set_type( 'integer' );
		$automatic_import_setting->set_default( 1 );
		$automatic_import_setting->set_save_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsSavings\Availability', 'save' ) );
		$field = new Checkbox();
		$field->set_title( __( 'Enable availability checks', 'personio-integration-light' ) );
		$field->set_description( __( 'If enabled the plugin will daily check the availability of position pages on Personio. You will be warned if a position is not available.', 'personio-integration-light' ) );
		$automatic_import_setting->set_field( $field );
	}

	/**
	 * Run the checks for availability of multiple positions.
	 *
	 * @return void
	 */
	public function run(): void {
		// bail if settings is not enabled.
		if ( 1 !== absint( get_option( 'personioIntegrationEnableAvailabilityCheck' ) ) ) {
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

			// show progress.
			$progress ? $progress->tick() : false;
		}

		// show progress.
		$progress ? $progress->finish() : false;
	}

	/**
	 * Add our own schedule to the list.
	 *
	 * @param array<string> $list_of_schedules List of schedules.
	 *
	 * @return array<string>
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
	 * @param array<int|string, mixed> $columns List of columns.
	 *
	 * @return array<int|string, mixed>
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

		// show column for availability.
		if ( 'personio_integration_position_availability' === $column ) {
			if ( $this->get_extension( $position_obj )->get_availability() ) {
				$html = '<a class="dashicons dashicons-yes personio-integration-availability-check" data-post-id="' . absint( $position_obj->get_id() ) . '" href="#" title="' . __( 'Available', 'personio-integration-light' ) . '"></a>';
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
						'<p>' . sprintf( __( 'Check in your <a href="%1$s" target="_blank">Personio account (opens new window)</a> why the page is not available.<br>You may have only deactivated the career page.', 'personio-integration-light' ), esc_url( Personio_Accounts::get_instance()->get_login_url() ) ) . '</p>',
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
				$html = '<a class="dashicons dashicons-no personio-integration-availability-check" href="#" data-post-id="' . absint( $position_obj->get_id() ) . '" title="' . __( 'Not available', 'personio-integration-light' ) . '"></a> <a class="pro-marker easy-dialog-for-wordpress" data-dialog="' . esc_attr( Helper::get_json( $dialog ) ) . '"><span class="dashicons dashicons-editor-help"></span></a>';
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
		return 1 === absint( get_option( $this->get_settings_field_name() ) );
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
	 * @param string $hook The used hook.
	 *
	 * @return void
	 */
	public function add_js( string $hook ): void {
		// do not load styles depending on used hook.
		if ( Helper::do_not_load_styles( $hook ) ) {
			return;
		}

		// backend-JS.
		wp_enqueue_script(
			'personio-integration-admin-availability',
			Helper::get_plugin_url() . 'admin/availability.js',
			array( 'jquery', 'easy-dialog-for-wordpress' ),
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

		// return ok-message.
		wp_send_json_success();
	}

	/**
	 * Return info about running check for availability of single position.
	 *
	 * @return void
	 */
	public function get_single_check_status(): void {
		// get the state.
		$is_running = 1 === absint( get_option( 'personio-integration-availability-info-nonce', 0 ) );

		// return the result.
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
		// define settings for second request to get the contents.
		$args     = array(
			'timeout'     => get_option( 'personioIntegrationUrlTimeout' ),
			'redirection' => 0,
		);
		$response = wp_remote_head( $position_obj->get_application_url(), $args );

		if ( is_wp_error( $response ) ) {
			// log possible error.
			Log::get_instance()->add( 'Error on request to get position availability: ' . $response->get_error_message(), 'error', 'availability' );
		} else {
			// get the http-status to check if call results in acceptable results.
			$http_status = $response['http_response']->get_status();

			// if http-status is not 200, mark the position as not available.
			$this->get_extension( $position_obj )->set_availability( 200 === $http_status );
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
	 * @param array<string,string> $categories List of categories.
	 *
	 * @return array<string,string>
	 */
	public function add_log_categories( array $categories ): array {
		// add categories we need for our cpt.
		$categories['availability'] = __( 'Availability', 'personio-integration-light' );

		// return resulting list.
		return $categories;
	}

	/**
	 * Get the extension for the position-object itself.
	 *
	 * @param Position $position_obj The object of the position.
	 *
	 * @return Extensions\Availability
	 */
	private function get_extension( Position $position_obj ): Extensions\Availability {
		return new Extensions\Availability( $position_obj->get_id() );
	}

	/**
	 * Return the installation state of the dependent plugin/theme.
	 *
	 * @return bool
	 */
	public function is_installed(): bool {
		return true;
	}

	/**
	 * Check the Personio-URL availability.
	 *
	 * @param WP_REST_Request $request The request-object.
	 *
	 * @return array<string,mixed>
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
			$result['description'] = sprintf( __( 'The Personio-URL <a href="%1$s" target="_blank">%2$s (opens new window)</a> is not available for the import of positions!<br><strong>Please check if you have entered the correct URL <a href="%3$s">in the plugin-settings</a>.<br>Also check if you have enabled the XML-API in your <a href="%4$s" target="_blank">Personio-account (opens new window)</a> under Settings > Recruiting > Career Page > Activations.</strong>', 'personio-integration-light' ), esc_url( $personio_obj->get_url() ), esc_url( $personio_obj->get_url() ), esc_url( Helper::get_settings_url() ), esc_url( Personio_Accounts::get_instance()->get_login_url() ) );
		}

		// return result.
		return $result;
	}

	/**
	 * Return setting value.
	 *
	 * @param mixed $settings The settings as array.
	 *
	 * @return array<string,mixed>
	 * @deprecated since 5.0.0
	 */
	public function add_settings( mixed $settings ): array {
		_deprecated_function( __FUNCTION__, '5.0.0', '\PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings::get_instance()' );
		if ( ! is_array( $settings ) ) {
			return array();
		}
		return $settings;
	}
}
