<?php
/**
 * File to add tab for tracking-functions.
 *
 * @package personio-integration-light
 */

/**
 * Add readonly tab in settings.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_settings_add_tracking_readonly_tab(): void {
	// output tab.
	echo '<span class="nav-tab" title="' . esc_attr__( 'Only in Pro.', 'personio-integration-light' ) . '">' . esc_html__( 'Tracking', 'personio-integration-light' ) . ' <span class="pro-marker">Pro</span></span>';
}
add_action( 'personio_integration_settings_add_tab', 'personio_integration_settings_add_tracking_readonly_tab', 49 );
