<?php
/**
 * File for handling logging in this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight;

// prevent also other direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handler for logging in this plugin.
 */
class Log {
	/**
	 * Constructor for Logging-Handler.
	 */
	public function __construct() {}

	/**
	 * Create the logging-table in the database.
	 *
	 * @return void
	 */
	public function create_table(): void {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		// table for import-log.
		$sql = 'CREATE TABLE ' . $wpdb->prefix . "personio_import_logs (
            `id` mediumint(9) NOT NULL AUTO_INCREMENT,
            `time` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            `log` text DEFAULT '' NOT NULL,
            `state` varchar(40) DEFAULT '' NOT NULL,
            UNIQUE KEY id (id)
        ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Delete the logging-table in the database.
	 *
	 * @return void
	 */
	public function delete_table(): void {
		global $wpdb;
		$wpdb->query( sprintf( 'DROP TABLE IF EXISTS %s', esc_sql( $wpdb->prefix . 'personio_import_logs' ) ) );
	}

	/**
	 * Add a single log-entry.
	 *
	 * @param string $log The text to log.
	 * @param string $state The state to log.
	 *
	 * @return void
	 */
	public function add_log( string $log, string $state ): void {
		global $wpdb;
		$wpdb->insert(
			$wpdb->prefix . 'personio_import_logs',
			array(
				'time'  => gmdate( 'Y-m-d H:i:s' ),
				'log'   => $log,
				'state' => $state,
			)
		);
		$this->clean_log();
	}

	/**
	 * Delete all entries which are older than X days.
	 *
	 * @return void
	 */
	public function clean_log(): void {
		global $wpdb;
		$wpdb->query( sprintf( 'DELETE FROM %s WHERE `time` < DATE_SUB(NOW(), INTERVAL %d DAY)', esc_sql( $wpdb->prefix . 'personio_import_logs' ), absint( get_option( 'personioIntegrationMaxAgeLogEntries' ) ) ) );
	}
}
