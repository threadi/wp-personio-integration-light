<?php
/**
 * File for handling imports of positions from Personio.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\Plugin\Languages;
use SimpleXMLElement;
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
		// set mark that import is running in WP.
		define( 'WP_IMPORTING', true );

		// mark process as running import.
		define( 'PERSONIO_INTEGRATION_IMPORT_RUNNING', 1 );

		// do not import if it is already running in another process.
		if ( absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) > 0 ) ) {
			$this->errors[] = __( 'Import is already running.', 'personio-integration-light' );
		}

		// get and check the Personio URLs.
		$personio_urls = $this->get_personio_urls();
		if ( empty( $personio_urls ) ) {
			$this->errors[] = __( 'Personio URL not configured.', 'personio-integration-light' );
		}

		// check if PHP-extension SimpleXML exists.
		if ( ! function_exists( 'simplexml_load_string' ) ) {
			$this->errors[] = __( 'The PHP extension simplexml is missing on the system. Please contact your hoster about this.', 'personio-integration-light' );
		}

		// get the languages.
		$languages = Languages::get_instance()->get_active_languages();

		// check if languages are enabled.
		if ( empty( $languages ) ) {
			$this->errors[] = __( 'No active language configured. Please check your settings.', 'personio-integration-light' );
		}

		// bail if any error occurred.
		if ( $this->has_errors() ) {
			$this->handle_errors();
			return;
		}

		// set max counter.
		$language_count = count( $languages );

		// mark import as running with its start-time.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, time() );

		// set status.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_STATUS, __( 'Import starting ..', 'personio-integration-light' ) );

		// reset list of errors during import.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_ERRORS, array() );

		// reset import counter.
		update_option( WP_PERSONIO_INTEGRATION_OPTION_COUNT, 0 );

		/**
		 * Run custom actions before import of positions is running.
		 *
		 * @since 3.0.0 Available since release 3.0.0.
		 */
		do_action( 'personio_integration_import_starting' );

		// set some counter.
		$imported_positions = 0;
		$this->set_import_count( 0 );
		$this->set_import_max_count( 0 );

		// run the imports in loops through Personio URLs and active languages.
		foreach ( $personio_urls as $import_url ) {
			// loop through the languages.
			foreach ( $languages as $language_name => $label ) {
				// run the import for this language on this Personio URL.
				$import_obj = new Import();
				$import_obj->set_imports_object( $this );
				$import_obj->set_language( $language_name );
				$import_obj->set_url( $import_url );
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
			if ( 0 === $imported_positions ) {
				// output success-message.
				Helper::is_cli() ? \WP_CLI::success( 'Import has been run but no changes have been imported.' ) : false;

				// mark import as not running anymore.
				update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 );

				// set status.
				update_option( WP_PERSONIO_INTEGRATION_IMPORT_STATUS, __( 'Import completed.', 'personio-integration-light' ) );

				/**
				 * Run custom actions in this case.
				 *
				 * @since 3.0.4 Available since release 3.0.4.
				 */
				do_action( 'personio_integration_import_without_changes' );

				// do nothing more.
				return;
			}

			/**
			 * Run custom actions before cleanup of positions after import.
			 *
			 * @since 3.0.0 Available since release 3.0.0.
			 */
			do_action( 'personio_integration_import_before_cleanup' );

			// delete all not updated positions.
			foreach ( Positions::get_instance()->get_positions() as $position_obj ) {
				$do_delete = true;
				/**
				 * Check if this position should be deleted.
				 *
				 * @noinspection PhpConditionAlreadyCheckedInspection
				 *
				 * @since 1.0.0 Available since first release.
				 *
				 * @param bool $do_delete Marker to delete the position (must be true to check for deletion).
				 * @param Position $position_obj The position as object.
				 */
				if ( false !== apply_filters( 'personio_integration_delete_single_position', $do_delete, $position_obj ) ) {
					// get Personio ID.
					$personio_id = $position_obj->get_personio_id();
					if ( 1 === absint( get_post_meta( $position_obj->get_id(), WP_PERSONIO_INTEGRATION_UPDATED, true ) ) ) {
						if ( false === delete_post_meta( $position_obj->get_id(), WP_PERSONIO_INTEGRATION_UPDATED ) ) {
							// log event.
							/* translators: %1$s will be replaced by the PersonioId. */
							$this->log->add_log( sprintf( __( 'Removing updated flag for %1$s failed.', 'personio-integration-light' ), esc_html( $personio_id ) ), 'error', 'import' );
						}
					} else {
						// delete this position from database without using trash.
						$result = wp_delete_post( $position_obj->get_id(), true );

						if ( $result instanceof WP_Post ) {
							// log this event.
							/* translators: %1$s will be replaced by the PersonioID. */
							$this->log->add_log( sprintf( __( 'Position %1$s has been deleted as it was not updated during the last import run.', 'personio-integration-light' ), esc_html( $personio_id ) ), 'success', 'import' );
						} else {
							// log event.
							/* translators: %1$s will be replaced by the PersonioID. */
							$this->log->add_log( sprintf( __( 'Removing of not updated position %1$s failed.', 'personio-integration-light' ), esc_html( $personio_id ) ), 'error', 'import' );
						}
					}
				}
			}

			/**
			 * Run custom actions after import of positions has been done without errors.
			 *
			 * @since 2.0.0 Available since release 2.0.0.
			 */
			do_action( 'personio_integration_import_ended' );

			// output success-message.
			Helper::is_cli() ? \WP_CLI::success( $language_count . ' languages grabbed, ' . $imported_positions . ' positions imported.' ) : false;
		} else {
			// document errors.
			update_option( WP_PERSONIO_INTEGRATION_IMPORT_ERRORS, $this->errors );

			// handle errors.
			$this->handle_errors();
		}

		// refresh permalinks.
		update_option( 'personio_integration_update_slugs', 1 );

		// define step-count that has been run.
		$step = 1;

		/**
		 * Run custom actions after finished import of positions.
		 *
		 * @since 3.0.0 Available since release 3.0.0.
		 *
		 * @param int $step The step to add.
		 */
		do_action( 'personio_integration_import_finished', $step );

		// mark import as not running anymore.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 );

		// set status.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_STATUS, __( 'Import completed.', 'personio-integration-light' ) );
	}

	/**
	 * Return list of Personio URLs which should be used to import positions.
	 *
	 * The array contains the URLs as strings.
	 *
	 * @return array
	 */
	public function get_personio_urls(): array {
		// define list of Personio URLs.
		$personio_urls = array();

		// add the configured Personio URL, if set.
		if ( Helper::is_personio_url_set() ) {
			$personio_urls[] = Helper::get_personio_url();
		}

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
			// convert array to string for output.
			$ausgabe = implode( "\n", $this->errors );

			// save results in database.
			$log = new Log();
			$log->add_log( $ausgabe, 'error', 'import' );

			// output results in WP-CLI.
			if ( Helper::is_cli() ) {
				\WP_CLI::success( trim( $ausgabe ), 'error' );
			}

			// send info to admin about the problem if debug is disabled.
			if ( 1 !== absint( get_option( 'personioIntegration_debug' ) ) ) {
				$send_to = get_bloginfo( 'admin_email' );
				$subject = get_bloginfo( 'name' ) . ': ' . __( 'Error during Import of positions from Personio', 'personio-integration-light' );
				$msg     = __( 'The following error occurred when importing positions provided by Personio:', 'personio-integration-light' ) . '<br><br>' . nl2br( $ausgabe );
				$msg    .= '<br><br>' . __( 'Sent by the plugin Personio Integration Light', 'personio-integration-light' );
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
	 * Return whether errors occurred during the running import (true) or not (false).
	 *
	 * @return bool
	 */
	private function has_errors(): bool {
		return ! empty( $this->get_errors() );
	}

	/**
	 * Return errors that happened during imports.
	 *
	 * @return array
	 */
	private function get_errors(): array {
		return $this->errors;
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

	/**
	 * Import single position.
	 *
	 * This is the main function which defines the object during import of position data.
	 * It calls the save-function to add all in the DB.
	 *
	 * @param SimpleXMLElement|null $position      The XML-object of a single position.
	 * @param string                $language_name The language-name.
	 * @param string                $personio_url The used Personio URL.
	 *
	 * @return Position
	 * @noinspection PhpUnused
	 */
	public function import_single_position( ?SimpleXMLElement $position, string $language_name, string $personio_url ): Position {
		// create position object to handle all values and save them to database.
		$position_object = new Position( 0 );
		$position_object->set_lang( $language_name );
		$position_object->set_title( (string) $position->name );
		$position_object->set_content( $position->jobDescriptions );
		if ( ! empty( $position->department ) ) {
			$position_object->set_department( (string) $position->department );
		}
		if ( ! empty( $position->keywords ) ) {
			$position_object->set_keywords( (string) $position->keywords );
		}
		$position_object->set_office( (string) $position->office );
		$position_object->set_personio_id( (int) $position->id );
		$position_object->set_recruiting_category( (string) $position->recruitingCategory );
		$position_object->set_employment_type( (string) $position->employmentType );
		$position_object->set_seniority( (string) $position->seniority );
		$position_object->set_schedule( (string) $position->schedule );
		$position_object->set_years_of_experience( (string) $position->yearsOfExperience );
		$position_object->set_occupation( (string) $position->occupation );
		$position_object->set_occupation_category( (string) $position->occupationCategory );
		$position_object->set_created_at( (string) $position->createdAt );
		/**
		 * Change the XML-object before saving the position.
		 *
		 * @since 1.0.0 Available since first release.
		 *
		 * @param Position $position_object The object of this position.
		 * @param object $position The XML-object with the data from Personio.
		 * @param string $personio_url The used Personio-URL.
		 */
		$position_object = apply_filters( 'personio_integration_import_single_position_xml', $position_object, $position, $personio_url );
		$position_object->save();

		// return the resulting position object.
		return $position_object;
	}

	/**
	 * Reset the Personio settings complete.
	 *
	 * @return void
	 *
	 * @noinspection PhpUnused
	 */
	public function reset_personio_settings(): void {
		foreach ( $this->get_personio_urls() as $personio_url ) {
			$personio_obj = new Personio( $personio_url );
			foreach ( Languages::get_instance()->get_languages() as $language_name => $label ) {
				$personio_obj->remove_timestamp( $language_name );
				delete_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION . $language_name );
				$personio_obj->remove_md5( $language_name );
			}
		}
	}
}
