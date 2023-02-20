<?php

namespace personioIntegration;

/**
 * Object which holds all version-specific updates.
 */
class updates {

    /**
     * To run on update to (exact) version 1.2.3.
     *
     * @return void
     */
    public static function version123() {
        // set max age for log entries in days
        if (!get_option('personioIntegrationTemplateBackToListButton')) {
            update_option('personioIntegrationTemplateBackToListButton', 0);
        }

        // update db-version
        update_option('personioIntegrationVersion', WP_PERSONIO_INTEGRATION_VERSION);
    }

    /**
     * To run on update to (exact) version 2.0.3
     *
     * @return void
     */
    public static function version203() {
        // set max age for log entries in days
        if (!get_option('personioIntegrationUrl')) {
            update_option('personioIntegrationUrl', '', true);
        }
    }

    /**
     * Wrapper to run all version-specific updates, which are in this class.
     *
     * ADD HERE ANY NEW version-update-function.
     *
     * @return void
     */
    public static function runAllUpdates()
    {
        self::version123();
        self::version203();

        // reset import-flag
        delete_option(WP_PERSONIO_INTEGRATION_IMPORT_RUNNING);
    }

}