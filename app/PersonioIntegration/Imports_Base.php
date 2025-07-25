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
use PersonioIntegrationLight\Plugin\Email_Base;
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
	 * Name of the settings-page where the tab resides.
	 *
	 * @var string
	 */
	protected string $setting_page = 'personioPositions';

	/**
	 * Name if the setting tab where the setting field is visible.
	 *
	 * @var string
	 */
	protected string $setting_tab = 'import';

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
	public function init(): void {}

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

		// output results in WP-CLI.
		if ( Helper::is_cli() ) {
			\WP_CLI::error( wp_json_encode( $message ) );
		}

		// set errors in list for response.
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
