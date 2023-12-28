<?php
/**
 * Main file for initialization of this plugin in frontend and backend.
 *
 * @package personio-integration-light
 */

use personioIntegration\Import;

/**
 * Run the scheduled positions-import.
 * Only if it is enabled.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_schudule_event_import_positions(): void {
	if ( 1 === absint( get_option( 'personioIntegrationEnablePositionSchedule', 0 ) ) ) {
		new Import();
	}
}
add_action( 'personio_integration_schudule_events', 'personio_integration_schudule_event_import_positions', 10, 0 );

/**
 * Return true for import any positions.
 *
 * @return bool
 * @noinspection PhpUnused
 */
function personio_integration_import_single_position(): bool {
	return true;
}
add_filter( 'personio_integration_import_single_position', 'personio_integration_import_single_position', 10, 2 );

/**
 * Add each position to list during import.
 *
 * @return true
 */
function personio_integration_import_single_position_filter_existing(): bool {
	return true;
}
add_filter( 'personio_integration_import_single_position_filter_existing', 'personio_integration_import_single_position_filter_existing' );
