<?php
/**
 * File for the abilities to control the import and the deletion of positions from Personio.
 *
 * @package personio-integration-light
 */

declare(strict_types=1);

namespace PersonioIntegrationLight\Abilities;

// prevent direct access.
\defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\Imports;
use PersonioIntegrationLight\PersonioIntegration\Imports\Xml;
use PersonioIntegrationLight\PersonioIntegration\Imports_Base;
use PersonioIntegrationLight\PersonioIntegration\Personio_Accounts;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;
use PersonioIntegrationLight\Plugin\Languages;
use Throwable;
use WP_Error;
use WP_User;

/**
 * Object to provide abilities to control the import and the deletion of positions from Personio (e.g. for AI).
 *
 * Together with "get-import-status" and "get-log" they cover the whole process: check the state, start an import,
 * follow its result, release an import which got stuck and remove all positions from WordPress. The positions are
 * still never edited: an import only fetches their actual state from Personio.
 */
class Import_Abilities {
	/**
	 * The time in seconds after which a running import is treated as stuck.
	 *
	 * The same limit is used in the settings, where the button to cancel an import is shown after one hour.
	 */
	private const STUCK_AFTER = 3600;

	/**
	 * Variable for the instance of this Singleton object.
	 *
	 * @var ?Import_Abilities
	 */
	private static ?Import_Abilities $instance = null;

	/**
	 * Constructor, not used as this a Singleton object.
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
	public static function get_instance(): Import_Abilities {
		if ( \is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize this object.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'wp_abilities_api_init', array( $this, 'add_abilities' ) );
	}

	/**
	 * Add the abilities.
	 *
	 * @return void
	 */
	public function add_abilities(): void {
		// bail if support for abilities is missing.
		if ( ! \function_exists( 'wp_register_ability' ) ) {
			return;
		}

		// the parts of the result, which both abilities share.
		$shared_output = array(
			'dry_run'  => array( 'type' => 'boolean' ),
			'done'     => array(
				'type'        => 'boolean',
				'description' => __( 'True if the action has been run successfully.', 'personio-integration-light' ),
			),
			'errors'   => array(
				'type'  => 'array',
				'items' => array( 'type' => 'string' ),
			),
			'warnings' => array(
				'type'  => 'array',
				'items' => array( 'type' => 'string' ),
			),
			'message'  => array( 'type' => 'string' ),
		);

		// add the ability to run the import.
		wp_register_ability(
			Abilities::ABILITY_CATEGORY . '/run-import',
			array(
				'label'               => __( 'Run the import of positions', 'personio-integration-light' ),
				'description'         => __( 'Runs the import of the open positions from Personio now, like the button in the settings of this plugin: new positions are added, changed ones are updated and positions which do not exist in Personio anymore are removed from WordPress. Call it first with "dry_run": true to check whether the import can be started and from where it would import. Start it with "dry_run": false if the user asked for the import or agreed. The call returns when the import is finished, which can take some time. Use get-import-status to follow an import which is still running, and cancel-import if it got stuck.', 'personio-integration-light' ),
				'category'            => Abilities::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'dry_run' => array(
							'type'        => 'boolean',
							'description' => __( 'Only check whether the import can be started and return what would happen, without running it. Set to false to run the import.', 'personio-integration-light' ),
							'default'     => true,
						),
					),
					'default'    => array(),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array_merge(
						$shared_output,
						array(
							'action'            => array(
								'type'        => 'string',
								'description' => __( 'What has been done or would be done: "import" or "none".', 'personio-integration-light' ),
							),
							'import_type'       => array(
								'type'        => 'string',
								'description' => __( 'The internal name of the used import type, empty if none is enabled.', 'personio-integration-light' ),
							),
							'personio_urls'     => array(
								'type'        => 'array',
								'description' => __( 'The URLs of the Personio accounts the positions are imported from.', 'personio-integration-light' ),
								'items'       => array( 'type' => 'string' ),
							),
							'languages'         => array(
								'type'        => 'array',
								'description' => __( 'The languages the positions are imported in.', 'personio-integration-light' ),
								'items'       => array( 'type' => 'string' ),
							),
							'positions_before'  => array(
								'type'        => 'integer',
								'description' => __( 'The amount of positions in WordPress before the import.', 'personio-integration-light' ),
							),
							'position_count'    => array(
								'type'        => 'integer',
								'description' => __( 'The amount of positions in WordPress now.', 'personio-integration-light' ),
							),
							'new_positions'     => array(
								'type'        => 'integer',
								'description' => __( 'The amount of positions which have been added by this import.', 'personio-integration-light' ),
							),
							'deleted_positions' => array(
								'type'        => 'integer',
								'description' => __( 'The amount of positions which have been removed by this import, as they do not exist in Personio anymore.', 'personio-integration-light' ),
							),
						)
					),
				),
				'execute_callback'    => array( $this, 'run_import' ),
				'permission_callback' => array( Abilities::get_instance(), 'has_permission' ),
				'meta'                => array(
					'show_in_rest' => true,
					'mcp'          => array( 'public' => true ), // expose via the MCP Adapter of WordPress.
					'annotations'  => array(
						'readonly'    => false,
						'destructive' => false, // the positions are a copy of the data in Personio, nothing gets lost there.
						'idempotent'  => true,
					),
				),
			)
		);

		// add the ability to cancel a running import.
		wp_register_ability(
			Abilities::ABILITY_CATEGORY . '/cancel-import',
			array(
				'label'               => __( 'Cancel the running import of positions', 'personio-integration-light' ),
				'description'         => __( 'Releases an import of positions which is marked as running, so a new import can be started with run-import. Use it only if an import got stuck, e.g. if get-import-status reports it as running for more than an hour. It does not stop a process which is still working. Call it first with "dry_run": true and cancel with "dry_run": false only after the user agreed.', 'personio-integration-light' ),
				'category'            => Abilities::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'dry_run' => array(
							'type'        => 'boolean',
							'description' => __( 'Only return what would happen, without cancelling. Set to false to cancel.', 'personio-integration-light' ),
							'default'     => true,
						),
					),
					'default'    => array(),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array_merge(
						$shared_output,
						array(
							'action'          => array(
								'type'        => 'string',
								'description' => __( 'What has been done or would be done: "cancel" or "none".', 'personio-integration-light' ),
							),
							'started_at'      => array(
								'type'        => 'string',
								'description' => __( 'The date the running import has been started (ISO 8601), empty if none is running.', 'personio-integration-light' ),
							),
							'running_seconds' => array(
								'type'        => 'integer',
								'description' => __( 'How long the import is running, in seconds.', 'personio-integration-light' ),
							),
						)
					),
				),
				'execute_callback'    => array( $this, 'cancel_import' ),
				'permission_callback' => array( Abilities::get_instance(), 'has_permission' ),
				'meta'                => array(
					'show_in_rest' => true,
					'mcp'          => array( 'public' => true ), // expose via the MCP Adapter of WordPress.
					'annotations'  => array(
						'readonly'    => false,
						'destructive' => false,
						'idempotent'  => true,
					),
				),
			)
		);

		// add the ability to delete all positions.
		wp_register_ability(
			Abilities::ABILITY_CATEGORY . '/delete-positions',
			array(
				'label'               => __( 'Delete all positions', 'personio-integration-light' ),
				'description'         => __( 'Deletes all positions in WordPress, like the button in the settings of this plugin. The website shows no positions until the next import. Nothing is changed in Personio: the positions are imported again by run-import, which is also the way to rebuild them completely. Single positions can not be deleted. Call it first with "dry_run": true and delete with "dry_run": false only after the user agreed.', 'personio-integration-light' ),
				'category'            => Abilities::ABILITY_CATEGORY,
				'input_schema'        => array(
					'type'       => 'object',
					'properties' => array(
						'dry_run' => array(
							'type'        => 'boolean',
							'description' => __( 'Only return what would happen, without deleting. Set to false to delete.', 'personio-integration-light' ),
							'default'     => true,
						),
					),
					'default'    => array(),
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array_merge(
						$shared_output,
						array(
							'action'            => array(
								'type'        => 'string',
								'description' => __( 'What has been done or would be done: "delete" or "none".', 'personio-integration-light' ),
							),
							'positions_before'  => array(
								'type'        => 'integer',
								'description' => __( 'The amount of positions in WordPress before the deletion.', 'personio-integration-light' ),
							),
							'position_count'    => array(
								'type'        => 'integer',
								'description' => __( 'The amount of positions in WordPress now.', 'personio-integration-light' ),
							),
							'deleted_positions' => array(
								'type'        => 'integer',
								'description' => __( 'The amount of positions which have been deleted.', 'personio-integration-light' ),
							),
						)
					),
				),
				'execute_callback'    => array( $this, 'delete_positions' ),
				'permission_callback' => array( Abilities::get_instance(), 'has_permission' ),
				'meta'                => array(
					'show_in_rest' => true,
					'mcp'          => array( 'public' => true ), // expose via the MCP Adapter of WordPress.
					'annotations'  => array(
						'readonly'    => false,
						'destructive' => true,
						'idempotent'  => true,
					),
				),
			)
		);
	}

	/**
	 * Return whether the given input requests a dry run, which is the default.
	 *
	 * @param mixed $input The given input.
	 *
	 * @return bool
	 */
	private function is_dry_run( mixed $input ): bool {
		return ! \is_array( $input ) || ! isset( $input['dry_run'] ) || (bool) $input['dry_run'];
	}

	/**
	 * Return the start time of the running import, 0 if none is running.
	 *
	 * @return int
	 */
	private function get_running_since(): int {
		return absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) );
	}

	/**
	 * Return the start time of the running deletion of all positions, 0 if none is running.
	 *
	 * @return int
	 */
	private function get_deleting_since(): int {
		return PersonioPosition::get_instance()->get_deletion_start_time();
	}

	/**
	 * Return whether a task with the given start time is running that long, that it most likely got stuck.
	 *
	 * @param int $since The start time.
	 *
	 * @return bool
	 */
	private function is_stuck( int $since ): bool {
		return $since > 0 && ( time() - $since ) > self::STUCK_AFTER;
	}

	/**
	 * Return the reasons why the import can not be started right now.
	 *
	 * The checks match the checks of the import itself. They are done before, as the import would otherwise
	 * document them as failed import and send an email about it.
	 *
	 * @return array<int,string>
	 */
	public function get_blockers(): array {
		$blockers = array();

		// get the import object.
		$imports_obj = Imports::get_instance()->get_import_extension();

		// check if an import type is enabled.
		if ( ! $imports_obj ) {
			$blockers[] = __( 'No import extension is enabled. Enable the import type to use in the extensions of this plugin.', 'personio-integration-light' );
		}

		// check if an import is already running.
		$running_since = $this->get_running_since();
		if ( $running_since > 0 ) {
			if ( $this->is_stuck( $running_since ) ) {
				/* translators: %1$s will be replaced by a date. */
				$blockers[] = \sprintf( __( 'An import is marked as running since %1$s, which is unusually long. Use cancel-import to release it and start the import again.', 'personio-integration-light' ), gmdate( 'c', $running_since ) );
			} else {
				/* translators: %1$s will be replaced by a date. */
				$blockers[] = \sprintf( __( 'An import is already running since %1$s. Wait until it is finished, see get-import-status.', 'personio-integration-light' ), gmdate( 'c', $running_since ) );
			}
		}

		// check if the positions are deleted right now (a deletion which got stuck does not block the import).
		$deleting_since = $this->get_deleting_since();
		if ( $deleting_since > 0 && ! PersonioPosition::get_instance()->is_deletion_stuck() ) {
			/* translators: %1$s will be replaced by a date. */
			$blockers[] = \sprintf( __( 'The deletion of all positions is running since %1$s. Wait until it is finished.', 'personio-integration-light' ), gmdate( 'c', $deleting_since ) );
		}

		// check the requirements of the import via XML.
		if ( $imports_obj instanceof Xml ) {
			if ( empty( Personio_Accounts::get_instance()->get_personio_urls() ) ) {
				$blockers[] = __( 'The Personio URL is not configured. It has to be set in the settings of this plugin.', 'personio-integration-light' );
			}
			if ( ! \function_exists( 'simplexml_load_string' ) ) {
				$blockers[] = __( 'The PHP extension simplexml is missing on the system. Please contact your hoster about this.', 'personio-integration-light' );
			}
			if ( empty( Languages::get_instance()->get_active_languages() ) ) {
				$blockers[] = __( 'No active language is configured. It has to be set in the settings of this plugin.', 'personio-integration-light' );
			}
		}

		/**
		 * Filter the reasons why the import of positions can not be started via abilities.
		 *
		 * Add a text for each reason. The import is only started if this list is empty.
		 *
		 * @since 5.8.0 Available since 5.8.0.
		 * @param array<int,string>  $blockers    The list of reasons.
		 * @param Imports_Base|false $imports_obj The import object, false if no import extension is enabled.
		 */
		$blockers = apply_filters( 'personio_integration_ability_import_blockers', $blockers, $imports_obj );

		// return only texts.
		return array_values( array_map( 'wp_strip_all_tags', array_filter( (array) $blockers, 'is_string' ) ) );
	}

	/**
	 * Return the errors of the import as list of texts.
	 *
	 * @param Imports_Base $imports_obj The import object, which has been run.
	 * @param bool         $with_saved  True to add the errors the import has saved for the settings.
	 *
	 * @return array<int,string>
	 */
	private function get_error_texts( Imports_Base $imports_obj, bool $with_saved ): array {
		$texts = array();

		// get the errors of the import object, in the same way the import itself reads them.
		foreach ( $imports_obj->get_errors() as $error ) {
			$text = $error->get_error_message();
			$data = $error->get_error_data();
			if ( ! empty( $data ) && \is_scalar( $data ) ) {
				$text = (string) $data;
			}
			$texts[] = $text;
		}

		// add the errors the import has saved, as some of them are only documented there.
		if ( $with_saved ) {
			$saved = get_option( WP_PERSONIO_INTEGRATION_IMPORT_ERRORS, array() );
			if ( \is_array( $saved ) ) {
				$texts = array_merge( $texts, array_filter( $saved, 'is_string' ) );
			}
		}

		// return each text only once and without HTML.
		return array_values( array_unique( array_filter( array_map( 'wp_strip_all_tags', $texts ) ) ) );
	}

	/**
	 * Run the import of positions from Personio.
	 *
	 * @param mixed $input The given input.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function run_import( mixed $input = array() ): array|WP_Error {
		// get the parameters.
		$dry_run = $this->is_dry_run( $input );

		// get the import object.
		$imports_obj = Imports::get_instance()->get_import_extension();

		// get the Personio URLs.
		$personio_urls = array_values( array_map( 'strval', array_filter( (array) Personio_Accounts::get_instance()->get_personio_urls(), 'is_scalar' ) ) );

		// get the actual amount of positions.
		$positions_before = Positions::get_instance()->get_positions_count();

		// prepare the result.
		$result = array(
			'dry_run'           => $dry_run,
			'done'              => false,
			'action'            => 'none',
			'import_type'       => $imports_obj ? $imports_obj->get_name() : '',
			'personio_urls'     => $personio_urls,
			'languages'         => array_map( 'strval', array_keys( Languages::get_instance()->get_active_languages() ) ),
			'positions_before'  => $positions_before,
			'position_count'    => $positions_before,
			'new_positions'     => 0,
			'deleted_positions' => 0,
			'errors'            => $this->get_blockers(),
			'warnings'          => array(),
			'message'           => '',
		);

		// bail if the import can not be started.
		if ( ! $imports_obj || ! empty( $result['errors'] ) ) {
			$result['message'] = __( 'The import can not be started. Fix the errors first.', 'personio-integration-light' );
			return $result;
		}

		// bail if this is only a check.
		if ( $dry_run ) {
			$result['action']  = 'import';
			$result['message'] = __( 'The import can be started. It would add new positions, update changed ones and remove positions which do not exist in Personio anymore. Start it with "dry_run": false if the user asked for the import or agreed.', 'personio-integration-light' );
			return $result;
		}

		// collect what the import does.
		$started   = false;
		$deleted   = 0;
		$on_start  = static function () use ( &$started ): void {
			$started = true;
		};
		$on_delete = static function () use ( &$deleted ): void {
			++$deleted;
		};
		add_action( 'personio_integration_import_starting', $on_start );
		add_action( 'personio_integration_light_import_deleted_position', $on_delete );

		// prevent output for WP CLI and its exit on errors, if this process is run via WP CLI (e.g. by an MCP server which uses STDIO).
		add_filter( 'personio_integration_light_is_cli', '__return_false', PHP_INT_MAX );

		// forget the errors of a previous import in this process.
		$imports_obj->reset_errors();

		// run the import.
		$exception = null;
		try {
			$imports_obj->run();
		} catch ( Throwable $e ) {
			$exception = $e;
		} finally {
			remove_filter( 'personio_integration_light_is_cli', '__return_false', PHP_INT_MAX );
			remove_action( 'personio_integration_light_import_deleted_position', $on_delete );
			remove_action( 'personio_integration_import_starting', $on_start );
		}

		// get the errors of this import.
		$errors = $this->get_error_texts( $imports_obj, $started );

		// handle an import which broke up unexpectedly.
		if ( $exception instanceof Throwable ) {
			// log this event.
			Log::get_instance()->add( __( 'Import of positions via ability was aborted by an error:', 'personio-integration-light' ) . ' <code>' . esc_html( $exception->getMessage() ) . '</code>', 'error', 'import' );

			// release the import, as this process does not work on it anymore.
			update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 );

			// add the error to the result.
			/* translators: %1$s will be replaced by an error message. */
			$errors[] = \sprintf( __( 'The import was aborted by an error: %1$s', 'personio-integration-light' ), wp_strip_all_tags( $exception->getMessage() ) );
		}

		// collect the results.
		$new_positions               = $started ? get_option( WP_PERSONIO_INTEGRATION_IMPORT_NEW_POSITIONS, array() ) : array();
		$result['action']            = $started ? 'import' : 'none';
		$result['done']              = $started && empty( $errors );
		$result['errors']            = $errors;
		$result['position_count']    = Positions::get_instance()->get_positions_count();
		$result['new_positions']     = \is_array( $new_positions ) ? \count( $new_positions ) : 0;
		$result['deleted_positions'] = $deleted;

		// add a message.
		if ( $result['done'] ) {
			/* translators: %1$d, %2$d and %3$d will be replaced by numbers. */
			$result['message'] = \sprintf( __( 'The import has been run. Positions in WordPress now: %1$d, added: %2$d, removed: %3$d.', 'personio-integration-light' ), $result['position_count'], $result['new_positions'], $result['deleted_positions'] );
		} else {
			$result['message'] = __( 'The import ended with errors. See the errors, get-import-status and get-log for details.', 'personio-integration-light' );
		}

		// return the result.
		return $result;
	}

	/**
	 * Cancel the running import by releasing its marker.
	 *
	 * @param mixed $input The given input.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function cancel_import( mixed $input = array() ): array|WP_Error {
		// get the parameters.
		$dry_run = $this->is_dry_run( $input );

		// get the start time of the running import.
		$running_since = $this->get_running_since();

		// prepare the result.
		$result = array(
			'dry_run'         => $dry_run,
			'done'            => false,
			'action'          => 'none',
			'started_at'      => $running_since > 0 ? gmdate( 'c', $running_since ) : '',
			'running_seconds' => $running_since > 0 ? max( 0, time() - $running_since ) : 0,
			'errors'          => array(),
			'warnings'        => array(),
			'message'         => '',
		);

		// bail if no import is running.
		if ( 0 === $running_since ) {
			$result['message'] = __( 'No import is running, so there is nothing to cancel.', 'personio-integration-light' );
			return $result;
		}
		$result['action'] = 'cancel';

		// warn if the import might still be working.
		if ( ! $this->is_stuck( $running_since ) ) {
			$result['warnings'][] = __( 'This import has been started less than an hour ago and might still be working. Cancelling does not stop it, so a new import could run at the same time.', 'personio-integration-light' );
		}

		// bail if this is only a check.
		if ( $dry_run ) {
			$result['message'] = __( 'Cancelling would release the running import, so a new import can be started with run-import. Ask the user before cancelling with "dry_run": false.', 'personio-integration-light' );
			return $result;
		}

		// remove the running marker.
		update_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 );

		// log this event.
		$user = wp_get_current_user();
		if ( $user instanceof WP_User ) { // @phpstan-ignore instanceof.alwaysTrue
			/* translators: a username will replace %1$s. */
			Log::get_instance()->add( \sprintf( __( 'A running import has been canceled through %1$s.', 'personio-integration-light' ), esc_html( $user->display_name ) ), 'info', 'import' );
		}

		// return the result.
		$result['done']    = true;
		$result['message'] = __( 'The running import has been released. Use run-import to start a new import.', 'personio-integration-light' );
		return $result;
	}

	/**
	 * Delete all positions in WordPress.
	 *
	 * @param mixed $input The given input.
	 *
	 * @return array<string,mixed>|WP_Error
	 */
	public function delete_positions( mixed $input = array() ): array|WP_Error {
		// get the parameters.
		$dry_run = $this->is_dry_run( $input );

		// get the actual amount of positions.
		$positions_before = Positions::get_instance()->get_positions_count();

		// prepare the result.
		$result = array(
			'dry_run'           => $dry_run,
			'done'              => false,
			'action'            => 'none',
			'positions_before'  => $positions_before,
			'position_count'    => $positions_before,
			'deleted_positions' => 0,
			'errors'            => array(),
			'warnings'          => array(),
			'message'           => '',
		);

		// the positions can not be deleted during an import.
		$running_since = $this->get_running_since();
		if ( $running_since > 0 ) {
			/* translators: %1$s will be replaced by a date. */
			$result['errors'][] = \sprintf( __( 'An import is running since %1$s. Wait until it is finished, or release it with cancel-import if it got stuck.', 'personio-integration-light' ), gmdate( 'c', $running_since ) );
		}

		// the positions can not be deleted twice at the same time.
		$deleting_since = $this->get_deleting_since();
		$is_stuck       = PersonioPosition::get_instance()->is_deletion_stuck();
		if ( $deleting_since > 0 && ! $is_stuck ) {
			/* translators: %1$s will be replaced by a date. */
			$result['errors'][] = \sprintf( __( 'The deletion of all positions is already running since %1$s. Wait until it is finished.', 'personio-integration-light' ), gmdate( 'c', $deleting_since ) );
		}

		// bail if the positions can not be deleted.
		if ( ! empty( $result['errors'] ) ) {
			$result['message'] = __( 'The positions can not be deleted right now.', 'personio-integration-light' );
			return $result;
		}

		// bail if there is nothing to delete.
		if ( 0 === $positions_before ) {
			$result['message'] = __( 'There are no positions in WordPress, so there is nothing to delete.', 'personio-integration-light' );
			return $result;
		}
		$result['action'] = 'delete';

		// a deletion which got stuck is released by the deletion itself.
		if ( $is_stuck ) {
			/* translators: %1$s will be replaced by a date. */
			$result['warnings'][] = \sprintf( __( 'A deletion which has been started on %1$s did not end properly. It is released by this deletion.', 'personio-integration-light' ), gmdate( 'c', $deleting_since ) );
		}

		// bail if this is only a check.
		if ( $dry_run ) {
			/* translators: %1$d will be replaced by a number. */
			$result['message'] = \sprintf( __( 'Deleting would remove all %1$d positions from WordPress, so the website shows no positions until the next import. Nothing is changed in Personio, run-import imports them again. Ask the user before deleting with "dry_run": false.', 'personio-integration-light' ), $positions_before );
			return $result;
		}

		// prevent output for WP CLI, if this process is run via WP CLI (e.g. by an MCP server which uses STDIO).
		add_filter( 'personio_integration_light_is_cli', '__return_false', PHP_INT_MAX );

		// delete the positions.
		try {
			PersonioPosition::get_instance()->delete_positions();
		} catch ( Throwable $e ) {
			// log this event.
			Log::get_instance()->add( __( 'Deletion of positions via ability was aborted by an error:', 'personio-integration-light' ) . ' <code>' . esc_html( $e->getMessage() ) . '</code>', 'error', 'import' );

			// release the deletion, as this process does not work on it anymore.
			update_option( WP_PERSONIO_INTEGRATION_DELETE_RUNNING, 0 );

			// add the error to the result.
			/* translators: %1$s will be replaced by an error message. */
			$result['errors'][] = \sprintf( __( 'The deletion was aborted by an error: %1$s', 'personio-integration-light' ), wp_strip_all_tags( $e->getMessage() ) );
		} finally {
			remove_filter( 'personio_integration_light_is_cli', '__return_false', PHP_INT_MAX );
		}

		// add the errors the deletion has saved.
		$saved_errors = get_option( WP_PERSONIO_INTEGRATION_DELETE_ERRORS, array() );
		if ( \is_array( $saved_errors ) ) {
			$result['errors'] = array_values( array_unique( array_merge( $result['errors'], array_map( 'wp_strip_all_tags', array_filter( $saved_errors, 'is_string' ) ) ) ) );
		}

		// collect the results.
		$result['position_count']    = Positions::get_instance()->get_positions_count();
		$result['deleted_positions'] = max( 0, $positions_before - $result['position_count'] );
		if ( empty( $result['errors'] ) && $result['position_count'] > 0 ) {
			$result['errors'][] = __( 'Not all positions could be deleted.', 'personio-integration-light' );
		}
		$result['done'] = empty( $result['errors'] );

		// add a message.
		if ( $result['done'] ) {
			/* translators: %1$d will be replaced by a number. */
			$result['message'] = \sprintf( __( '%1$d positions have been deleted in WordPress. Use run-import to import the positions from Personio again.', 'personio-integration-light' ), $result['deleted_positions'] );
		} else {
			$result['message'] = __( 'The deletion ended with errors. See the errors and get-log for details.', 'personio-integration-light' );
		}

		// return the result.
		return $result;
	}
}
