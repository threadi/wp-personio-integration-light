<?php

namespace personioIntegration;

trait helper_cli {

    /**
     * Delete all imported positions.
     *
     * @return void
     */
    private function deletePositionsFromDb() {
        $positionsObject = new Positions();
        $positions = $positionsObject->getPositions();
        $positionCount = count($positions);
        $progress = helper::isCLI() ? \WP_CLI\Utils\make_progress_bar( 'Delete all local positions', $positionCount ) : false;
        foreach( $positions as $position ) {
            // get personioId for log
            $personioId = $position->getPersonioId();

            // delete it
            wp_delete_post($position->ID, true);

            // log in debug-mode
            if( get_option('personioIntegration_debug', 0) == 1 ) {
                $log = new Log();
                $log->addLog('Position '.$personioId.' has been deleted.', 'success');
            }

            // show progress
            !$progress ?: $progress->tick();
        }
        // finalize progress
        !$progress ?: $progress->finish();

        // delete position count
        delete_option('personioIntegrationPositionCount');

        // delete options regarding the import
        foreach( WP_PERSONIO_INTEGRATION_LANGUAGES as $key => $lang ) {
            delete_option(WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP.$key);
            delete_option(WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5.$key);
        }

        // output success-message
        helper::isCLI() ? \WP_CLI::success($positionCount." positions deleted.") : false;
    }

    /**
     * Delete all taxonomies which depends on our own custom post type.
     *
     * @return void
     * @noinspection SqlResolve
     */
    private function deleteTaxonomies()
    {
        global $wpdb;

        // delete the content of all taxonomies
        // -> hint: some will be newly insert after next wp-init
        $taxonomies = apply_filters('personio_integration_taxonomies', WP_PERSONIO_INTEGRATION_TAXONOMIES);
        $progress = helper::isCLI() ? \WP_CLI\Utils\make_progress_bar( 'Delete all local taxonomies', count($taxonomies) ) : false;
        foreach( $taxonomies as $taxonomy => $settings ) {
            // delete all terms of this taxonomy
            $sql = "
                DELETE FROM ".$wpdb->terms."
                WHERE term_id IN
                ( 
                    SELECT ".$wpdb->terms.".term_id
                    FROM ".$wpdb->terms."
                    JOIN ".$wpdb->term_taxonomy."
                    ON ".$wpdb->term_taxonomy.".term_id = ".$wpdb->terms.".term_id
                    WHERE taxonomy = '".$taxonomy."'
                )";
            $wpdb->query($sql);

            // delete all taxonomy-entries
            $sql = "
                DELETE FROM ".$wpdb->term_taxonomy."
                WHERE taxonomy = '".$taxonomy."'";
            $wpdb->query($sql);

            // cleanup options
            delete_option($taxonomy."_children");

            // log in debug-mode
            if( get_option('personioIntegration_debug', 0) == 1 ) {
                $log = new Log();
                $log->addLog('Taxonomy '.$taxonomy.' has been deleted.', 'success');
            }

            // show progress
            !$progress ?: $progress->tick();
        }
        // finalize progress
        !$progress ?: $progress->finish();

        // output success-message
        helper::isCLI() ? \WP_CLI::success(count($taxonomies)." taxonomies where cleaned.") : false;
    }
}