<?php
/**
 * File for handling imports of positions from Personio.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\Plugin\Languages;
use WP_Post;

/**
 * Object to handle positions.
 */
class Imports {

	/**
	 * Object for logging events.
	 *
	 * @var Log
	 */
	private Log $log;

	/**
	 * List of errors during an import run.
	 *
	 * @var array
	 */
	private array $errors = array();

	/**
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Imports
	 */
	protected static ?Imports $instance = null;

	/**
	 * WP CLI object.
	 *
	 * @var bool|\cli\progress\Bar
	 */
	private bool|\cli\progress\Bar $cli_progress = false;

	/**
	 * Constructor, not used as this a Singleton object.
	 */
	private function __construct() {
		$this->log = new Log();
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
	public static function get_instance(): Imports {
		if ( ! static::$instance instanceof static ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Run the import of positions.
	 *
	 * @return void
	 */
	public function run(): void {
		// do not import if it is already running in another process.
		if ( 1 === absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) ) ) {
			return;
		}

		// get the languages.
		$languages = Languages::get_instance()->get_active_languages();

		// check if Personio URL is set.
		if ( ! Helper::is_personio_url_set() ) {
			$this->errors[] = __( 'Personio URL not configured.', 'personio-integration-light' );
		}

		// check if simpleXML exists.
		if ( ! function_exists( 'simplexml_load_string' ) ) {
			$this->errors[] = __( 'The PHP extension simplexml is missing on the system. Please contact your hoster about this.', 'personio-integration-light' );
		}

		// check if simpleXML exists.
		if ( empty( $languages ) ) {
			$this->errors[] = __( 'No active language configured. Please check your settings.', 'personio-integration-light' );
		}

		// bail if any error occurred.
		if( $this->has_errors() ) {
			$this->handle_errors();
			return;
		}

		// get the Personio URLs.
		$personio_urls = $this->get_personio_urls();

		// set max counter.
		$language_count = count( $languages );

		// mark import as running with its start-time.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, time() );

		// reset list of errors during import.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_ERRORS, array() );

		// set some counter.
		$imported_positions = 0;
		$this->set_import_count( 0 );
		$this->set_import_max_count( 0 );

		/**
		 * Set max steps in external objects.
		 *
		 * @param int $language_count The steps to add.
		 *
		 * @since 3.0.0 Available since release 3.0.0.
		 */
		do_action( 'personio_integration_import_max_steps', $language_count );

		// loop through the Personio URLs.
		foreach( $personio_urls as $import_url ) {
			// loop through the languages.
			foreach ( $languages as $language_name => $label ) {
				// run the import for this language on this Personio URL.
				$import_obj = new Import();
				$import_obj->set_imports_object( $this );
				$import_obj->set_url( $import_url );
				$import_obj->set_language( $language_name );
				$import_obj->run();

				// add errors from import to global list.
				$this->add_errors( $import_obj->get_errors() );

				// update counter for imported positions.
				$imported_positions = $imported_positions + count( $import_obj->get_imported_positions() );
			}
		}

		// finalize progress for WP CLI.
		$this->cli_progress ? $this->cli_progress->finish() : false;

		// clean-up the database if no errors occurred.
		if ( ! $this->has_errors() ) {
			if( 0 === $imported_positions ) {
				// output success-message.
				Helper::is_cli() ? \WP_CLI::success( 'Import has been successful run but no changes has been imported.' ) : false;
				return;
			}

			// get Positions-object.
			$positions_object = Positions::get_instance();

			// delete all not updated positions.
			foreach ( $positions_object->get_positions() as $position_obj ) {
				$do_delete = true;
				/**
				 * Check if this position should be deleted.
				 *
				 * @noinspection PhpConditionAlreadyCheckedInspection
				 *
				 * @since 1.0.0 Available since first release.
				 *
				 * @param bool $do_delete Marker to delete the position.
				 * @param Position $position_obj The position as object.
				 */
				$do_delete = apply_filters( 'personio_integration_delete_single_position', $do_delete, $position_obj );

				if ( false !== $do_delete ) {
					// get Personio ID.
					$personio_id = $position_obj->get_personio_id();
					if ( 1 === absint( get_post_meta( $position_obj->get_id(), WP_PERSONIO_INTEGRATION_UPDATED, true ) ) ) {
						if ( false === delete_post_meta( $position_obj->get_id(), WP_PERSONIO_INTEGRATION_UPDATED ) ) {
							// log event.
							$this->log->add_log( sprintf( 'Removing updated flag for %1$s failed.', esc_html( $personio_id ) ), 'error' );
						}
					} else {
						// delete this position from database.
						$result = wp_delete_post( $position_obj->get_id(), true );

						if ( $result instanceof WP_Post ) {
							// log this event.
							$this->log->add_log( 'Position ' . $personio_id . ' has been deleted as it was not updated during the last import run.', 'success' );
						} else {
							// log event.
							$this->log->add_log( sprintf( 'Removing of not updated positions %1$s failed.', esc_html( $personio_id ) ), 'error' );
						}
					}
				}
			}

			/**
			 * Run custom actions after import of single Personio-URL has been done without errors.
			 *
			 * @since 2.0.0 Available since release 2.0.0.
			 */
			do_action( 'personio_integration_import_ended' );

			// output success-message.
			Helper::is_cli() ? WP_CLI::success( $language_count . ' languages grabbed, ' . $imported_positions . ' positions imported.' ) : false;

			// save actual position count.
			update_option( 'personioIntegrationPositionCount', $positions_object->get_positions_count() );
		} else {
			// document errors.
			update_option( WP_PERSONIO_INTEGRATION_IMPORT_ERRORS, $this->errors );

			// handle errors.
			$this->handle_errors();
		}

		// define step-count that has been run.
		$step = 1;

		/**
		 * Run custom actions after finished import of single Personio-URL.
		 *
		 * @since 3.0.0 Available since release 3.0.0.
		 *
		 * @param int $step The step to add.
		 */
		do_action( 'personio_integration_import_finished', $step );

		// mark import as not running anymore.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 );

	}

	/**
	 * Return list of Personio URLs which should be used to import positions.
	 *
	 * @return array
	 */
	public function get_personio_urls(): array {
		$personio_urls = array(
			Helper::get_personio_url()
		);

		/**
		 * Filter the list of Personio URLs used to import positions.
		 *
		 * @since 3.0.0 Available since 3.0.0.
		 *
		 * @param array $personio_urls List of Personio URLs.
		 */
		return apply_filters( 'personio_integration_personio_urls', $personio_urls );
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
	private function handle_errors(): void {
		if ( ! empty( $this->errors ) ) {
			$ausgabe = '';
			foreach ( $this->errors as $e ) {
				$ausgabe .= $e . '\n';
			}
			$ausgabe .= "\n";

			// save results in database.
			$log = new Log();
			$log->add_log( $ausgabe, 'error' );

			// output results in WP-CLI.
			if ( Helper::is_cli() ) {
				echo esc_html( $ausgabe );
			}

			// send info to admin about the problem if debug is not enabled.
			if ( 1 !== absint( get_option( 'personioIntegration_debug' ) ) ) {
				$send_to = get_bloginfo( 'admin_email' );
				$subject = get_bloginfo( 'name' ) . ': ' . __( 'Error during Import of Personio Positions', 'personio-integration-light' );
				$msg     = __( 'The following error occurred when importing positions provided by Personio:', 'personio-integration-light' ) . '\r\n' . $ausgabe;
				$msg    .= '\r\n\r\n' . __( 'Sent by the plugin Personio Integration', 'personio-integration-light' );
				wp_mail( $send_to, $subject, $msg );
			}
		}
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
	 * Set the import count.
	 *
	 * @param int $import_count The new count value.
	 *
	 * @return void
	 */
	public function set_import_count( int $import_count ): void {
		// update for frontend.
		update_option( WP_PERSONIO_INTEGRATION_OPTION_COUNT, $import_count );
		// update for WP CLI.
		$this->cli_progress ? $this->cli_progress->tick() : false;
	}

	/**
	 * Add collection of errors to the list.
	 *
	 * @param array $errors List of errors.
	 *
	 * @return void
	 */
	private function add_errors( array $errors ): void {
		$this->errors = array_merge( $this->get_errors(), $errors );
	}

	/**
	 * Return whether errors occurred during import (true) or not (false).
	 *
	 * @return bool
	 */
	private function has_errors(): bool {
		return ! empty( $this->get_errors() );
	}

	/**
	 * Return errors during imports.
	 *
	 * @return array
	 */
	private function get_errors(): array {
		return $this->errors;
	}

	/**
	 * Return max count for import.
	 *
	 * @return int
	 */
	public function get_import_max_count(): int {
		return absint( get_option( WP_PERSONIO_INTEGRATION_OPTION_MAX ) );
	}

	/**
	 * Set max count for import.
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
	}
}