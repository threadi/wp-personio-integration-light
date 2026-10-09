<?php
/**
 * File to handle locks for long-running processes like the import or the deletion of positions.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin;

// prevent direct access.
\defined( 'ABSPATH' ) || exit;

/**
 * Object to handle locks, which are saved as option with the start time of the process as value.
 *
 * Setting and releasing a lock is done with a single SQL statement each, so it is atomic:
 * if two processes try to get the same lock at the same time, only one of them gets it.
 *
 * The value of the option is 0 if no process is running, otherwise the timestamp of its start.
 * This timestamp is also the token of the process, which holds the lock.
 */
class Lock {
	/**
	 * Try to get the lock with the given option name.
	 *
	 * A lock older than the given max age is treated as orphaned (e.g. the process has been killed by the hosting)
	 * and taken over.
	 *
	 * @param string $option_name The name of the option used for this lock.
	 * @param int    $max_age     Time in seconds after which a lock is treated as orphaned.
	 *
	 * @return int The token (= the start time) on success, 0 if another process holds the lock.
	 */
	public static function acquire( string $option_name, int $max_age = HOUR_IN_SECONDS ): int {
		global $wpdb;

		// make sure the option exists, as the UPDATE below only works on existing rows.
		// Hint: add_option() is not used, as it would overwrite an existing value via "ON DUPLICATE KEY UPDATE".
		$wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO {$wpdb->options} (option_name, option_value, autoload) VALUES (%s, '0', 'off')", $option_name ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		// get the actual time as token.
		$now = time();

		// set the lock only if no process is running or the running one is orphaned.
		$updated = $wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"UPDATE {$wpdb->options} SET option_value = %d WHERE option_name = %s AND ( option_value = '0' OR CAST( option_value AS UNSIGNED ) < %d )",
				$now,
				$option_name,
				$now - $max_age
			)
		);

		// clear the caches, as we changed the option directly in the database.
		self::clear_cache( $option_name );

		// return the token if we got the lock.
		return 1 === $updated ? $now : 0;
	}

	/**
	 * Release the lock, but only if it is still held by the process with the given token.
	 *
	 * @param string $option_name The name of the option used for this lock.
	 * @param int    $token       The token returned by acquire().
	 *
	 * @return bool True if the lock has been released.
	 */
	public static function release( string $option_name, int $token ): bool {
		global $wpdb;

		// bail if no token is given.
		if ( $token <= 0 ) {
			return false;
		}

		// release the lock only if it is still ours.
		$updated = $wpdb->query( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"UPDATE {$wpdb->options} SET option_value = '0' WHERE option_name = %s AND option_value = %s",
				$option_name,
				(string) $token
			)
		);

		// clear the caches, as we changed the option directly in the database.
		self::clear_cache( $option_name );

		// return whether we released the lock.
		return 1 === $updated;
	}

	/**
	 * Release the lock regardless of which process holds it.
	 *
	 * Use this only for tasks which are explicitly meant to remove a lock of another process,
	 * e.g. if a user cancels a running import or a stuck process is released.
	 *
	 * @param string $option_name The name of the option used for this lock.
	 *
	 * @return void
	 */
	public static function force_release( string $option_name ): void {
		update_option( $option_name, 0, false );
	}

	/**
	 * Return the start time of the process which holds the lock, 0 if no process holds it.
	 *
	 * @param string $option_name The name of the option used for this lock.
	 *
	 * @return int
	 */
	public static function get_start_time( string $option_name ): int {
		return absint( get_option( $option_name, 0 ) );
	}

	/**
	 * Clear the object caches for the given option.
	 *
	 * @param string $option_name The name of the option.
	 *
	 * @return void
	 */
	private static function clear_cache( string $option_name ): void {
		wp_cache_delete( $option_name, 'options' );
		wp_cache_delete( 'alloptions', 'options' );
		wp_cache_delete( 'notoptions', 'options' );
	}
}
