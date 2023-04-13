<?php

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
    public function deletePositions(): void
    {
        $this->deletePositionsFromDb();
    }

    /**
     * Resets all settings of this plugin.
     *
     * @param array $deleteData
     * @return void
     * @since  1.0.0
     */
    public function resetPlugin( $deleteData = [] ): void
    {
        (new installer)->removeAllData( $deleteData );
        (new installer)->initializePlugin();
    }
}