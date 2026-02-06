<?php
/**
 * File to handle import extensions for this plugin.
 *
 * @package wp-personio-integration
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use cli\progress\Bar;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Emails\ImportError;
use WP_CLI\NoOp;
use WP_Error;

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
	 * Name if the setting tab where the setting field is visible.
	 *
	 * @var string
	 */
	protected string $setting_tab = 'import';

	/**
	 * Variable for the instance of this Singleton object.
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
		$this->add_the_settings();
	}

	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {
		add_filter( 'personio_integration_light_extension_state_changed_dialog', array( $this, 'add_hint_after_enabling' ), 10, 2 );
	}

	/**
	 * Run the import.
	 *
	 * @return void
	 */
	public function run(): void {}

	/**
	 * Add an error with the given text to the list of errors.
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
	 * Send a list of errors via email to the configured admin-e-mail if debug is not enabled.
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

		// collect the error texts for the email and WP_CLI.
		$message = array();

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
			$message[] = $text;
		}

		// set errors in the list for response.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_ERRORS, $message );

		// create the email object.
		$email = new ImportError();
		$email->set_errors( $message );
		$email->send();

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
	 * Set a maximum count for the running import.
	 *
	 * @param int $max_count New count value.
	 *
	 * @return void
	 */
	public function set_import_max_count( int $max_count ): void {
		// set it in DB for frontend.
		update_option( WP_PERSONIO_INTEGRATION_OPTION_MAX, $max_count );

		// set it in an object for WP CLI.
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

	/**
	 * Return whether this import can be run.
	 *
	 * @return bool
	 */
	public function can_be_run(): bool {
		return false;
	}

	/**
	 * Return a complete position object with data from a source object.
	 *
	 * @param object $xml_object The source object.
	 * @param string $language_name The used language.
	 * @param string $personio_url The used Personio URL.
	 *
	 * @return Position
	 */
	public function get_position_from_object( object $xml_object, string $language_name, string $personio_url ): Position {
		return new Position( 0 );
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
	 * Extend the dialog after enabling this extension with hints to usage.
	 *
	 * @param array<string,mixed> $dialog The dialog.
	 * @param Extensions_Base     $extension The changed extension.
	 *
	 * @return array<string,mixed>
	 */
	public function add_hint_after_enabling( array $dialog, Extensions_Base $extension ): array {
		// bail if this is not this extension.
		if ( $this->get_name() !== $extension->get_name() ) {
			return $dialog;
		}

		// bail if status is disabled.
		if ( ! $extension->is_enabled() ) {
			// bail if another extension is enabled.
			if ( Imports::get_instance()->is_one_extension_enabled() ) {
				return $dialog;
			}

			// add hint.
			/* translators: %1$s will be replaced by a URL. */
			$dialog['texts'][] = '<p><strong>' . sprintf( __( 'There is no import extension for Personio positions enabled!', 'personio-integration-light' ) . '</strong> ' . __( 'Please <a href="%1$s">go to the list of import extensions</a> and enable one to import and update your positions on your website.', 'personio-integration-light' ), esc_url( Extensions::get_instance()->get_link( 'imports' ) ) ) . '</p>';

			// return the dialog.
			return $dialog;
		}

		// add hint.
		$dialog['texts'][] = '<p>' . __( 'Follow these steps to use this import extension.', 'personio-integration-light' ) . '</p>';
		/* translators: %1$s will be replaced by a URL. */
		$list  = '<ol><li>' . sprintf( __( 'Check the <a href="%1$s">settings</a> for imports.', 'personio-integration-light' ), Helper::get_settings_url( 'personioPositions', 'import' ) ) . '</li>';
		$list .= '<li>' . __( 'Run the import of positions.', 'personio-integration-light' ) . '</li>';
		/* translators: %1$s will be replaced by a URL. */
		$list             .= '<li>' . sprintf( __( 'Go to the <a href="%1$s">list of positions</a>.', 'personio-integration-light' ), esc_url( PersonioPosition::get_instance()->get_link() ) ) . '</li></ol>';
		$dialog['texts'][] = $list;

		// return resulting dialog.
		return $dialog;
	}
}
