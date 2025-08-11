<?php
/**
 * File to handle database tasks.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Log;

/**
 * Object to handle database tasks.
 */
class Db {
	/**
	 * Instance of this object.
	 *
	 * @var ?Db
	 */
	private static ?Db $instance = null;

	/**
	 * Constructor which sets the active method.
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
	public static function get_instance(): Db {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Run the insert of data in the database.
	 *
	 * This function is simply using $wpdb->insert() but also checks for any errors.
	 *
	 * @param string $table The table to use.
	 * @param array<string,mixed> $data The data to insert.
	 *
	 * @return void
	 */
	public function insert( string $table, array $data ): void {
		global $wpdb;

		// add the data.
		$wpdb->insert( $table, $data );

		// check for any errors, but not if this is the log table itself as it might cause an infinite loop.
		if( $wpdb->last_error && ( $wpdb->prefix . 'personio_import_logs' ) !== $table && ! isset( $data['log'] ) ) {
			$this->log_error( $wpdb->last_query );
		}
	}

	/**
	 * Return the results for a statement from database.
	 *
	 * This function is simply using $wpdb->get_results() but also checks for any errors.
	 *
	 * @param string|null $sql The statement.
	 * @param string      $data_type The requested return format.
	 *
	 * @return array<int,mixed>
	 */
	public function get_results( ?string $sql, string $data_type = ARRAY_A ): array {
		global $wpdb;

		// bail if no statement is given.
		if( ! is_string( $sql ) ) {
			return array();
		}

		// get the results.
		$results = $wpdb->get_results( $sql, $data_type );

		// check for any errors.
		if( $wpdb->last_error ) {
			$this->log_error( $sql );
		}

		// return the results.
		return $results;
	}

	/**
	 * Log any error.
	 *
	 * @param string $sql The used SQL-statement.
	 *
	 * @return void
	 */
	private function log_error( string $sql ): void {
		global $wpdb;

		// create the error text.
		$text = '<strong>' . __( 'Database-error occurred!', 'personio-integration-light' ) . '</strong>';
		$text .= '<br><br><em>' . __( 'Statement:', 'personio-integration-light' ) . '</em> <code>' . $sql . '</code>';
		$text .= '<br><br><em>' . __( 'Error:', 'personio-integration-light' ) . '</em> <code>' . $wpdb->last_error . '</code>';

		// log this error.
		Log::get_instance()->add( $text, 'error', 'system' );
	}
}
