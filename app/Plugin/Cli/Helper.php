<?php
/**
 * File with cli-helper tasks for the plugin.
 *
 * @package personio-integration-light
 */

namespace App\Plugin\Cli;

use App\Log;
use App\PersonioIntegration\Positions;
use App\PersonioIntegration\Taxonomies;
use App\Plugin\Languages;

/**
 * Trait with helper-functions.
 */
trait Helper {

	/**
	 * Delete all imported positions.
	 *
	 * @return void
	 */
	private function delete_positions_from_db(): void {
		$positions_obj  = Positions::get_instance();
		$positions      = $positions_obj->get_positions();
		$position_count = count( $positions );
		$progress       = \App\Helper::is_cli() ? \WP_CLI\Utils\make_progress_bar( 'Delete all local positions', $position_count ) : false;
		foreach ( $positions as $position ) {
			// delete it.
			wp_delete_post( $position->get_id(), true );

			// show progress.
			$progress ? $progress->tick() : false;
		}
		// finalize progress.
		$progress ? $progress->finish() : false;

		// delete position count.
		delete_option( 'personioIntegrationPositionCount' );

		// delete options regarding the import.
		foreach ( Languages::get_instance()->get_languages() as $language_name => $lang ) {
			delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP . $language_name );
			delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 . $language_name );
		}

		// output success-message.
		\App\Helper::is_cli() ? \WP_CLI::success( $position_count . ' positions from local database deleted.' ) : false;
	}

	/**
	 * Delete all taxonomies which depends on our own custom post type.
	 *
	 * @return void
	 * @noinspection SqlResolve
	 */
	private function delete_taxonomies(): void {
		global $wpdb;

		// delete the content of all taxonomies.
		// -> hint: some will be newly insert after next wp-init.
		$taxonomies = Taxonomies::get_instance()->get_taxonomies();
		$progress   = \App\Helper::is_cli() ? \WP_CLI\Utils\make_progress_bar( 'Delete all local taxonomies', count( $taxonomies ) ) : false;
		foreach ( $taxonomies as $taxonomy => $settings ) {
			// delete all terms of this taxonomy.
			$sql    = '
                DELETE FROM ' . $wpdb->terms . '
                WHERE term_id IN
                (
                    SELECT ' . $wpdb->terms . '.term_id
                    FROM ' . $wpdb->terms . '
                    JOIN ' . $wpdb->term_taxonomy . '
                    ON ' . $wpdb->term_taxonomy . '.term_id = ' . $wpdb->terms . '.term_id
                    WHERE taxonomy = %s
                )';
			$params = array(
				$taxonomy,
			);
			$wpdb->query( $wpdb->prepare( $sql, $params ) );

			// delete all taxonomy-entries.
			$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->term_taxonomy . ' WHERE taxonomy = %s', array( $taxonomy ) ) );

			// cleanup options.
			delete_option( $taxonomy . '_children' );

			// log in debug-mode.
			if ( 1 === absint( get_option( 'personioIntegration_debug', 0 ) ) ) {
				$log = new Log();
				$log->add_log( 'Taxonomy ' . $taxonomy . ' has been deleted.', 'success' );
			}

			// show progress.
			$progress ? $progress->tick() : false;
		}
		// finalize progress.
		$progress ? $progress->finish() : false;

		// delete marker that default terms has been deleted.
		delete_option( 'personioTaxonomyDefaults' );

		// output success-message.
		\App\Helper::is_cli() ? \WP_CLI::success( count( $taxonomies ) . ' taxonomies from local database has been cleaned.' ) : false;
	}
}