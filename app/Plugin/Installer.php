<?php
/**
 * File for handling installation of this plugin.
 *
 * @package personio-integration-light
 */

namespace App\Plugin;

use App\Helper;
use personioIntegration\Log;
use personioIntegration\updates;

/**
 * Helper-function for plugin-activation and -deactivation.
 */
class Installer {

	/**
	 * Activate the plugin.
	 *
	 * TODO add multisite-support?
	 *
	 * Either via activation-hook or via cli-plugin-reset.
	 *
	 * @return void
	 */
	public static function activation(): void {
		$error = false;

		// check if simplexml is available on this system.
		if ( ! function_exists( 'simplexml_load_string' ) ) {
			set_transient( 'personio_integration_no_simplexml', 1 );
			$error = true;
		}

		if ( false === $error ) {
			// set interval to daily if it is not set atm.
			if ( ! get_option( 'personioIntegrationPositionScheduleInterval' ) ) {
				update_option( 'personioIntegrationPositionScheduleInterval', 'daily' );
			}
			helper::set_import_schedule();

			// get the main frontend language depending on the language of this WP-installation.
			// if it is not already set.
			if ( ! get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE ) ) {
				update_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, Helper::get_wp_lang() );
			}

			// initially enable only the main-language of this page.
			if ( ! get_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION, false ) ) {
				$lang_key  = get_option( WP_PERSONIO_INTEGRATION_MAIN_LANGUAGE, WP_PERSONIO_INTEGRATION_LANGUAGE_EMERGENCY );
				$languages = helper::get_supported_languages();
				update_option(
					WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION,
					array(
						$lang_key => $languages[ $lang_key ],
					)
				);
				update_option( WP_PERSONIO_INTEGRATION_LANGUAGE_OPTION . $lang_key, 1 );
			}

			// set automatic import.
			if ( ! get_option( 'personioIntegrationEnablePositionSchedule' ) ) {
				update_option( 'personioIntegrationEnablePositionSchedule', 1 );
			}

			// set default timeout if not already set.
			if ( ! get_option( 'personioIntegrationUrlTimeout' ) ) {
				update_option( 'personioIntegrationUrlTimeout', 30 );
			}

			// set marker to delete all imported data on uninstall.
			if ( ! get_option( 'personioIntegrationDeleteOnUninstall' ) ) {
				update_option( 'personioIntegrationDeleteOnUninstall', 1 );
			}

			// set default excerpt-parts for list-page.
			if ( ! get_option( 'personioIntegrationTemplateExcerptDefaults' ) ) {
				update_option( 'personioIntegrationTemplateExcerptDefaults', array( 'recruitingCategory', 'schedule', 'office' ) );
			}

			// set default templates for default-page.
			if ( ! get_option( 'personioIntegrationTemplateContentDefaults' ) ) {
				update_option( 'personioIntegrationTemplateContentDefaults', array( 'title', 'content', 'formular' ) );
			}

			// set default excerpt-templates for detail-page.
			if ( ! get_option( 'personioIntegrationTemplateExcerptDetail' ) ) {
				update_option( 'personioIntegrationTemplateExcerptDetail', array( 'recruitingCategory', 'schedule', 'office' ) );
			}

			// set default jobdescription-template for detail-page.
			if ( ! get_option( 'personioIntegrationTemplateJobDescription' ) ) {
				update_option( 'personioIntegrationTemplateJobDescription', 'default' );
			}

			// set default templates for list-page.
			if ( ! get_option( 'personioIntegrationTemplateContentList' ) ) {
				update_option( 'personioIntegrationTemplateContentList', array( 'title', 'excerpt' ) );
			}

			// set default filter.
			if ( ! get_option( 'personioIntegrationTemplateFilter' ) ) {
				update_option( 'personioIntegrationTemplateFilter', array( 'recruitingCategory', 'schedule', 'office' ) );
			}

			// set default filter-type.
			if ( ! get_option( 'personioIntegrationFilterType' ) ) {
				update_option( 'personioIntegrationFilterType', 'linklist' );
			}

			// enable link to detail in list-view.
			if ( ! get_option( 'personioIntegrationEnableLinkInList' ) ) {
				update_option( 'personioIntegrationEnableLinkInList', 1 );
			}

			// set excerpt-separator.
			if ( ! get_option( 'personioIntegrationTemplateExcerptSeparator' ) ) {
				update_option( 'personioIntegrationTemplateExcerptSeparator', ', ' );
			}

			// set max age for log entries in days.
			if ( ! get_option( 'personioIntegrationMaxAgeLogEntries' ) ) {
				update_option( 'personioIntegrationMaxAgeLogEntries', 50 );
			}

			// run all updates.
			updates::run_all_updates();

			// save the current DB-version of this plugin.
			update_option( 'personioIntegrationVersion', WP_PERSONIO_INTEGRATION_VERSION );

			// refresh permalinks.
			set_transient( 'personio_integration_update_slugs', 1 );

			// initialize database.
			self::initialize_db();
		}
	}

	/**
	 * All db-specific handlings for activation.
	 *
	 * TODO collect on other way.
	 *
	 * @return void
	 */
	private static function initialize_db(): void {
		// initialize Log-database-table.
		$log = new Log();
		$log->create_table();
	}
}
