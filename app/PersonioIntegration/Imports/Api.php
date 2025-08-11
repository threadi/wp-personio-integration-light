<?php
/**
 * File for handling API imports of positions from Personio.
 *
 * @source https://developer.personio.de/reference/get_v2-recruiting-jobs
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\PersonioIntegration\Imports;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use JsonException;
use PersonioIntegrationLight\Helper;
use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\Api_Request;
use PersonioIntegrationLight\PersonioIntegration\Imports_Base;
use PersonioIntegrationLight\PersonioIntegration\Personio;
use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use stdClass;
use WP_Post;

/**
 * Object to handle import of positions from Personio via API.
 */
class Api extends Imports_Base {
	/**
	 * The internal name of this extension.
	 *
	 * @var string
	 */
	protected string $name = 'api_import';

	/**
	 * Name of the setting field which defines its state.
	 *
	 * @var string
	 */
	protected string $setting_field = 'personioIntegrationApiImport';

	/**
	 * Internal name of the used category.
	 *
	 * @var string
	 */
	protected string $extension_category = 'imports';

	/**
	 * List of all positions this import is handing.
	 *
	 * @var array<string,array<string,mixed>>
	 */
	private array $positions = array();

	/**
	 * Variable for instance of this Singleton object.
	 *
	 * @var ?Api
	 */
	protected static ?Api $instance = null;

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() {}

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Api {
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
		return true;
	}

	/**
	 * Run the import of positions.
	 *
	 * @return void
	 */
	public function run(): void {
		// if debug mode is enabled log this event.
		if ( 1 === absint( get_option( 'personioIntegration_debug', 0 ) ) ) {
			Log::get_instance()->add( __( 'Import of positions is now running.', 'personio-integration-light' ), 'success', 'import' );
		}

		// get the API object.
		$api_obj = \PersonioIntegrationLight\PersonioIntegration\Api::get_instance();

		// get the access token.
		$access_token = $api_obj->get_access_token();

		// bail if no token is set.
		if ( empty( $access_token ) ) {
			// log this as error.
			$this->add_error( __( 'No Access Token for API available. Import from API will not run.', 'personio-integration-light' ) );

			// do nothing more.
			return;
		}

		// set mark that import is running in WP.
		define( 'WP_IMPORTING', true );

		// mark process as running import.
		define( 'PERSONIO_INTEGRATION_IMPORT_RUNNING', 1 );

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

		// define our custom header for this API request.
		add_filter( 'personio_integration_light_request_header', array( $api_obj, 'set_api_request_header' ) );

		// set the starting URL.
		$url = 'https://api.personio.de/v2/recruiting/jobs?limit=200';

		// set some counter.
		$imported_positions = 0;
		$this->set_import_count( 0 );
		$this->set_import_max_count( 0 );

		/**
		 * Use a loop to get all positions.
		 *
		 * We use 10 as limit to get max. (10*200=)2000 open positions and to not reach the limit
		 * for 150 requests per minute for the most projects.
		 */
		$max_iterations = 100;
		for ( $i = 1;$i < $max_iterations;$i++ ) {
			// create request to get the open positions.
			$request_object = new Api_Request();
			$request_object->set_url( $url );
			$request_object->set_post_data( '' );
			$request_object->set_method( 'GET' );
			$request_object->set_md5( md5( $url ) );

			// send it.
			if ( ! $request_object->send() ) {
				// if it was not successfully, get the occurred errors from the request object.
				foreach ( $request_object->get_errors() as $error ) {
					$this->add_error( $error );
				}

				// do nothing more.
				continue;
			}

			// bail if response is not 200.
			if ( 200 !== $request_object->get_http_status() ) {
				// break the loop.
				$i = $max_iterations;

				// do nothing more.
				continue;
			}

			// get the response content.
			$body = $request_object->get_response();

			// convert it to array.
			$positions = json_decode( $body, true );

			// bail if list is empty.
			if ( empty( $positions ) ) {
				$this->add_error( __( 'Got empty response for positions from Personio API.', 'personio-integration-light' ) );
				continue;
			}

			// bail if "_data" is missing. This should happen in any step > 1 OR if no positions are available in Personio.
			if ( empty( $positions['_data'] ) ) {
				// break the loop.
				$i = $max_iterations;

				// do nothing more.
				continue;
			}

			// add the positions to the list.
			foreach ( $positions['_data'] as $position ) {
				$this->add_position( $position['id'] );
			}

			// bail if next is missing.
			if ( empty( $positions['_meta']['links']['next']['href'] ) ) {
				// break the loop.
				$i = $max_iterations;

				// add the error.
				$this->add_error( __( 'Response from Personio API is missing the "next"-link entry:', 'personio-integration-light' ) . ' <code>' . wp_json_encode( $positions ) . '</code>' );

				// do nothing more.
				continue;
			}

			// get the "next" URL which will be used in next loop.
			$url = $positions['_meta']['links']['next']['href'];
		}

		// bail if list of positions is empty.
		if ( empty( $this->get_positions() ) ) {
			return;
		}

		/**
		 * Get the data of all positions we collected in a loop.
		 *
		 * @source https://developer.personio.de/reference/get_v2-recruiting-jobs-id
		 */
		// loop through the positions and get their data.
		foreach ( $this->get_positions() as $personio_id => $position_data ) {
			// bail if position data are not empty.
			if ( ! empty( $position_data ) ) {
				continue;
			}

			// create request to get the open positions.
			$request_object = new Api_Request();
			$request_object->set_url( 'https://api.personio.de/v2/recruiting/jobs/' . $personio_id );
			$request_object->set_post_data( '' );
			$request_object->set_method( 'GET' );
			$request_object->set_md5( md5( $url ) );

			// send it.
			if ( ! $request_object->send() ) {
				// if it was not successfully, get the occurred errors from the request object.
				foreach ( $request_object->get_errors() as $error ) {
					$this->add_error( $error );
				}

				// do nothing more.
				continue;
			}

			// bail if response is not 200.
			if ( 200 !== $request_object->get_http_status() ) {
				continue;
			}

			// get the response content.
			$body = $request_object->get_response();

			// convert it to array.
			try {
				$data = json_decode( $body, true, 512, JSON_THROW_ON_ERROR );
			} catch ( JsonException $e ) {
				$this->add_error( __( 'JSON-Error:', 'personio-integration-light' ) . ' <code>' . $e->getMessage() . '</code>' );
			}

			// bail if data is empty.
			if ( empty( $data ) ) {
				// add this as error.
				/* translators: %1$s will be replaced by the Personio ID. */
				$this->add_error( sprintf( __( 'Got no data for position %1$s from Personio API.', 'personio-integration-light' ), esc_html( $personio_id ) ) );

				// do nothing more.
				continue;
			}

			// marker to run import.
			$run_import = true;
			$language_name = 'de'; // TODO change this if language support is given by API.
			$object = new stdClass(); // TODO change this is API supports all necessary fields.
			$personio_obj = new Personio( get_option( 'personioIntegrationUrl' ) );
			$imports_obj = $this;

			/**
			 * Check the position before import.
			 *
			 * @noinspection PhpConditionAlreadyCheckedInspection
			 *
			 * @since 1.0.0 Available since first release.
			 *
			 * @param bool $run_import The individual text.
			 * @param object $xml_object The XML-object of the Position.
			 * @param string $language_name The language-marker.
			 * @param Personio $personio_obj The used Personio-account-object.
			 * @param Imports_Base $imports_obj The used imports object.
			 */
			if ( false !== apply_filters( 'personio_integration_import_single_position', $run_import, $object, $language_name, $personio_obj, $imports_obj ) ) {
				continue;
			}

			// add the result to the array.
			$this->positions[ $personio_id ] = $data;

			/**
			 * As of June 2025, we unfortunately receive almost no information on positions relevant to the website
			 * from API V2. As soon as this is available, it should be read out and imported here.
			 */

			// get the position object for this position.
			$position_obj = Positions::get_instance()->get_position_by_personio_id( $personio_id );

			// if no object could be loaded, create a new one.
			if ( ! $position_obj instanceof Position ) {
				$position_obj = new Position( 0 );
				$position_obj->set_personio_id( $personio_id );
			}

			// set the creation datetime.
			$position_obj->set_created_at( $data['created_at']['date-time'] );

			// set the title.
			$position_obj->set_title( $data['name'] );

			// set the job description.
			$position_obj->set_content_as_string( '{}' );

			/**
			 * Change the position-object before saving it.
			 *
			 * @since 5.0.0 Available since 5.0.0.
			 *
			 * @param Position $position_obj The object of this position.
			 * @param array $data The data from Personio.
			 */
			$position_obj = apply_filters( 'personio_integration_import_single_position_api', $position_obj, $data );

			// save the position object.
			try {
				$position_obj->save();
			} catch ( JsonException $e ) {
				$this->add_error( __( 'JSON-Error:', 'personio-integration-light' ) . ' <code>' . $e->getMessage() . '</code>' );
			}

			// update position counter.
			++$imported_positions;
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
		if( apply_filters( 'personio_integration_light_import_bail_before_cleanup', $false ) ) {
			return;
		}

		// handle the errors.
		if ( $this->has_errors() ) {
			$this->handle_errors();
			return;
		}

		// if no positions has been imported.
		if ( 0 === $imported_positions ) {
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
					Log::get_instance()->add( sprintf( __( 'Removing updated flag for %1$s failed.', 'personio-integration-light' ), esc_html( $personio_id ) ), 'error', 'import' );
				}
			} else {
				// delete this position from database without using trash.
				$result = wp_delete_post( $position_obj->get_id(), true );

				// if result is WP_Post, it has been deleted successfully.
				if ( $result instanceof WP_Post ) {
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
		Helper::is_cli() ? \WP_CLI::success( $imported_positions . ' positions imported.' ) : false;

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
	 * Return label of this extension.
	 *
	 * @return string
	 */
	public function get_label(): string {
		return __( 'Import via API', 'personio-integration-light' );
	}

	/**
	 * Return the description for this extension.
	 *
	 * @return string
	 */
	public function get_description(): string {
		if ( $this->can_be_enabled_by_user() ) {
			return esc_html__( 'Provides the import of positions from Personio via API. You can activate this extension because your WordPress is running in plugin developer mode. Note that the Personio API is still in beta mode and does not provide all the data the plugin needs. You will encounter errors that the plugin itself cannot solve for you.', 'personio-integration-light' );
		}
		return esc_html__( 'Provides the import of positions from Personio via API. This interface does not yet provide all the data required by the plugin. This extension will therefore only be available once the Personio API has reached a usable state.', 'personio-integration-light' );
	}

	/**
	 * Return whether this extension is enabled (true) or not (false).
	 *
	 * @return bool
	 */
	public function is_enabled(): bool {
		return 1 === absint( get_option( $this->get_settings_field_name() ) );
	}

	/**
	 * Add position to the list of all positions.
	 *
	 * @param string $personio_id The used Personio IO.
	 *
	 * @return void
	 */
	private function add_position( string $personio_id ): void {
		$this->positions[ $personio_id ] = array();
	}

	/**
	 * Return list of positions we collected.
	 *
	 * @return array<string,array<string,mixed>>
	 */
	private function get_positions(): array {
		return $this->positions;
	}

	/**
	 * Return whether this extension can be enabled by the user (true) or not (false).
	 *
	 * @return bool
	 */
	public function can_be_enabled_by_user(): bool {
		return function_exists( 'wp_is_development_mode' ) && wp_is_development_mode( 'plugin' );
	}
}
