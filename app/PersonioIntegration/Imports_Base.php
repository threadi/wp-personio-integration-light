<?php
/**
 * File to handle import extensions for this plugin.
 *
 * @package wp-personio-integration
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
use cli\progress\Bar;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Button;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Fields\Checkbox;
use PersonioIntegrationLight\Dependencies\easySettingsForWordPress\Settings;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use WP_CLI\NoOp;
use WP_Error;

defined( 'ABSPATH' ) || exit;

/**
 * Handles import extensions for this plugin.
 */
class Imports_Base extends Extensions_Base {
	/**
	 * List of errors during an import run.
	 *
	 * @var array<int,WP_Error>
	 */
	private array $errors = array();

	/**
	 * WP CLI object.
	 *
	 * @var bool|Bar|NoOp
	 */
	protected bool|Bar|NoOp $cli_progress = false;

	/**
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Imports_Base
	 */
	private static ?Imports_Base $instance = null;

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Imports_Base {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Tasks to run during plugin activation for this extension.
	 *
	 * @return void
	 */
	public function activation(): void {
		$this->add_settings();
	}

	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'init', array( $this, 'add_settings' ), 20 );
	}

	/**
	 * Tasks to run during plugin activation for this extension.
	 *
	 * @return void
	 */
	public function add_settings(): void {
		// get settings object.
		$settings_obj = Settings::get_instance();

		// bail if import tab is already set.
		if ( $settings_obj->get_tab( 'import' ) ) {
			return;
		}

		// add the import tab.
		$import_tab = $settings_obj->add_tab( 'import' );
		$import_tab->set_title( __( 'Import', 'personio-integration-light' ) );
		$import_tab->set_position( 1 );

		// add main section.
		$import_section = $import_tab->add_section( 'settings_section_import' );
		$import_section->set_title( __( 'Import of positions from Personio', 'personio-integration-light' ) );
		$import_section->set_setting( $settings_obj );

		// add other section.
		$import_other_section = $import_tab->add_section( 'settings_section_import_other' );
		$import_other_section->set_title( __( 'Other settings', 'personio-integration-light' ) );
		$import_other_section->set_setting( $settings_obj );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationImportNow' );
		$setting->set_section( $import_section );
		$setting->set_autoload( false );
		$setting->prevent_export( true );
		$field = new Button();
		$field->set_title( __( 'Get open positions from Personio', 'personio-integration-light' ) );
		$field->set_button_title( __( 'Run import of positions now', 'personio-integration-light' ) );
		$field->add_class( 'personio-integration-import-hint' );
		$setting->set_field( $field );

		// add setting.
		$setting = $settings_obj->add_setting( 'personioIntegrationDeleteNow' );
		$setting->set_section( $import_section );
		$setting->set_autoload( false );
		$setting->prevent_export( true );
		$field = new Button();
		$field->set_title( __( 'Delete local positions', 'personio-integration-light' ) );
		$field->set_button_title( __( 'Delete all positions', 'personio-integration-light' ) );
		$field->add_class( 'personio-integration-delete-all' );
		$setting->set_field( $field );

		// add setting.
		/* translators: %1$s will be replaced by a link to the Pro plugin page. */
		$pro_hint                 = __( 'Use more import options with the %1$s. Among other things, you get the possibility to change the time interval for imports and partial imports of very large position lists.', 'personio-integration-light' );
		$true                     = true;
		$automatic_import_setting = $settings_obj->add_setting( 'personioIntegrationEnablePositionSchedule' );
		$automatic_import_setting->set_section( $import_section );
		$automatic_import_setting->set_type( 'integer' );
		$automatic_import_setting->set_default( 1 );
		$automatic_import_setting->set_save_callback( array( 'PersonioIntegrationLight\Plugin\Admin\SettingsSavings\Import', 'save' ) );
		$field = new Checkbox();
		$field->set_title( __( 'Enable automatic import', 'personio-integration-light' ) );
		$field->set_description( __( 'The automatic import is run once per day. You don\'t have to worry about updating your jobs on the website yourself.', 'personio-integration-light' ) . apply_filters( 'personio_integration_admin_show_pro_hint', $pro_hint, $true ) );
		$automatic_import_setting->set_field( $field );
	}

	/**
	 * Run the import.
	 *
	 * @return void
	 */
	public function run(): void {}

	/**
	 * Add an error with given text to the list of errors.
	 *
	 * @param string|WP_Error $text The error text or WP_Error object.
	 *
	 * @return void
	 */
	protected function add_error( string|WP_Error $text ): void {
		// create the WP_Error object with the text.
		if ( is_string( $text ) ) {
			$error = new WP_Error();
			$error->add_data( $text );
		} else {
			$error = $text;
		}

		// add error to the list.
		$this->errors[] = $error;
	}

	/**
	 * Return the list of errors.
	 *
	 * @return array<int,WP_Error>
	 */
	public function get_errors(): array {
		return $this->errors;
	}

	/**
	 * Return whether errors occurred during the running import (true) or not (false).
	 *
	 * @return bool
	 */
	protected function has_errors(): bool {
		return ! empty( $this->get_errors() );
	}

	/**
	 * Handle errors during the import.
	 *
	 * Logs resulting errors.
	 *
	 * Send list of errors via email to configured admin-e-mail if debug is not enabled.
	 *
	 * @return void
	 */
	protected function handle_errors(): void {
		// bail on no errors.
		if ( empty( $this->get_errors() ) ) {
			return;
		}

		// get the log object.
		$log = Log::get_instance();

		// collect the string for the email and WP_CLI.
		$message = '';

		// loop through the errors.
		foreach ( $this->get_errors() as $error ) {
			// collect the error string.
			$text = '';

			// get the error message, if set.
			if ( ! empty( $error->get_error_message() ) ) {
				$text = $error->get_error_message();
			}

			// get error data, if set.
			if ( ! empty( $error->get_error_data() ) ) {
				$text = $error->get_error_data();
			}

			// add log entry.
			$log->add( $text, 'error', 'import' );

			// add text to string for email and WP_CLI.
			$message .= "\n\n" . $text;
		}

		// output results in WP-CLI.
		if ( Helper::is_cli() ) {
			\WP_CLI::error( trim( $message ) );
		}

		// set errors in list for response.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_ERRORS, nl2br( $message ) );

		// send info to admin about the problem if debug is disabled.
		if ( 1 !== absint( get_option( 'personioIntegration_debug' ) ) ) {
			/* translators: %1$s will be replaced by a URL. */
			$support_part  = '<br><br>' . sprintf( __( 'If you have any questions about the message, please feel free to contact us in <a href="%1$s">our support forum</a>.', 'personio-integration-light' ), esc_url( Helper::get_plugin_support_url() ) );
			$support_part .= '<br><br>' . __( 'This hint was sent to by the WordPress-plugin Personio Integration Light', 'personio-integration-light' );
			/**
			 * Filter the support part of the email on import error.
			 *
			 * @since 4.1.0 Available since 4.1.0.
			 * @param string $support_part The text to use.
			 */
			$support_part = apply_filters( 'personio_integration_light_import_error_support_hint', $support_part );

			// set recipient.
			$send_to = get_bloginfo( 'admin_email' );

			// set subject.
			$subject = get_bloginfo( 'name' ) . ': ' . __( 'Error during Import of positions from Personio', 'personio-integration-light' );

			// set email text.
			$body  = __( 'The following error occurred when importing positions provided by Personio:', 'personio-integration-light' ) . '<br><br><em>' . nl2br( $message ) . '</em>';
			$body .= $support_part;

			// set headers.
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );

			// send email.
			wp_mail( $send_to, $subject, $body, $headers );
		}

		/**
		 * Run additional tasks for processing errors during import of positions.
		 *
		 * @since 4.0.0 Available since 4.0.0.
		 * @param array<int,WP_Error> $errors List of errors.
		 */
		do_action( 'personio_integration_light_import_error', $this->get_errors() );
	}

	/**
	 * Return actual import count.
	 *
	 * @return int
	 * @noinspection PhpUnused
	 */
	public function get_import_count(): int {
		return absint( get_option( WP_PERSONIO_INTEGRATION_OPTION_COUNT ) );
	}

	/**
	 * Add up the import count.
	 *
	 * @param int $count The value to add.
	 *
	 * @return void
	 */
	public function set_import_count( int $count ): void {
		// update for frontend.
		update_option( WP_PERSONIO_INTEGRATION_OPTION_COUNT, $this->get_import_count() + $count );

		// update for WP CLI.
		$this->cli_progress ? $this->cli_progress->tick() : false;

		/**
		 * Add actual count on third party components (like Setup).
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param int $count The value to add.
		 */
		do_action( 'personio_integration_import_count', $count );
	}

	/**
	 * Return max count for the running import.
	 *
	 * @return int
	 */
	public function get_import_max_count(): int {
		return absint( get_option( WP_PERSONIO_INTEGRATION_OPTION_MAX ) );
	}

	/**
	 * Set max count for the running import.
	 *
	 * @param int $max_count New count value.
	 *
	 * @return void
	 */
	public function set_import_max_count( int $max_count ): void {
		// set it in DB for frontend.
		update_option( WP_PERSONIO_INTEGRATION_OPTION_MAX, $max_count );

		// set it in object for WP CLI.
		$this->cli_progress = Helper::is_cli() ? \WP_CLI\Utils\make_progress_bar( 'Get positions from Personio by language', $this->get_import_max_count() ) : false;

		/**
		 * Add max count on third party components (like Setup).
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param int $max_count The max count to set.
		 */
		do_action( 'personio_integration_import_max_count', $max_count );
	}
}
