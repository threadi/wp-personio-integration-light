<?php
/**
 * File to handle setup for this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) or exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Imports;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\PersonioIntegration\Taxonomies;

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
	 * The object of the setup.
	 *
	 * @var \wpEasySetup\Setup
	 */
	private \wpEasySetup\Setup $setup_obj;

	/**
	 * Constructor for this handler.
	 */
	private function __construct() {
		// get the setup-object.
		$this->setup_obj = \wpEasySetup\Setup::get_instance();
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
	 * Initialize the setup-object.
	 *
	 * @return void
	 */
	public function init(): void {
		add_filter( 'wp_easy_setup_completed', array( $this, 'check_completed_value' ) );
		add_action( 'wp_easy_setup_set_completed', array( $this, 'set_completed' ) );
		add_action( 'wp_easy_setup_process', array( $this, 'run_process' ) );

		// set configuration for the setup.
		$this->setup_obj->set_config( $this->get_config() );

		// show hint if setup should be run.
		$this->show_hint();

		if ( ! $this->is_completed() ) {
			// add hooks to enable the setup of this plugin.
			add_action( 'admin_menu', array( $this, 'add_setup_menu' ) );

			// use own hooks.
			add_action( 'personio_integration_import_max_count', array( $this, 'update_max_step' ) );
			add_action( 'personio_integration_import_count', array( $this, 'update_step' ) );
		}
	}

	/**
	 * Return whether setup is completed.
	 *
	 * @return bool
	 */
	public function is_completed(): bool {
		return $this->setup_obj->is_completed();
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
	 * Check if setup should be run and show hint for it.
	 *
	 * @return void
	 */
	public function show_hint(): void {
		// get transients object.
		$transients_obj = Transients::get_instance();

		// check if setup should be run.
		if ( ! $this->setup_obj->is_completed() ) {
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
		echo $this->setup_obj->display();
	}

	/**
	 * Add setup menu of setup is not completed.
	 *
	 * @return void
	 */
	public function add_setup_menu(): void {
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
	 * Return configuration for setup.
	 *
	 * Here we define which steps and texts are used by wp-easy-setup.
	 *
	 * @return array
	 */
	private function get_config(): array {
		// get setup.
		$setup = $this->get_setup();

		// collect configuration.
		$config = array(
			'name' => 'personio-integration-light',
			'url' => Helper::get_plugin_url(),
			'path' => Helper::get_plugin_path(),
			'title'                 => __( 'Personio Integration Light', 'personio-integration-light' ) . ' ' . __( 'Setup', 'personio-integration-light' ),
			'steps'                 => $setup,
			'back_button_label'     => __( 'Back', 'personio-integration-light' ) . '<span class="dashicons dashicons-controls-undo"></span>',
			'continue_button_label' => __( 'Continue', 'personio-integration-light' ) . '<span class="dashicons dashicons-controls-play"></span>',
			'finish_button_label'   => __( 'Completed', 'personio-integration-light' ) . '<span class="dashicons dashicons-saved"></span>',
			'title_error'      => __( 'Error', 'personio-integration-light' ),
			'txt_error_1'      => __( 'The following error occurred:', 'personio-integration-light' ),
			/* translators: %1$s will be replaced with the URL of the plugin-forum on wp.org */
			'txt_error_2'      => sprintf( __( '<strong>If reason is unclear</strong> please contact our <a href="%1$s" target="_blank">support-forum (opens new window)</a> with as much detail as possible.', 'personio-integration-light' ), esc_url( Helper::get_plugin_support_url() ) ),
		);

		/**
		 * Filter the setup configuration.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 * @param array $config List of setup-configuration.
		 */
		return apply_filters( 'personio_integration_setup_config', $config );
	}

	/**
	 * Set process label.
	 *
	 * @param string $label The label to process.
	 *
	 * @return void
	 */
	private function set_process_label( string $label ): void {
		update_option( 'wp_easy_setup_step_label', $label );
	}

	/**
	 * Updates the process step.
	 *
	 * @param int $step Steps to add.
	 *
	 * @return void
	 */
	public function update_process_step( int $step = 1 ): void {
		update_option( 'wp_easy_setup_step', absint( get_option( 'wp_easy_setup_step', 0 ) + $step ) );
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
				'help'                                => array(
					'type' => 'Text',
					/* translators: %1$s will be replaced by our support-forum-URL. */
					'text' => '<p><span class="dashicons dashicons-editor-help"></span> ' . sprintf( __( '<strong>Need help?</strong> Ask in <a href="%1$s" target="_blank">our forum (opens new window)</a>.', 'personio-integration-light' ), esc_url( Helper::get_plugin_support_url() ) ) . '</p>',
				),
			),
			2 => array(
				'runSetup' => array(
					'type'  => 'ProgressBar',
					'label' => __( 'Setup preparing your Personio data', 'personio-integration-light' )
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
		update_option( 'wp_easy_setup_max_steps', absint( get_option( 'wp_easy_setup_max_steps' ) ) + $max_count );
	}

	/**
	 * Update count.
	 *
	 * @param int $count The value to add.
	 *
	 * @return void
	 */
	public function update_step( int $count ): void {
		update_option( 'wp_easy_setup_step', absint( get_option( 'wp_easy_setup_step' ) ) + $count );
	}

	/**
	 * Run the process.
	 *
	 * @return void
	 */
	public function run_process(): void {
		// get the max steps for this process.
		$max_steps = Taxonomies::get_instance()->get_taxonomy_defaults_count() + count( Imports::get_instance()->get_personio_urls() );

		// set max step count (taxonomy-labels + Personio-accounts).
		update_option( 'wp_easy_setup_max_steps', $max_steps );

		// 1. Run import of taxonomies.
		$this->set_process_label( __( 'Import of Personio labels running.', 'personio-integration-light' ) );
		Taxonomies::get_instance()->create_defaults( array( $this, 'update_process_step' ) );

		// 2. Run import of positions.
		$this->set_process_label( __( 'Import of your Personio positions running.', 'personio-integration-light' ) );
		$imports_obj = Imports::get_instance();
		$imports_obj->run();

		// set steps to max steps to end the process.
		update_option( 'wp_easy_setup_step', $max_steps );

		$completed_text = __( 'Setup has been run. Your positions from Personio has been imported. Click on "Completed" to view them in an intro.', 'personio-integration-light' );
		/**
		 * Filter the text for display if setup has been run.
		 */
		$this->set_process_label( apply_filters( 'personio_integration_setup_process_completed_text', $completed_text ) );
	}

	/**
	 * Run additional tasks if setup has been marked as completed.
	 *
	 * @return void
	 */
	public function set_completed(): void {
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
	 * If Personio URL is set do not run the setup.
	 *
	 * @param bool $is_completed Whether to run setup (true) or not (false).
	 *
	 * @return bool
	 */
	public function check_completed_value( bool $is_completed ): bool {
		if( Helper::is_personio_url_set() ) {
			return true;
		}

		return $is_completed;
	}
}
