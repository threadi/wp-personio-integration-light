<?php
/**
 * File for handling logging in this plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight;

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\Plugin\Db;

/**
 * Handler for logging in this plugin.
 */
class Log {
	/**
	 * The md5 hash.
	 *
	 * @var string
	 */
	private string $md5 = '';

	/**
	 * Instance of this object.
	 *
	 * @var ?Log
	 */
	private static ?Log $instance = null;

	/**
	 * Constructor for this object.
	 */
	public function __construct() {}

	/**
	 * Prevent cloning of this object.
	 *
	 * @return void
	 */
	private function __clone() { }

	/**
	 * Return the instance of this Singleton object.
	 */
	public static function get_instance(): Log {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Create the logging-table in the database.
	 *
	 * @return void
	 */
	public function create_table(): void {
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		// definition for the table for log-entries.
		$sql = 'CREATE TABLE ' . $wpdb->prefix . "personio_import_logs (
            `id` mediumint(9) NOT NULL AUTO_INCREMENT,
            `time` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
            `log` text DEFAULT '' NOT NULL,
            `md5` text DEFAULT '' NOT NULL,
            `category` varchar(40) DEFAULT '' NOT NULL,
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
		$wpdb->query( sprintf( 'DROP TABLE IF EXISTS %s', (string) esc_sql( $wpdb->prefix . 'personio_import_logs' ) ) ); // @phpstan-ignore cast.string
	}

	/**
	 * Add a single log-entry.
	 *
	 * @param string $log   The text to log.
	 * @param string $state The state to log.
	 * @param string $category The category for this log entry (optional).
	 * @param string $md5 Marker to identify unique entries (optional).
	 *
	 * @return void
	 */
	public function add( string $log, string $state, string $category = '', string $md5 = '' ): void {
		global $wpdb;

		// insert the log entry.
		Db::get_instance()->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prefix . 'personio_import_logs',
			array(
				'time'     => gmdate( 'Y-m-d H:i:s' ),
				'log'      => $log,
				'md5'      => $md5,
				'category' => $category,
				'state'    => $state,
			)
		);

		// clean the log.
		$this->clean_log();
	}

	/**
	 * Delete all entries that are older than X days.
	 *
	 * @return void
	 * @noinspection PhpUnused
	 */
	public function clean_log(): void {
		// bail on uninstalling.
		if ( defined( 'PERSONIO_INTEGRATION_DEACTIVATION_RUNNING' ) ) {
			return;
		}

		// get db connection.
		global $wpdb;

		// run the deletion.
		$wpdb->query( sprintf( 'DELETE FROM %s WHERE `time` < DATE_SUB(NOW(), INTERVAL %d DAY) LIMIT 10000', (string) esc_sql( $wpdb->prefix . 'personio_import_logs' ), absint( get_option( 'personioIntegrationMaxAgeLogEntries' ) ) ) ); // @phpstan-ignore cast.string

		// log if any error occurred.
		if ( ! empty( $wpdb->last_error ) ) {
			/* translators: %1$s will be replaced by a DB-error-message. */
			$this->add( sprintf( __( 'Database error during plugin activation: %1$s - This usually indicates that the database system of your hosting does not meet the minimum requirements of WordPress. Please contact your hosts support team for clarification.', 'personio-integration-light' ), '<code>' . esc_html( $wpdb->last_error ) . '</code>' ), 'error', 'system' );
		}
	}

	/**
	 * Return the list of categories with internal name and their label.
	 *
	 * @return array<string,string>
	 */
	public function get_categories(): array {
		$list = array(
			'system' => __( 'System', 'personio-integration-light' ),
		);

		/**
		 * Filter the list of possible log categories.
		 *
		 * @since 3.1.0 Available since 3.1.0.
		 *
		 * @param array<string,string> $list List of categories.
		 */
		return apply_filters( 'personio_integration_log_categories', $list );
	}

	/**
	 * Get log entries depending on some filters.
	 *
	 * Use for each possible condition own statements to match WCS.
	 *
	 * @return array<int,mixed>
	 */
	public function get_entries(): array {
		global $wpdb;

		// order table.
		$order_by = filter_input( INPUT_GET, 'orderby', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( is_null( $order_by ) ) {
			$order_by = 'date';
		}
		$order = filter_input( INPUT_GET, 'order', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( is_string( $order ) ) {
			$order = sanitize_sql_orderby( $order );
		} else {
			$order = 'DESC';
		}

		$limit = 10000;
		/**
		 * Filter limit to prevent possible errors on big tables.
		 *
		 * @since 3.1.0 Available since 3.1.0.
		 * @param int $limit The actual limit.
		 */
		$limit = apply_filters( 'personio_integration_light_log_limit', $limit );

		// get filter.
		$category = (string) filter_input( INPUT_GET, 'category', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		/**
		 * Filter the used category.
		 *
		 * @since 4.1.0 Available since 4.1.0.
		 * @param string $category The category to use.
		 */
		$category = (string) apply_filters( 'personio_integration_light_log_category', $category );

		// get md5.
		$md5 = (string) filter_input( INPUT_GET, 'md5', FILTER_SANITIZE_FULL_SPECIAL_CHARS );

		// if the request is empty, get md5 from the object if set.
		if ( empty( $md5 ) ) {
			$md5 = $this->get_md5();
		}

		/**
		 * Filter the used md5.
		 *
		 * @since 4.1.0 Available since 4.1.0.
		 * @param string $md5 The md5 to use.
		 */
		$md5 = (string) apply_filters( 'personio_integration_light_log_md5', $md5 );

		// get errors.
		$errors = absint( filter_input( INPUT_GET, 'errors', FILTER_SANITIZE_NUMBER_INT ) );

		/**
		 * Filter for errors.
		 *
		 * @since 4.1.0 Available since 4.1.0.
		 * @param int $errors Should be 1 to filter only for errors.
		 */
		$errors = apply_filters( 'personio_integration_light_log_errors', $errors );

		// add where-condition for errors.
		$where = '';
		if ( 1 === $errors ) {
			$where .= ' AND `state` = "error"';
		}

		// if only category is set.
		if ( ! empty( $category ) && empty( $md5 ) ) {
			// get and return the entries.
			return Db::get_instance()->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$wpdb->prepare(
					'SELECT `state`, `time` AS `date`, `log`, `category`
                    FROM `' . $wpdb->prefix . 'personio_import_logs`
                    WHERE `category` = %s' . $where . '
                    ORDER BY ' . $order_by . ' ' . $order . ', `id` ' . $order . '
                    LIMIT %d',
					array( $category, $limit )
				),
				ARRAY_A
			);
		}

		// if only md5 is set.
		if ( empty( $category ) && ! empty( $md5 ) ) {
			// get and return the entries.
			return Db::get_instance()->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$wpdb->prepare(
					'SELECT `state`, `time` AS `date`, `log`, `category`
                    FROM `' . $wpdb->prefix . 'personio_import_logs`
                    WHERE `md5` = %s' . $where . '
                    ORDER BY ' . $order_by . ' ' . $order . ', `id` ' . $order . '
                    LIMIT %d',
					array( $md5, $limit )
				),
				ARRAY_A
			);
		}

		// if both are set.
		if ( ! empty( $category ) && ! empty( $md5 ) ) {
			// get and return the entries.
			return Db::get_instance()->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$wpdb->prepare(
					'SELECT `state`, `time` AS `date`, `log`, `category`
                    FROM `' . $wpdb->prefix . 'personio_import_logs`
                    WHERE `md5` = %s AND `category` = %s' . $where . '
                    ORDER BY ' . $order_by . ' ' . $order . ', `id` ' . $order . '
                    LIMIT %d',
					array( $md5, $category, $limit )
				),
				ARRAY_A
			);
		}

		if ( 1 === $errors ) {
			// return all.
			return Db::get_instance()->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$wpdb->prepare(
					'SELECT `state`, `time` AS `date`, `log`, `category`
                FROM `' . $wpdb->prefix . 'personio_import_logs`
                WHERE `state` = "error"
                ORDER BY ' . $order_by . ' ' . $order . ', `id` ' . $order . '
                LIMIT %d',
					array( $limit )
				),
				ARRAY_A
			);
		}

		// return all.
		return Db::get_instance()->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				'SELECT `state`, `time` AS `date`, `log`, `category`
                FROM `' . $wpdb->prefix . 'personio_import_logs`
                ORDER BY ' . $order_by . ' ' . $order . '
                LIMIT %d',
				array( $limit )
			),
			ARRAY_A
		);
	}

	/**
	 * Return md5 hash.
	 *
	 * @return string
	 */
	private function get_md5(): string {
		return $this->md5;
	}

	/**
	 * Set the md5 hash.
	 *
	 * @param string $md5 The md5 hash.
	 *
	 * @return void
	 */
	public function set_md5( string $md5 ): void {
		$this->md5 = $md5;
	}
}
