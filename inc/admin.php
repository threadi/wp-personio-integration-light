<?php
/**
 * File for functions to run in wp-admin only.
 *
 * @package personio-integration-light
 */

use personioIntegration\Import;
use personioIntegration\Position;
use personioIntegration\Positions;

/**
 * Start Import via AJAX.
 *
 * @return void
 * @noinspection PhpUnused
 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
 */
function personio_integration_admin_run_import(): void {
	// check nonce.
	check_ajax_referer( 'personio-run-import', 'nonce' );

	// run import.
	new Import();

	// return nothing.
	wp_die();
}

/**
 * Return state of the actual running import.
 *
 * @return void
 * @noinspection PhpUnused
 * @noinspection PhpNoReturnAttributeCanBeAddedInspection
 */
function personio_integration_admin_get_import_info(): void {
	// check nonce.
	check_ajax_referer( 'personio-get-import-info', 'nonce' );

	// return actual and max count of import steps.
	echo absint( get_option( WP_PERSONIO_OPTION_COUNT, 0 ) ) . ';' . absint( get_option( WP_PERSONIO_OPTION_MAX ) ) . ';' . absint( get_option( WP_PERSONIO_INTEGRATION_IMPORT_RUNNING, 0 ) );

	// return nothing else.
	wp_die();
}

/**
 * Add AJAX-endpoints.
 */
add_action(
	'admin_init',
	function () {
		add_action( 'wp_ajax_nopriv_dismiss_admin_notice', 'personio_integration_admin_dismiss' );
		add_action( 'wp_ajax_dismiss_admin_notice', 'personio_integration_admin_dismiss' );

		add_action( 'wp_ajax_nopriv_personio_run_import', 'personio_integration_admin_run_import' );
		add_action( 'wp_ajax_personio_run_import', 'personio_integration_admin_run_import' );

		add_action( 'wp_ajax_nopriv_personio_get_import_info', 'personio_integration_admin_get_import_info' );
		add_action( 'wp_ajax_personio_get_import_info', 'personio_integration_admin_get_import_info' );
	}
);
