<?php
/**
 * File to handle setup for this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent also other direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Import;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\PersonioIntegration\Taxonomies;
use WP_REST_Request;
use WP_REST_Server;

/**
 * Initialize this plugin.
 */
class Setup {
	/**
	 * Instance of this object.
	 *
	 * @var ?Setup
	 */
	private static ?Setup $instance = null;

	/**
	 * Define setup as array with steps.
	 *
	 * @var array
	 */
	private array $setup;

	/**
	 * Constructor for Init-Handler.
	 */
	private function __construct() {
		// get properties from settings.
		$settings = Settings::get_instance();
		$settings->set_settings();

		// get URL-settings.
		$url_settings = $settings->get_settings_for_field( 'personioIntegrationUrl' );

		// get main language setting.
		$language_setting = $settings->get_settings_for_field( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE );

		// define setup.
		$this->setup = array(
			1 => array(
				'personioIntegrationUrl'              => array(
					'type'                => 'TextControl',
					'label'               => $url_settings['label'],
					'help'                => $url_settings['description'],
					'placeholder'         => $url_settings['placeholder'],
					'required'            => true,
					'validation_callback' => 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationUrl::rest_validate',
				),
				WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE => array(
					'type'                => 'RadioControl',
					'label'               => $language_setting['label'],
					'help'                => $language_setting['description'],
					'options'             => $this->convert_options_for_react( $language_setting['options'] ),
					'validation_callback' => 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\MainLanguage::rest_validate',
				),
			),
			2 => array(
				'runSetup' => array(
					'type'  => 'ProgressBar',
					'label' => __( 'Setup preparing your Personio data', 'personio-integration-light' ),
				),
			),
		);

		// add hooks.
		add_action( 'admin_action_personioIntegrationSetup', array( $this, 'display' ) );
		add_action( 'admin_menu', array( $this, 'add_setup_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
		add_action( 'personio_integration_import_count', array( $this, 'update_process_step' ) );
		add_action( 'personio_integration_import_finished', array( $this, 'update_process_step' ) );
		add_action( 'personio_integration_import_max_steps', array( $this, 'update_process_max_steps' ) );

		// register REST API.
		add_action( 'rest_api_init', array( $this, 'add_rest_api' ) );
	}

	/**
	 * Return the setup-URL.
	 *
	 * @return string
	 */
	private function get_setup_link(): string {
		return Helper::get_settings_url();
	}

	/**
	 * Initialize the setup-object.
	 *
	 * @return void
	 */
	public function init(): void {
		$this->check();
	}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Setup {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Return whether the setup has been completed.
	 *
	 * @return bool
	 */
	public function is_completed(): bool {
		return (bool) get_option( 'wp_easy_setup_completed', false );
	}

	/**
	 * Set setup as completed.
	 *
	 * @return void
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
	 */
	public function set_completed(): void {
		update_option( 'wp_easy_setup_completed', true );

		if ( Helper::is_admin_api_request() ) {
			// Return JSON with forward-URL.
			wp_send_json(
				array(
					'forward' => PersonioPosition::get_instance()->get_link(),
				)
			);

			// Don't forget to stop execution afterward.
			wp_die();
		}
	}

	/**
	 * Check if setup should be run and show hint for it.
	 *
	 * @return void
	 */
	public function check(): void {
		// get transients object.
		$transients_obj = Transients::get_instance();

		// check if setup should be run.
		if ( ! $this->is_completed() ) {
			// delete all other transients.
			foreach ( $transients_obj->get_transients() as $transient_obj ) {
				$transient_obj->delete();
			}

			// add hint to run setup.
			$transient_obj = Transients::get_instance()->add();
			$transient_obj->set_name( 'personio_integration_start_setup_hint' );
			$transient_obj->set_message( __( '<strong>You have installed Personio Integration Light - nice and thank you!</strong> Now run the setup to expand your website with the possibilities of this plugin.', 'personio-integration-light' ) . '<br><br>' . sprintf( '<a href="%1$s" class="button button-primary">' . __( 'Start setup', 'personio-integration-light' ) . '</a>', esc_url( $this->get_setup_link() ) ) );
			$transient_obj->set_type( 'error' );
			$transient_obj->set_hide_on(
				array(
					Helper::get_settings_url(),
					PersonioPosition::get_instance()->get_link(),
					add_query_arg( array( 'page' => 'personioPositions' ), admin_url() . 'admin.php' ),
				)
			);
			$transient_obj->save();
		} else {
			$transients_obj->get_transient_by_name( 'personio_integration_start_setup_hint' )->delete();
		}
	}

	/**
	 * Return the configured setup.
	 *
	 * @return array
	 */
	private function get_setup(): array {
		$setup = $this->setup;

		/**
		 * Filter the configured setup for this plugin.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array $setup The setup-configuration.
		 */
		return apply_filters( 'personio_integration_setup', $setup );
	}

	/**
	 * Show setup dialog.
	 *
	 * @return void
	 */
	public function display(): void {
		echo '<div id="wp-plugin-setup" data-config="' . esc_attr( wp_json_encode( $this->get_config() ) ) . '" data-fields="' . esc_attr( wp_json_encode( $this->get_setup() ) ) . '"></div>';
	}

	/**
	 * Add setup menu of setup is not completed.
	 *
	 * @return void
	 */
	public function add_setup_menu(): void {
		if ( ! $this->is_completed() ) {
			// add setup menu entry.
			add_submenu_page(
				PersonioPosition::get_instance()->get_link( true ),
				__( 'Personio Integration Light', 'personio-integration-light' ) . ' ' . __( 'Setup', 'personio-integration-light' ),
				__( 'Setup', 'personio-integration-light' ),
				'manage_' . PersonioPosition::get_instance()->get_name(),
				'personioPositions',
				array( $this, 'display' ),
				1
			);

			// remove menu page of our own cpt.
			remove_submenu_page( 'edit.php?post_type=' . PersonioPosition::get_instance()->get_name(), 'edit.php?post_type=' . PersonioPosition::get_instance()->get_name() );
		}
	}

	/**
	 * Embed our own scripts for setup-dialog.
	 *
	 * @return void
	 */
	public function admin_scripts(): void {
		// embed necessary scripts for setup.
		$path = Helper::get_plugin_path() . 'blocks/setup/';
		$url  = Helper::get_plugin_url() . 'blocks/setup/';

		// bail if path does not exist.
		if ( ! file_exists( $path ) ) {
			return;
		}

		// embed the setup-JS-script.
		$script_asset_path = $path . 'build/setup.asset.php';
		$script_asset      = require $script_asset_path;
		wp_enqueue_script(
			'wp-easy-setup',
			$url . 'build/setup.js',
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		// embed the dialog-components CSS-script.
		$admin_css      = $url . 'build/setup.css';
		$admin_css_path = $path . 'build/setup.css';
		wp_enqueue_style(
			'wp-easy-setup',
			$admin_css,
			array( 'wp-components' ),
			filemtime( $admin_css_path )
		);

		wp_localize_script(
			'wp-easy-setup',
			'wp_easy_setup',
			array(
				'rest_nonce'       => wp_create_nonce( 'wp_rest' ),
				'validation_url'   => rest_url( 'wp-easy-setup/v1/validate-field' ),
				'process_url'      => rest_url( 'wp-easy-setup/v1/process' ),
				'process_info_url' => rest_url( 'wp-easy-setup/v1/get-process-info' ),
				'completed_url'    => rest_url( 'wp-easy-setup/v1/completed' ),
				'title_error'      => __( 'Error', 'personio-integration-light' ),
				'txt_error_1'      => __( 'The following error occurred:', 'personio-integration-light' ),
				/* translators: %1$s will be replaced with the URL of the plugin-forum on wordpress.org */
				'txt_error_2'      => sprintf( __( '<strong>If reason is unclear</strong> please contact our <a href="%1$s" target="_blank">support-forum (opens new window)</a> with as much detail as possible.', 'personio-integration-light' ), esc_url( Helper::get_plugin_support_url() ) ),
			)
		);
	}

	/**
	 * Convert options array to react-compatible array-list with label and value.
	 *
	 * @param array $options The list of options to convert.
	 *
	 * @return array
	 */
	private function convert_options_for_react( array $options ): array {
		// define resulting list.
		$resulting_array = array();

		// loop through the options.
		foreach ( $options as $key => $label ) {
			$resulting_array[] = array(
				'label' => $label,
				'value' => $key,
			);
		}

		// return resulting list.
		return $resulting_array;
	}

	/**
	 * Validate a given field via REST API request.
	 *
	 * @param WP_REST_Request $request The REST API request object.
	 *
	 * @return void
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
	 */
	public function validate_field( WP_REST_Request $request ): void {
		$validation_result = array(
			'field_name' => false,
			'result'     => 'error',
		);

		// get setup step.
		$step = $request->get_param( 'step' );

		// get field-name.
		$field_name = $request->get_param( 'field_name' );

		// get value.
		$value = $request->get_param( 'value' );

		// get setup-fields.
		$fields = $this->get_setup();

		// run check if all 3 vars are filled.
		if ( ! empty( $step ) && ! empty( $field_name ) ) {
			// set field for response.
			$validation_result['field_name'] = $field_name;
			// check if field exist in step.
			if ( ! empty( $fields[ $step ][ $field_name ] ) ) {
				// get validation-callback for this field.
				$validation_callback = $this->get_setup()[ $step ][ $field_name ]['validation_callback'];
				if ( ! empty( $validation_callback ) ) {
					if ( is_callable( $validation_callback ) ) {
						$validation_result['result'] = call_user_func( $validation_callback, $value );
					}
				}
			}
		}

		// Return JSON with results.
		wp_send_json( $validation_result );

		// Don't forget to stop execution afterward.
		wp_die();
	}

	/**
	 * Add rest api endpoints.
	 *
	 * @return void
	 */
	public function add_rest_api(): void {
		register_rest_route(
			'wp-easy-setup/v1',
			'/validate-field/',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'validate_field' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
		register_rest_route(
			'wp-easy-setup/v1',
			'/process/',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'process_init' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
		register_rest_route(
			'wp-easy-setup/v1',
			'/get-process-info/',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'get_process_info' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
		register_rest_route(
			'wp-easy-setup/v1',
			'/completed/',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'set_completed' ),
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
			)
		);
	}

	/**
	 * Return configuration for setup.

	 * Here we define how many steps are used.
	 *
	 * @return array
	 */
	private function get_config(): array {
		return array(
			'title'                 => __( 'Personio Integration Light', 'personio-integration-light' ) . ' ' . __( 'Setup', 'personio-integration-light' ),
			'steps'                 => 2,
			'back_button_label'     => __( 'Back', 'personio-integration-light' ) . '<span class="dashicons dashicons-controls-undo"></span>',
			'continue_button_label' => __( 'Continue', 'personio-integration-light' ) . '<span class="dashicons dashicons-controls-play"></span>',
			'finish_button_label'   => __( 'Finish', 'personio-integration-light' ) . '<span class="dashicons dashicons-saved"></span>',
		);
	}

	/**
	 * Run the setup-progress via REST API.
	 *
	 * @return void
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
	 */
	public function process_init(): void {
		// set marker that setup is running.
		update_option( 'wp_easy_setup_pi_running', 1 );

		// set max step count (taxonomy-labels + Personio-accounts).
		update_option( 'wp_easy_setup_pi_max_steps', Taxonomies::get_instance()->get_taxonomy_defaults_count() + 1 );

		// set actual steps to 0.
		update_option( 'wp_easy_setup_pi_step', 0 );

		// 1. Run import taxonomies.
		$this->set_process_label( __( 'Import Personio labels running.', 'personio-integration-light' ) );
		Taxonomies::get_instance()->create_defaults( array( $this, 'update_process_step' ) );

		// 2. Run import positions.
		$this->set_process_label( __( 'Import of your Personio positions running.', 'personio-integration-light' ) );
		new Import();

		// cleanup.
		update_option( 'wp_easy_setup_pi_step_label', __( 'Setup has been run.', 'personio-integration-light' ) );
		delete_option( 'wp_easy_setup_pi_running' );

		// return empty json.
		wp_send_json( array() );

		// Don't forget to stop execution afterward.
		wp_die();
	}

	/**
	 * Set process label.
	 *
	 * @param string $label The label to process.
	 *
	 * @return void
	 */
	private function set_process_label( string $label ): void {
		update_option( 'wp_easy_setup_pi_step_label', $label );
	}

	/**
	 * Updates the process step.
	 *
	 * @param int $step Steps to add.
	 *
	 * @return void
	 */
	public function update_process_step( int $step = 1 ): void {
		update_option( 'wp_easy_setup_pi_step', absint( get_option( 'wp_easy_setup_pi_step', 0 ) + $step ) );
	}

	/**
	 * Updates the max steps for processing.
	 *
	 * @param int $max_steps Steps to add to max steps.
	 *
	 * @return void
	 */
	public function update_process_max_steps( int $max_steps = 1 ): void {
		update_option( 'wp_easy_setup_pi_max_steps', absint( get_option( 'wp_easy_setup_pi_max_steps', 0 ) + $max_steps ) );
	}

	/**
	 * Get progress info via REST API.
	 *
	 * @return void
	 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
	 */
	public function get_process_info(): void {
		$return = array(
			'running'    => absint( get_option( 'wp_easy_setup_pi_running', 0 ) ),
			'max'        => absint( get_option( 'wp_easy_setup_pi_max_steps', 0 ) ),
			'step'       => absint( get_option( 'wp_easy_setup_pi_step', 0 ) ),
			'step_label' => get_option( 'wp_easy_setup_pi_step_label', '' ),
		);

		// Return JSON with result.
		wp_send_json( $return );

		// Don't forget to stop execution afterward.
		wp_die();
	}
}
