<?php
/**
 * File for handling changes in data through plugin-updates.
 *
 * @package personio-integration-light
 */

namespace personioIntegration;

use WP_Query;

/**
 * Object which holds all version-specific updates.
 */
class Updates {

    /**
     * To run on update to (exact) version 1.2.3.
     *
     * @return void
     */
    public static function version123(): void
    {
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
    public static function version203(): void
    {
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
    public static function runAllUpdates(): void
    {
        self::version123();
        self::version203();
        self::version205();
        self::version211();
		self::version227();
        self::version240();
        self::version250();
		self::version255();
		self::version260();

        // reset import-flag
        delete_option(WP_PERSONIO_INTEGRATION_IMPORT_RUNNING);
    }

    /**
     * To run on update to (exact) version 2.0.5
     *
     * @return void
     */
    public static function version205(): void
    {
        // take care that import schedule is installed and active.
        helper::set_import_schedule();

        // set initial value for debug to disabled if not set.
        if (!get_option('personioIntegration_debug')) {
            update_option('personioIntegration_debug', 0);
        }

        // set initial value for debug to disabled if not set.
        if (!get_option('personioIntegrationTemplateBackToListUrl')) {
            update_option('personioIntegrationTemplateBackToListUrl', '');
        }

        // set initial value for debug to disabled if not set.
        if (!get_option('personioIntegrationEnableFilter')) {
            update_option('personioIntegrationEnableFilter', 0);
        }
    }

    /**
     * To run on update to (exact) version 2.1.1
     *
     * @return void
     */
    public static function version211(): void
    {
        $query = [
            'post_type' => 'wp_template',
            'post_name' => 'archive-'.WP_PERSONIO_INTEGRATION_CPT,
            'post_status' => 'any',
            'fields' => 'ids'
        ];
        $result = new WP_Query($query);
        if( $result->post_count == 0 ) {
            $archive_template = '
            <!-- wp:template-part {"slug":"header"} /-->

            <!-- wp:group {"tagName":"main","style":{"spacing":{"margin":{"top":"var:preset|spacing|70","bottom":"var:preset|spacing|70"}}},"layout":{"type":"constrained"}} -->
            <main class="wp-block-group" style="margin-top:var(--wp--preset--spacing--70);margin-bottom:var(--wp--preset--spacing--70)"><!-- wp:query-title {"type":"archive","showPrefix":false,"align":"wide","style":{"spacing":{"margin":{"bottom":"var:preset|spacing|50"}}}} /-->

            <!-- wp:wp-personio-integration/filter-list {"filter":["recruitingCategory","office","schedule"],"blockId":"061798b9-b51f-4903-8c3e-8d6d0e617011"} /-->

            <!-- wp:wp-personio-integration/list {"blockId":"ae57e576-b490-4ae6-b5cf-7cef4c9a8102"} /--></main>
            <!-- /wp:group -->

            <!-- wp:template-part {"slug":"footer","theme":"twentytwentythree","tagName":"footer"} /-->';
            $array = [
                'post_type' => 'wp_template',
                'post_status' => 'publish',
                'post_name' => 'archive-'.WP_PERSONIO_INTEGRATION_CPT,
                'post_content' => $archive_template,
                'post_title' => 'Archive: Stelle'
            ];
            wp_insert_post($array);
        }
    }

	/**
	 * To run on update to (exact) version 2.2.7.
	 *
	 * @return void
	 */
    public static function version227(): void {
		// enable search extension.
		if (!get_option('personioIntegrationExtendSearch')) {
			update_option('personioIntegrationExtendSearch', 1);
		}
	}

    /**
     * To run on update to (exact) version 2.4.0.
     *
     * @return void
     */
    public static function version240(): void {
        // set install-date if not set.
        if (!get_option('personioIntegrationLightInstallDate')) {
            update_option('personioIntegrationLightInstallDate', time());
        }
    }

    /**
     * To run on update to (exact) version 2.5.0.
     *
     * @return void
     */
    public static function version250(): void {
        // add user role to manage positions if it does not exist.
        $personio_position_manager_role = get_role('manage_personio_positions');
        if( null === $personio_position_manager_role ) {
            $personio_position_manager_role = add_role('manage_personio_positions', __('Manage Personio-based Positions', 'personio-integration-light'));
        }
        $personio_position_manager_role->add_cap( 'read' ); // to enter wp-admin
        $personio_position_manager_role->add_cap( 'read_'.WP_PERSONIO_INTEGRATION_CPT );
        $personio_position_manager_role->add_cap( 'manage_'.WP_PERSONIO_INTEGRATION_CPT );

        // get admin-role.
        $admin_role = get_role( 'administrator' );
        $admin_role->add_cap( 'read_'.WP_PERSONIO_INTEGRATION_CPT );
        $admin_role->add_cap( 'manage_'.WP_PERSONIO_INTEGRATION_CPT );
    }

	/**
	 * To run on update to (exact) version 2.5.5.
	 *
	 * @return void
	 */
	public static function version255(): void {
		// set default jobdescription-template for detail-page.
		if (!get_option('personioIntegrationTemplateJobDescription')) {
			update_option('personioIntegrationTemplateJobDescription', 'default');
		}
	}

	/**
	 * To run on update to (exact) version 2.6.0.
	 *
	 * @return void
	 */
	public static function version260(): void {
		// set default archive-template.
		if (!get_option('personioIntegrationTemplateContentListingTemplate')) {
			update_option('personioIntegrationTemplateContentListingTemplate', 'default');
		}
	}
}
