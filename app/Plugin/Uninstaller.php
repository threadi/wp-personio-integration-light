<?php
/**
 * File for handling uninstallation of this plugin.
 *
 * @package personio-integration-light
 */

namespace App\Plugin;

use App\Helper;
use personioIntegration\cli;

/**
 * Helper-function for plugin-activation and -deactivation.
 */
class Uninstaller {
	/**
	 * Remove all plugin-data.
	 *
	 * Either via uninstall or via cli.
	 *
	 * @param array $delete_data Marker to delete all data.
	 * @return void
	 */
	public function run( array $delete_data = array() ): void {
		// remove schedule.
		wp_clear_scheduled_hook( 'personio_integration_schudule_events' ); // TODO migrate wrong written name.

		// remove widgets.
		do_action( 'widgets_init' );

		// remove transients.
		// TODO use transients-object.
		foreach ( WP_PERSONIO_INTEGRATION_TRANSIENTS as $transient => $setting ) {
			delete_transient( $transient );
			delete_transient( 'pi-dismissed-' . md5( $transient ) );
		}

		// delete all plugin-data.
		if ( ! empty( $delete_data[0] ) && 1 === absint( $delete_data[0] ) ) {
			// remove options.
			foreach ( Languages::get_instance()->get_languages() as $key => $lang ) {
				delete_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION . $key );
				delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_MD5 . $key );
				delete_option( WP_PERSONIO_INTEGRATION_OPTION_IMPORT_TIMESTAMP . $key );
			}

			// delete all collected data.
			( new cli() )->delete_all();

			// remove options.
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
				'personioIntegrationTemplateContentListingTemplate',
			);
			foreach ( $options as $option ) {
				delete_option( $option );
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
		foreach ( $wp_roles->roles as $role_name => $settings ) {
			$role = get_role( $role_name );
			$role->remove_cap( 'manage_' . WP_PERSONIO_INTEGRATION_CPT );
			$role->remove_cap( 'read_' . WP_PERSONIO_INTEGRATION_CPT );
		}

		// delete our custom database-tables.
		global $wpdb;
		$wpdb->query( sprintf( 'DROP TABLE IF EXISTS %s', esc_sql( $wpdb->prefix . 'personio_import_logs' ) ) );
	}
}
