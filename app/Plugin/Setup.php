<?php
/**
 * File to handle setup for this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use easyTransientsForWordPress\Transients;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Button;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Radio;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Text;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Import;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Setting;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\PersonioIntegration\Imports;
use PersonioIntegrationLight\PersonioIntegration\Imports_Base;
use PersonioIntegrationLight\PersonioIntegration\Personio_Accounts;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\PersonioIntegration\Taxonomies;

/**
 * Initialize the setup object.
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
	 * @var array<int,array<string,mixed>>
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
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize the setup-object.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'init', array( $this, 'init_setup' ) );
	}

	/**
	 * Initialize the setup.
	 *
	 * @return void
	 */
	public function init_setup(): void {
		// check to show hint if setup should be run.
		$this->show_hint();

		// only load setup if it is not completed.
		if ( ! $this->is_completed() ) {
			// initialize the setup object.
			$setup_obj = \easySetupForWordPress\Setup::get_instance();
			$setup_obj->init();

			// get the setup-object.
			$setup_obj->set_url( Helper::get_plugin_url() );
			$setup_obj->set_path( Helper::get_plugin_path() );
			$setup_obj->set_texts(
				array(
					'title_error' => __( 'Error', 'personio-integration-light' ),
					'txt_error_1' => __( 'The following error occurred:', 'personio-integration-light' ),
					/* translators: %1$s will be replaced with the URL of the plugin-forum on wp.org */
					'txt_error_2' => sprintf( __( '<strong>If reason is unclear</strong> please contact our <a href="%1$s" target="_blank">support-forum (opens new window)</a> with as much detail as possible.', 'personio-integration-light' ), esc_url( Helper::get_plugin_support_url() ) ),
				)
			);
			$setup_obj->set_display_hook( '_page_personioPositions' );

			// set configuration for setup.
			$setup_obj->set_config( $this->get_config() );

			// initialize the import object.
			Import::get_instance()->init();

			// only load setup if it is not completed.
			add_filter( 'esfw_completed', array( $this, 'check_completed_value' ), 10, 2 );
			add_action( 'esfw_set_completed', array( $this, 'set_completed' ) );
			add_action( 'esfw_process', array( $this, 'run_process' ) );
			add_action( 'esfw_process', array( $this, 'show_process_end' ), PHP_INT_MAX );

			// add hooks to enable the setup of this plugin.
			add_action( 'admin_menu', array( $this, 'add_setup_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'add_js_and_css' ) );

			// use own hooks.
			add_action( 'personio_integration_import_max_count', array( $this, 'update_max_step' ) );
			add_action( 'personio_integration_import_count', array( $this, 'update_process_step' ) );
		}
	}

	/**
	 * Return whether setup is completed.
	 *
	 * @return bool
	 */
	public function is_completed(): bool {
		$completed = \easySetupForWordPress\Setup::get_instance()->is_completed( $this->get_setup_name() );
		/**
		 * Filter the setup complete marker.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 * @param bool $completed True if setup has been completed.
		 */
		return apply_filters( 'personio_integration_light_setup_is_completed', $completed );
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
			$transient_obj->set_message( __( '<strong>You have installed Personio Integration Light - nice and thank you!</strong> Now run the setup to expand your website with the possibilities of this plugin to promote your open positions from Personio.', 'personio-integration-light' ) . '<br><br>' . sprintf( '<a href="%1$s" class="button button-primary">' . __( 'Start setup', 'personio-integration-light' ) . '</a>', esc_url( $this->get_setup_link() ) ) );
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
	 * @return array<int,array<string,mixed>>
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
		 * @param array<int,array<string,mixed>> $setup The setup-configuration.
		 */
		return apply_filters( 'personio_integration_setup', $setup );
	}

	/**
	 * Show setup dialog.
	 *
	 * @return void
	 */
	public function display(): void {
		// create help in case of error during loading of the setup.
		$error_help = '<div class="personio-integration-transient notice notice-success"><h3>' . wp_kses_post( Helper::get_logo_img() ) . ' ' . esc_html( apply_filters( 'personio_integration_light_transient_title', Helper::get_plugin_name() ) ) . '</h3><p><strong>' . __( 'Setup is loading', 'personio-integration-light' ) . '</strong><br>' . __( 'Please wait while we load the setup.', 'personio-integration-light' ) . '<br>' . __( 'However, you can also skip the setup and configure the plugin manually.', 'personio-integration-light' ) . '</p><p><a href="' . esc_url( \easySetupForWordPress\Setup::get_instance()->get_skip_url( $this->get_setup_name(), Helper::get_settings_url() ) ) . '" class="button button-primary">' . __( 'Skip setup', 'personio-integration-light' ) . '</a></p></div>';

		// add error text.
		\easySetupForWordPress\Setup::get_instance()->set_error_help( $error_help );

		// output.
		echo wp_kses_post( \easySetupForWordPress\Setup::get_instance()->display( $this->get_setup_name() ) );
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
			'manage_options',
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
	 * @param array<string,string> $options The list of options to convert.
	 *
	 * @return array<int,array<string,string>>
	 */
	public function convert_options_for_react( array $options ): array {
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
	 * @return array<string,array<int,mixed>|string>
	 */
	private function get_config(): array {
		// get setup.
		$setup = $this->get_setup();

		// collect configuration for the setup.
		$config = array(
			'name'                  => $this->get_setup_name(),
			'title'                 => __( 'Personio Integration Light', 'personio-integration-light' ) . ' ' . __( 'Setup', 'personio-integration-light' ),
			'steps'                 => $setup,
			'back_button_label'     => __( 'Back', 'personio-integration-light' ) . '<span class="dashicons dashicons-undo"></span>',
			'continue_button_label' => __( 'Continue', 'personio-integration-light' ) . '<span class="dashicons dashicons-controls-play"></span>',
			'finish_button_label'   => __( 'Completed', 'personio-integration-light' ) . '<span class="dashicons dashicons-saved"></span>',
			'skip_button_label'     => __( 'Skip', 'personio-integration-light' ) . '<span class="dashicons dashicons-undo"></span>',
			'skip_url'              => \easySetupForWordPress\Setup::get_instance()->get_skip_url( $this->get_setup_name(), Helper::get_settings_url() ),
		);

		/**
		 * Filter the setup configuration.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 * @param array<string,array<int,mixed>|string> $config List of configuration for the setup.
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
	public function set_process_label( string $label ): void {
		update_option( 'esfw_step_label', $label );
	}

	/**
	 * Updates the process step.
	 *
	 * @param int $step Steps to add.
	 *
	 * @return void
	 */
	public function update_process_step( int $step = 1 ): void {
		update_option( 'esfw_step', absint( get_option( 'esfw_step' ) + $step ) );
	}

	/**
	 * Sets the setup configuration.
	 *
	 * @return void
	 */
	public function set_config(): void {
		// get properties from settings.
		$settings = \PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings::get_instance();

		// get URL-settings.
		$url_settings = $settings->get_setting( 'personioIntegrationUrl' );

		// bail if URL setting could not be loaded.
		if ( ! $url_settings instanceof Setting ) {
			return;
		}

		// get field for URL settings.
		$url_field = $url_settings->get_field();

		// bail if field is not available.
		if ( ! $url_field instanceof Text ) {
			return;
		}

		// get main language setting.
		$language_setting = $settings->get_setting( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE );

		// bail if language setting could not be loaded.
		if ( ! $language_setting instanceof Setting ) {
			return;
		}

		// get field for URL settings.
		$language_field = $language_setting->get_field();

		// bail if field is not available.
		if ( ! $language_field instanceof Radio ) {
			return;
		}

		// define setup.
		$this->setup = array(
			1 => array(
				'personioIntegrationUrl'              => array(
					'type'                => 'TextControl',
					'label'               => $url_field->get_title(),
					'help'                => $url_field->get_description(),
					'placeholder'         => $url_field->get_placeholder(),
					'required'            => true,
					'validation_callback' => 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\PersonioIntegrationUrl::rest_validate',
				),
				WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE => array(
					'type'                => 'RadioControl',
					'label'               => $language_field->get_title(),
					'help'                => $language_field->get_description(),
					'options'             => $this->convert_options_for_react( $language_field->get_options() ),
					'validation_callback' => 'PersonioIntegrationLight\Plugin\Admin\SettingsValidation\MainLanguage::rest_validate',
				),
				'import_settings' => array(
					'type'                => 'ButtonControl',
					'variant' => 'secondary',
					'label'               => __( 'Import configuration', 'personio-integration-light' ),
					'help'                => __( 'Select a configuration file to quickly save the plugin settings. The setup will be skipped.', 'personio-integration-light' ),
					'onclick' => '() => personio_integration_settings_import_dialog_via_setup();'
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
					'label' => __( 'Setup preparing your Personio data', 'personio-integration-light' ),
				),
			),
		);
	}

	/**
	 * Update max count.
	 *
	 * @param int $add_to_max_count The value to add.
	 *
	 * @return void
	 */
	public function update_max_step( int $add_to_max_count ): void {
		update_option( 'esfw_max_steps', absint( get_option( 'esfw_max_steps' ) ) + $add_to_max_count );
	}

	/**
	 * Run the process.
	 *
	 * @param string $config_name The name of the setup-configuration.
	 *
	 * @return void
	 */
	public function run_process( string $config_name ): void {
		// bail if this is not our setup.
		if ( $config_name !== $this->get_setup_name() ) {
			return;
		}

		// update the max steps for this process.
		$this->update_max_step( Taxonomies::get_instance()->get_taxonomy_defaults_count() + count( Personio_Accounts::get_instance()->get_personio_urls() ) );

		// step 1: Run import of taxonomies.
		$this->set_process_label( __( 'Import of Personio labels running.', 'personio-integration-light' ) );
		Taxonomies::get_instance()->create_defaults( array( $this, 'update_process_step' ) );

		// step 2: Run import of positions.
		$this->set_process_label( __( 'Import of your Personio positions running.', 'personio-integration-light' ) );

		// get the import extension.
		$imports_obj = Imports::get_instance()->get_import_extension();
		if ( $imports_obj instanceof Imports_Base ) {
			$imports_obj->run();
		}
	}

	/**
	 * Show process end text.
	 *
	 * @param string $config_name The name of the setup-configuration.
	 *
	 * @return void
	 */
	public function show_process_end( string $config_name ): void {
		// bail if this is not our setup.
		if ( $config_name !== $this->get_setup_name() ) {
			return;
		}

		$completed_text = __( 'Setup has been run. Your positions from Personio has been imported. Click on "Completed" to view them in an intro.', 'personio-integration-light' );
		/**
		 * Filter the text for display if setup has been run.
		 *
		 * @since 3.0.0 Available since 3.0.0
		 * @param string $completed_text The text to show.
		 * @param string $config_name The name of the setup-configuration used.
		 */
		$this->set_process_label( apply_filters( 'personio_integration_setup_process_completed_text', $completed_text, $config_name ) );

		// set steps to max steps.
		$this->update_process_step( $this->get_max_step() );
	}

	/**
	 * Run additional tasks if setup has been marked as completed.
	 *
	 * @param string $config_name The name of the setup-configuration.
	 *
	 * @return void
	 */
	public function set_completed( string $config_name ): void {
		// bail if this is not our setup.
		if ( $this->get_setup_name() !== $config_name ) {
			return;
		}

		// bail if this is not a request from API.
		if ( ! Helper::is_rest_request() ) {
			return;
		}

		/**
		 * Run additional tasks if setup is marked as completed.
		 *
		 * @since 4.0.0 Available since 4.0.0.
		 */
		do_action( 'personio_integration_light_setup_completed' );

		// return JSON with forward-URL.
		wp_send_json(
			array(
				'forward' => Intro::get_instance()->get_start_url(),
			)
		);
	}

	/**
	 * If Personio URL is set do not run the setup.
	 *
	 * @param bool   $is_completed Whether to run setup (true) or not (false).
	 * @param string $config_name The name of the used setup-configuration.
	 *
	 * @return bool
	 */
	public function check_completed_value( bool $is_completed, string $config_name ): bool {
		// bail if this is not our setup.
		if ( $this->get_setup_name() !== $config_name ) {
			return $is_completed;
		}

		if ( Helper::is_personio_url_set() ) {
			return true;
		}

		return $is_completed;
	}

	/**
	 * Return name for the setup configuration.
	 *
	 * @return string
	 */
	public function get_setup_name(): string {
		return 'personio-integration-light';
	}

	/**
	 * Return the actual max steps.
	 *
	 * @return int
	 */
	public function get_max_step(): int {
		return absint( get_option( 'esfw_max_steps' ) );
	}

	/**
	 * Uninstall the setup.
	 *
	 * @return void
	 */
	public function uninstall(): void {
		\easySetupForWordPress\Setup::get_instance()->uninstall( $this->get_setup_name() );
	}

	/**
	 * Add own JS and CSS for backend.
	 *
	 * @return void
	 */
	public function add_js_and_css(): void {
		Import::get_instance()->add_script( 'appearance_page_easy-settings-for-wordpress' );
	}
}
