<?php

namespace personioIntegration;

/**
 * Handler for recruitment from HR Personio
 */
class cli {

    use helper;

    /**
     * Get actual open positions via PersonioAPI.
     *
     * @since  1.0.0
     * @noinspection PhpUnused
     */
    public function getPositions() {
        new Import();
    }

    /**
     * Cleanup the database from plugin-data.
     *
     * @since  1.0.0
     * @return void
     * @noinspection PhpUnused
     */
    public function deleteAll()
    {
        // delete taxonomies
        $this->deleteTaxonomies();

        // delete position
        $this->deletePositionsFromDb();

        // delete options regarding the import
        foreach( WP_PERSONIO_INTEGRATION_LANGUAGES as $key => $lang ) {
            delete_option(WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP.$key);
            delete_option(WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5.$key);
        }
    }

    /**
     * Remove all position from local database.
     *
     * @since  1.0.0
     * @noinspection PhpUnused
     */
    public function deletePositions() {
        $this->deletePositionsFromDb();
    }

    /**
     * Resets all settings of this plugin.
     *
     * @since  1.0.0
     * @return void
     */
    public function resetPlugin( $deleteData = [] ) {
        (new installer)->removeAllData( $deleteData );
        (new installer)->initializePlugin();
    }
}