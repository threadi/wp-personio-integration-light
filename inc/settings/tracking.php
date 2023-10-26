<?php
/**
 * File to add tab for tracking-functions.
 *
 * @package wp-personio-integration
 */

/**
 * Add readonly tab in settings.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_settings_add_tracking_readonly_tab(): void
{
    // output tab.
    echo '<span class="nav-tab" title="'.__('Only in Pro.', 'personio-integration-light').'">'.__('Tracking', 'personio-integration-light').' <span class="pro-marker">Pro</span></span>';
}
add_action( 'personio_integration_settings_add_tab', 'personio_integration_settings_add_tracking_readonly_tab', 49 );
