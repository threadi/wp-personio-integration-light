<?php
/**
 * File for cli-commands of this plugin.
 *
 * @package personio-integration-light
 */

namespace personioIntegration;

/**
 * Handler for recruitment from HR Personio
 */
class cli {

    use helper_cli;

    /**
     * Get actual open positions via PersonioAPI.
     *
     * @since  1.0.0
     * @noinspection PhpUnused
     */
    public function getPositions(): void
    {
        new Import();
    }

    /**
     * Cleanup the database from plugin-data.
     *
     * @since  1.0.0
     * @return void
     * @noinspection PhpUnused
     */
    public function deleteAll(): void
    {
        // log this event.
        $logs = new Log();
        $logs->addLog( 'WP CLI-command deleteAll has been used.', 'success' );

        // delete taxonomies
        $this->deleteTaxonomies();

        // delete position
        $this->deletePositionsFromDb();
    }

    /**
     * Remove all position from local database.
     *
     * @since  1.0.0
     * @noinspection PhpUnused
     */
    public function deletePositions( array $args ): void
    {
        // set arguments if empty.
        if( empty($args) ) {
            $args = array( 'WP CLI-command deletePositions', '' );
        }

        // log this event.
        $logs = new Log();
        $logs->addLog( sprintf( '%s has been used%s.', $args[0], $args[1] ), 'success' );

        // delete them.
        $this->deletePositionsFromDb();
    }

    /**
     * Resets all settings of this plugin.
     *
     * @param array $deleteData
     * @return void
     * @since  1.0.0
     */
    public function resetPlugin( array $deleteData = array() ): void
    {
        (new installer)->removeAllData( $deleteData );
        (new installer)->initializePlugin();
    }
}
