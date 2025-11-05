<?php
/**
 * File for handling XML imports of positions from Personio.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\Imports;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use Error;
use JsonException;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\Imports\Xml\Import_Single_Personio_Url;
use PersonioIntegrationLight\PersonioIntegration\Personio_Accounts;
use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\Plugin\Languages;
use PersonioIntegrationLight\PersonioIntegration\Imports_Base;
use SimpleXMLElement;
use WP_Post;

/**
 * Object to handle import of positions from Personio via XML.
 */
class Xml extends Imports_Base {
	/**
	 * The internal name of this extension.
	 *
	 * @var string
	 */
	protected string $name = 'xml_import';

	/**
	 * Name of the setting field which defines its state.
	 *
	 * @var string
	 */
	protected string $setting_field = 'personioIntegrationXmlImport';

	/**
	 * Internal name of the used category.
	 *
	 * @var string
	 */
	protected string $extension_category = 'imports';

	/**
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Xml
	 */
	protected static ?Xml $instance = null;

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Xml {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Return whether this import can be run.
	 *
	 * @return bool
	 */
	public function can_be_run(): bool {
		return 0 === absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) );
	}

	/**
	 * Run the import of positions from Personio.
	 *
	 * @return void
	 */
	public function run(): void {
		// if debug mode is enabled log this event.
		if ( 1 === absint( get_option( 'personioIntegration_debug', 0 ) ) ) {
			Log::get_instance()->add( __( 'Import of positions is now running.', 'personio-integration-light' ), 'success', 'import' );
		}

		// set mark that import is running in WP.
		define( 'WP_IMPORTING', true );

		// mark process as running import.
		define( 'PERSONIO_INTEGRATION_IMPORT_RUNNING', 1 );

		// do not import if it is already running in another process.
		if ( ! $this->can_be_run() ) {
			$this->add_error( __( 'Import is already running. Please wait a moment until it is finished.', 'personio-integration-light' ) );
		}

		// get and check the Personio URLs.
		$personio_urls = Personio_Accounts::get_instance()->get_personio_urls();
		if ( empty( $personio_urls ) ) {
			/* translators: %1$s will be replaced by the URL for main settings. */
			$this->add_error( sprintf( __( 'Personio URL not configured. Please check your <a href="%1$s">settings</a>.', 'personio-integration-light' ), esc_url( Helper::get_settings_url() ) ) );
		}

		// check if PHP-extension SimpleXML exists.
		if ( ! function_exists( 'simplexml_load_string' ) ) {
			$this->add_error( __( 'The PHP extension simplexml is missing on the system. Please contact your hoster about this.', 'personio-integration-light' ) );
		}

		// get the active languages.
		$languages = Languages::get_instance()->get_active_languages();

		// check if languages are enabled.
		if ( empty( $languages ) ) {
			/* translators: %1$s will be replaced by the URL for main settings. */
			$this->add_error( sprintf( __( 'No active language configured. Please check your <a href="%1$s">settings</a>.', 'personio-integration-light' ), esc_url( Helper::get_settings_url() ) ) );
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

		$instance = $this;
		/**
		 * Run custom actions before import of positions is running.
		 *
		 * @since 3.0.0 Available since release 3.0.0.
		 * @param Imports_Base $instance The import object.
		 */
		do_action( 'personio_integration_import_starting', $instance );

		// set some counter.
		$imported_positions = 0;
		$this->set_import_count( 0 );
		$this->set_import_max_count( 0 );

		try {
			// run the imports in loops through Personio URLs and active languages.
			foreach ( $personio_urls as $import_url ) {
				// loop through the languages.
				foreach ( $languages as $language_name => $label ) {
					// run the import for this language on this Personio URL.
					$import_obj = new Import_Single_Personio_Url();
					$import_obj->set_imports_object( $this );
					$import_obj->set_language( $language_name );
					$import_obj->set_url( $import_url );
					$import_obj->run();

					// add errors from import to global list.
					foreach ( $import_obj->get_errors() as $error ) {
						$this->add_error( $error );
					}

					// update counter for imported positions.
					$imported_positions += (int) count( $import_obj->get_imported_positions() );
				}
			}
		} catch ( Error $e ) {
			// log this event.
			Log::get_instance()->add( __( 'Following error occurred during import of positions via XML:', 'personio-integration-light' ) . '<br>' . __( 'Message:', 'personio-integration-light' ) . '<code>' . $e->getMessage() . '</code><br>' . __( 'Code:', 'personio-integration-light' ) . '<code>' . $e->getCode() . '</code><br>' . __( 'File:', 'personio-integration-light' ) . '<code>' . $e->getFile() . '</code><br>' . __( 'Line:', 'personio-integration-light' ) . '<code>' . $e->getLine() . '</code>', 'error', 'imports' );

			// show hint.
			/* translators: %1$s will be replaced by a URL. */
			$this->add_error( sprintf( __( 'Error occurred. Check <a href="%1$s">the log</a> for details.', 'personio-integration-light' ), esc_url( Helper::get_settings_url( 'personioPositions', 'logs' ) ) ) );
		}

		// finalize progress for WP CLI.
		$this->cli_progress ? $this->cli_progress->finish() : false;

		$false = false;
		/**
		 * Cancel the import before cleanup the database.
		 *
		 * @since 5.0.0 Available since 5.0.0.
		 * @param bool $false True to prevent the cleanup tasks.
		 *
		 * @noinspection PhpConditionAlreadyCheckedInspection
		 */
		if ( apply_filters( 'personio_integration_light_import_bail_before_cleanup', $false ) ) {
			// mark import as not running anymore.
			update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 );

			// do nothing more.
			return;
		}

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
			 * Run custom actions before cleanup of positions but after import.
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
				if ( false === apply_filters( 'personio_integration_delete_single_position', $do_delete, $position_obj ) ) {
					continue;
				}

				// get Personio ID.
				$personio_id = $position_obj->get_personio_id();

				// if this postion has been changed.
				if ( 1 === absint( get_post_meta( $position_obj->get_id(), WP_PERSONIO_INTEGRATION_UPDATED, true ) ) ) {
					// delete the marker.
					if ( false === delete_post_meta( $position_obj->get_id(), WP_PERSONIO_INTEGRATION_UPDATED ) ) {
						// log this event.
						/* translators: %1$s will be replaced by the PersonioId. */
						Log::get_instance()->add( sprintf( __( 'Removing the update flag for %1$s failed.', 'personio-integration-light' ), esc_html( $personio_id ) ), 'error', 'import' );
					}
				} else {
					// delete this position from database without using trash.
					$result = wp_delete_post( $position_obj->get_id(), true );

					// if result is WP_Post, it has been deleted successfully.
					if ( $result instanceof WP_Post ) {
						/**
						 * Run tasks if a position has been deleted.
						 *
						 * @since 5.0.0 Available since 5.0.0.
						 * @param string $personio_id The Personio ID of the position which has been deleted.
						 * @param Position $position_obj The position which has been deleted. Hint: do not use any DB-request via this object.
						 */
						do_action( 'personio_integration_light_import_deleted_position', $personio_id, $position_obj );

						// log this event.
						/* translators: %1$s will be replaced by the PersonioID. */
						Log::get_instance()->add( sprintf( __( 'Position %1$s has been deleted as it was not updated during the last import run.', 'personio-integration-light' ), esc_html( $personio_id ) ), 'success', 'import' );
					} else {
						// deletion failed, so log this event.
						/* translators: %1$s will be replaced by the PersonioID. */
						Log::get_instance()->add( sprintf( __( 'Removing of not updated position %1$s failed.', 'personio-integration-light' ), esc_html( $personio_id ) ), 'error', 'import' );
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
			update_option( WP_PERSONIO_INTEGRATION_IMPORT_ERRORS, $this->get_errors() );

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
	 * Import single position.
	 *
	 * This is the main function which defines the object during import of position data.
	 * It calls the save-function to add all in the DB.
	 *
	 * @param SimpleXMLElement $xml_object      The XML-object of a single position.
	 * @param string           $language_name The language-name.
	 * @param string           $personio_url The used Personio URL.
	 *
	 * @return Position
	 * @noinspection PhpUnused
	 */
	public function import_single_position( SimpleXMLElement $xml_object, string $language_name, string $personio_url ): Position {
		// get the position object from XML.
		$position_object = $this->get_position_from_object( $xml_object, $language_name, $personio_url );

		// and save it.
		try {
			$position_object->save();
		} catch ( JsonException $e ) {
			Log::get_instance()->add( __( 'Error during saving a position. The following error occurred:', 'personio-integration-light' ) . ' <code>' . $e->getMessage() . '</code>', 'error', 'import' );
		}

		// return the resulting position object.
		return $position_object;
	}

	/**
	 * Return a complete position object with data from source object.
	 *
	 * @param object $xml_object The source object.
	 * @param string $language_name The used language.
	 * @param string $personio_url The used Personio URL.
	 *
	 * @return Position
	 */
	public function get_position_from_object( object $xml_object, string $language_name, string $personio_url ): Position {
		// bail if object is not a SimpleXMLElement.
		if ( ! $xml_object instanceof SimpleXMLElement ) {
			return new Position( 0 );
		}

		// create position object to handle all values and save them to database.
		$position_object = new Position( 0 );
		$position_object->set_lang( $language_name );
		$position_object->set_title( (string) $xml_object->name );
		$position_object->set_content( $xml_object->jobDescriptions );
		if ( ! empty( $xml_object->department ) ) {
			$position_object->set_department( (string) $xml_object->department );
		}
		if ( ! empty( $xml_object->keywords ) ) {
			$position_object->set_keywords( (string) $xml_object->keywords );
		}
		$position_object->set_office( (string) $xml_object->office );
		$position_object->set_personio_id( (string) $xml_object->id );
		$position_object->set_recruiting_category( (string) $xml_object->recruitingCategory );
		$position_object->set_employment_type( (string) $xml_object->employmentType );
		$position_object->set_seniority( (string) $xml_object->seniority );
		$position_object->set_schedule( (string) $xml_object->schedule );
		$position_object->set_years_of_experience( (string) $xml_object->yearsOfExperience );
		$position_object->set_occupation( (string) $xml_object->occupation );
		$position_object->set_occupation_category( (string) $xml_object->occupationCategory );
		$position_object->set_created_at( (string) $xml_object->createdAt );
		/**
		 * Change the XML-object before saving the position.
		 *
		 * @since 1.0.0 Available since first release.
		 *
		 * @param Position $position_object The object of this position.
		 * @param SimpleXMLElement $xml_object The XML-object with the data from Personio.
		 * @param string $personio_url The used Personio-URL.
		 */
		return apply_filters( 'personio_integration_import_single_position_xml', $position_object, $xml_object, $personio_url );
	}

	/**
	 * Return label of this extension.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Import via XML', 'personio-integration-light' );
	}

	/**
	 * Return the description for this extension.
	 *
	 * @return string
	 */
	public function get_description(): string {
		// show hint if required PHP-modul is missing.
		if ( ! $this->can_be_enabled_by_user() ) {
			return __( 'The PHP extension simplexml is missing on you hosting. Please contact your hoster about this. This import can only be used if it is available in the hosting.', 'personio-integration-light' );
		}
		return esc_html__( 'Provides the import of positions from Personio via XML interface.', 'personio-integration-light' );
	}

	/**
	 * Whether this extension is enabled by default (true) or not (false).
	 *
	 * @return bool
	 */
	protected function is_default_enabled(): bool {
		return $this->can_be_enabled_by_user();
	}

	/**
	 * Return whether this extension is enabled (true) or not (false).
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return 1 === absint( get_option( $this->get_settings_field_name() ) ) && $this->can_be_enabled_by_user();
	}

	/**
	 * Return whether this extension can be enabled by the user (true) or not (false).
	 *
	 * @return bool
	 */
	public function can_be_enabled_by_user(): bool {
		return function_exists( 'simplexml_load_string' );
	}
}
