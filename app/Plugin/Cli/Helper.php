<?php
/**
 * File with cli-helper tasks for the plugin.
 *
 * @package personio-integration-light
 */

namespace PersonioIntegrationLight\Plugin\Cli;

// prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use PersonioIntegrationLight\Log;
use PersonioIntegrationLight\PersonioIntegration\Imports;
use PersonioIntegrationLight\PersonioIntegration\Personio;
use PersonioIntegrationLight\PersonioIntegration\Positions;
use PersonioIntegrationLight\PersonioIntegration\Taxonomies;
use PersonioIntegrationLight\Plugin\Languages;

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
		$progress       = \PersonioIntegrationLight\Helper::is_cli() ? \WP_CLI\Utils\make_progress_bar( 'Delete all local positions', $position_count ) : false;
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
		foreach ( Imports::get_instance()->get_personio_urls() as $personio_url ) {
			$personio_obj = new Personio( $personio_url );
			foreach ( Languages::get_instance()->get_languages() as $language_name => $lang ) {
				$personio_obj->remove_timestamp( $language_name );
				$personio_obj->remove_md5( $language_name );
			}
		}

		// output success-message.
		\PersonioIntegrationLight\Helper::is_cli() ? \WP_CLI::success( $position_count . ' positions from local database deleted.' ) : false;
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
		$progress   = \PersonioIntegrationLight\Helper::is_cli() ? \WP_CLI\Utils\make_progress_bar( 'Delete all local taxonomies', count( $taxonomies ) ) : false;
		foreach ( $taxonomies as $taxonomy => $settings ) {
			// delete all terms of this taxonomy.
			$wpdb->query(
				$wpdb->prepare(
					'DELETE FROM ' . $wpdb->terms . '
                WHERE term_id IN
                (
                    SELECT ' . $wpdb->terms . '.term_id
                    FROM ' . $wpdb->terms . '
                    JOIN ' . $wpdb->term_taxonomy . '
                    ON ' . $wpdb->term_taxonomy . '.term_id = ' . $wpdb->terms . '.term_id
                    WHERE taxonomy = %s
                )',
					array(
						$taxonomy,
					)
				)
			);

			// delete all taxonomy-entries.
			$wpdb->query( $wpdb->prepare( 'DELETE FROM ' . $wpdb->term_taxonomy . ' WHERE taxonomy = %s', array( $taxonomy ) ) );

			// cleanup options.
			delete_option( $taxonomy . '_children' );

			// log in debug-mode.
			if ( 1 === absint( get_option( 'personioIntegration_debug' ) ) ) {
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
		\PersonioIntegrationLight\Helper::is_cli() ? \WP_CLI::success( count( $taxonomies ) . ' taxonomies from local database has been cleaned.' ) : false;
	}
}
