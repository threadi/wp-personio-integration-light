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
use PersonioIntegrationLight\PersonioIntegration\Imports;
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
	private array $setup = array();

	/**
	 * Constructor for this handler.
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
	public static function get_instance(): Setup {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Initialize the setup-object.
	 *
	 * @return void
	 */
	public function init(): void {
		$this->check();

		if ( ! $this->is_completed() ) {
			// add hooks.
			add_action( 'admin_init', array( $this, 'set_config' ) );
			add_action( 'admin_menu', array( $this, 'add_setup_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

			// register REST API.
			add_action( 'rest_api_init', array( $this, 'add_rest_api' ) );

			// use own hooks.
			add_action( 'personio_integration_import_max_count', array( $this, 'update_max_step' ) );
			add_action( 'personio_integration_import_count', array( $this, 'update_step' ) );
		}
	}

	/**
	 * Return the setup-URL.
	 *
	 * @return string
	 */
	public function get_setup_link(): string {
		return add_query_arg( array( 'page' => 'personioPositions' ), admin_url() . 'admin.php' );
	}

	/**
	 * Return whether the setup has been completed.
	 *
	 * @return bool
	 */
	public function is_completed(): bool {
		// return true if main block functions are not available.
		if ( ! has_action( 'enqueue_block_assets' ) ) {
			return true;
		}

		// return depending on own setting.
		return (bool) get_option( 'wp_easy_setup_completed', false );
	}

	/**
	 * Set setup as completed.
	 *
	 * @return void
	 */
	public function set_completed(): void {
		update_option( 'wp_easy_setup_completed', true );

		if ( Helper::is_admin_api_request() ) {
			// Return JSON with forward-URL.
			wp_send_json(
				array(
					'forward' => Intro::get_instance()->get_start_url(),
				)
			);
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
			// bail if hint is already set.
			if ( $transients_obj->get_transient_by_name( 'personio_integration_start_setup_hint' )->is_set() ) {
				return;
			}

			// delete all other transients.
			foreach ( $transients_obj->get_transients() as $transient_obj ) {
				$transient_obj->delete();
			}

			// add hint to run setup.
			$transient_obj = Transients::get_instance()->add();
			$transient_obj->set_name( 'personio_integration_start_setup_hint' );
			$transient_obj->set_message( __( '<strong>You have installed Personio Integration Light - nice and thank you!</strong> Now run the setup to expand your website with the possibilities of this plugin.', 'personio-integration-light' ) . '<br><br>' . sprintf( '<a href="%1$s" class="button button-primary">' . __( 'Start setup', 'personio-integration-light' ) . '</a>', esc_url( $this->get_setup_link() ) ) );
			$transient_obj->set_type( 'error' );
			$transient_obj->set_dismissible_days( 2 );
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
		if ( empty( $setup ) ) {
			$this->set_config();
			$setup = $this->setup;
		}

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
		global $submenu;
		if ( ! $this->is_completed() ) {
			// add main menu as setup entry.
			add_menu_page(
				__( 'Positions', 'personio-integration-light' ),
				__( 'Positions', 'personio-integration-light' ),
				'manage_options',
				PersonioPosition::get_instance()->get_name(),
				array( $this, 'display' ),
				Helper::get_plugin_url() . 'gfx/personio_icon.png',
				40
			);

			// add setup entry as sub-menu.
			add_submenu_page(
				PersonioPosition::get_instance()->get_name(),
				__( 'Personio Integration Light', 'personio-integration-light' ) . ' ' . __( 'Setup', 'personio-integration-light' ),
				__( 'Setup', 'personio-integration-light' ),
				'manage_' . PersonioPosition::get_instance()->get_name(),
				'personioPositions',
				array( $this, 'display' ),
				1
			);

			// remove menu page of our own cpt.
			remove_submenu_page( PersonioPosition::get_instance()->get_name(), PersonioPosition::get_instance()->get_name() );
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
			Helper::get_file_version( $admin_css_path )
		);

		// localize the script.
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
				/* translators: %1$s will be replaced with the URL of the plugin-forum on wp.org */
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
			'steps'                 => count( $this->get_setup() ),
			'back_button_label'     => __( 'Back', 'personio-integration-light' ) . '<span class="dashicons dashicons-controls-undo"></span>',
			'continue_button_label' => __( 'Continue', 'personio-integration-light' ) . '<span class="dashicons dashicons-controls-play"></span>',
			'finish_button_label'   => __( 'Completed', 'personio-integration-light' ) . '<span class="dashicons dashicons-saved"></span>',
		);
	}

	/**
	 * Run the setup-progress via REST API.
	 *
	 * @return void
	 */
	public function process_init(): void {
		// set marker that setup is running.
		update_option( 'wp_easy_setup_pi_running', 1 );

		// set max step count (taxonomy-labels + Personio-accounts).
		update_option( 'wp_easy_setup_pi_max_steps', Taxonomies::get_instance()->get_taxonomy_defaults_count() + count( Imports::get_instance()->get_personio_urls() ) );

		// set actual steps to 0.
		update_option( 'wp_easy_setup_pi_step', 0 );

		// 1. Run import of taxonomies.
		$this->set_process_label( __( 'Import of Personio labels running.', 'personio-integration-light' ) );
		Taxonomies::get_instance()->create_defaults( array( $this, 'update_process_step' ) );

		// 2. Run import of positions.
		$this->set_process_label( __( 'Import of your Personio positions running.', 'personio-integration-light' ) );
		$imports_obj = Imports::get_instance();
		$imports_obj->run();

		// cleanup.
		update_option( 'wp_easy_setup_pi_step_label', __( 'Setup has been run. Your positions from Personio has been imported. Click on "Completed" to view them in an intro.', 'personio-integration-light' ) );
		delete_option( 'wp_easy_setup_pi_running' );

		// return empty json.
		wp_send_json( array() );
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
	 * Get progress info via REST API.
	 *
	 * @return void
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
	}

	/**
	 * Sets the setup configuration.
	 *
	 * @return void
	 */
	public function set_config(): void {
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
				'help' => array(
					'type' => 'Text',
					'text' => '<p>'.sprintf( __( '<span class="dashicons dashicons-editor-help"></span> <strong>Need help?</strong> Ask in <a href="%1$s" target="_blank">our forum (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_plugin_support_url() ) ).'</p>'
				)
			),
			2 => array(
				'runSetup' => array(
					'type'  => 'ProgressBar',
					'label' => __( 'Setup preparing your Personio data', 'personio-integration-light' ),
				),
			),
		);
	}

	/**
	 * Update max count.
	 *
	 * @param int $max_count The value to add.
	 *
	 * @return void
	 */
	public function update_max_step( int $max_count ): void {
		update_option( 'wp_easy_setup_pi_max_steps', absint( get_option( 'wp_easy_setup_pi_max_steps' ) ) + $max_count );
	}

	/**
	 * Update count.
	 *
	 * @param int $count The value to add.
	 *
	 * @return void
	 */
	public function update_step( int $count ): void {
		update_option( 'wp_easy_setup_pi_step', absint( get_option( 'wp_easy_setup_pi_step' ) ) + $count );
	}
}