<?php

namespace personioIntegration;

/**
 * Helper-function for plugin-activation and -deinstallation.
 */
class installer
{
    /**
     * Initialize the plugin.
     *
     * Either via activation-hook or via cli-plugin-reset.
     *
     * @return void
     */
    public static function initializePlugin(): void {
        $error = false;

        // check if simplexml is available on this system
        if( !function_exists("simplexml_load_string") ) {
            set_transient("personio_integration_no_simplexml", 1);
            $error = true;
        }

        if( false === $error ) {
            // set interval to daily if it is not set atm
            if (!get_option('personioIntegrationPositionScheduleInterval')) {
                update_option('personioIntegrationPositionScheduleInterval', 'daily');
            }
            helper::set_import_schedule();

            // get the main frontend language depending on the language of this WP-installation
            // if it is not already set
            if (!get_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE)) {
                update_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, Helper::get_wp_lang());
            }

            // initially enable only the main-language of this page
            if (!get_option(WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION, false)) {
                $langKey = get_option(WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY);
                $languages = helper::get_supported_languages();
                update_option(WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION, [
                    $langKey => $languages[$langKey]
                ]);
                update_option(WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION . $langKey, 1);
            }

            // set automatic import
            if (!get_option('personioIntegrationEnablePositionSchedule')) {
                update_option('personioIntegrationEnablePositionSchedule', 1);
            }

            // set default timeout if not already set
            if (!get_option('personioIntegrationUrlTimeout')) {
                update_option('personioIntegrationUrlTimeout', 30);
            }

            // set marker to delete all imported data on uninstall
            if (!get_option('personioIntegrationDeleteOnUninstall')) {
                update_option('personioIntegrationDeleteOnUninstall', 1);
            }

            // set default excerpt-parts for list-page
            if (!get_option('personioIntegrationTemplateExcerptDefaults')) {
                update_option('personioIntegrationTemplateExcerptDefaults', ['recruitingCategory', 'schedule', 'office']);
            }

            // set default templates for default-page
            if (!get_option('personioIntegrationTemplateContentDefaults')) {
                update_option('personioIntegrationTemplateContentDefaults', ['title', 'content', 'formular']);
            }

            // set default excerpt-templates for detail-page
            if (!get_option('personioIntegrationTemplateExcerptDetail')) {
                update_option('personioIntegrationTemplateExcerptDetail', ['recruitingCategory', 'schedule', 'office']);
            }

			// set default jobdescription-template for detail-page
			if (!get_option('personioIntegrationTemplateJobDescription')) {
				update_option('personioIntegrationTemplateJobDescription', 'default');
			}

            // set default templates for list-page
            if (!get_option('personioIntegrationTemplateContentList')) {
                update_option('personioIntegrationTemplateContentList', ['title', 'excerpt']);
            }

            // set default filter
            if (!get_option('personioIntegrationTemplateFilter')) {
                update_option('personioIntegrationTemplateFilter', ['recruitingCategory', 'schedule', 'office']);
            }

            // set default filter-type
            if (!get_option('personioIntegrationFilterType')) {
                update_option('personioIntegrationFilterType', 'linklist');
            }

            // enable link to detail in list-view
            if (!get_option('personioIntegrationEnableLinkInList')) {
                update_option('personioIntegrationEnableLinkInList', 1);
            }

            // set excerpt-separator
            if (!get_option('personioIntegrationTemplateExcerptSeparator')) {
                update_option('personioIntegrationTemplateExcerptSeparator', ', ');
            }

            // set max age for log entries in days
            if (!get_option('personioIntegrationMaxAgeLogEntries')) {
                update_option('personioIntegrationMaxAgeLogEntries', 50);
            }

            // run all updates
            updates::runAllUpdates();

            // save the current DB-version of this plugin
            update_option('personioIntegrationVersion', WP_PERSONIO_INTEGRATION_VERSION);

            // refresh permalinks
            set_transient('personio_integration_update_slugs', 1);

            // initialize database
            self::initializeDB();
        }
    }

    /**
     * All db-specific handlings for activation.
     *
     * @return void
     */
    private static function initializeDB(): void
    {
        // initialize Log-database-table
        $log = new Log();
        $log->createTable();
    }

    /**
     * Remove all plugin-data.
     *
     * Either via uninstall or via cli.
     *
     * @param array $deleteData
     * @return void
     */
    public static function removeAllData(array $deleteData = [] ): void
    {
        // remove schedule
        wp_clear_scheduled_hook( 'personio_integration_schudule_events' );

        // remove widgets
        do_action('widgets_init');

        // remove transients
        foreach( WP_PERSONIO_INTEGRATION_TRANSIENTS as $transient => $setting ) {
            delete_transient($transient);
            delete_transient('pi-dismissed-'.md5($transient));
        }

        // delete all plugin-data
        if( !empty($deleteData[0]) && absint($deleteData[0]) == 1 ) {
            // remove options
            foreach( helper::get_supported_languages() as $key => $lang ) {
                delete_option(WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION.$key);
                delete_option(WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5.$key);
                delete_option(WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP.$key);
            }

            // delete all collected data
            (new cli)->deleteAll();

            // remove options
            $options = array(
                'personioIntegrationUrlTimeout',
                'personioIntegrationUrl',
                'personioIntegrationLanguages',
                'personioIntegration_debug',
                'personioIntegrationMainLanguage',
                'personioIntegrationDeleteOnUninstall',
                'personioIntegrationTemplateExcerptDefaults',
                'personioIntegrationPositionScheduleInterval',
                'personioIntegrationEnablePositionSchedule',
                'personioIntegrationTemplateContentDefaults',
                'personioIntegrationTemplateExcerptDetail',
                'personioIntegrationTemplateContentList',
                'personioIntegrationEnableFilter',
                'personioIntegrationTemplateFilter',
                'personioIntegrationEnableLinkInList',
                'personioIntegrationEnableLinkInDetail',
                'personioIntegrationTemplateExcerptSeparator',
                'personioIntegrationVersion',
                'personioIntegrationFilterType',
                'personioIntegrationMaxAgeLogEntries',
                'personioIntegrationEnableForm',
                'personioIntegrationPositionCount',
                'personioIntegrationTemplateBackToListButton',
                'personioIntegrationTemplateBackToListUrl',
                'personioTaxonomyDefaults',
                WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION,
                WP_PERSONIO_OPTION_COUNT,
                WP_PERSONIO_OPTION_MAX,
                WP_PERSONIO_INTEGRATION_IMPORT_RUNNING,
	            'personioIntegrationExtendSearch',
                'personioIntegrationLightInstallDate',
				'personioIntegrationTemplateJobDescription',
	            'personioIntegrationTemplateContentListingTemplate'
            );
            foreach ($options as $option) {
                delete_option($option);
            }
        }

        /**
         * Remove our own role.
         */
        remove_role( 'manage_personio_positions' );

        /**
         * Remove our capabilities from other roles.
         */
        global $wp_roles;
        foreach( $wp_roles->roles as $role_name => $settings ) {
            $role = get_role( $role_name );
            $role->remove_cap( 'manage_'.WP_PERSONIO_INTEGRATION_CPT );
            $role->remove_cap( 'read_'.WP_PERSONIO_INTEGRATION_CPT );
        }

        // delete our custom database-tables
        global $wpdb;
        $table_name = $wpdb->prefix . 'personio_import_logs';
        $sql = "DROP TABLE IF EXISTS ".$table_name;
        $wpdb->query($sql);
    }
}
