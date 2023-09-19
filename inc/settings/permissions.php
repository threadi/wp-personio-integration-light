<?php
/**
 * File to add tab for permissions-functions.
 *
 * @package wp-personio-integration
 */

/**
 * Add readonly tab in settings.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_settings_add_permissions_readonly_tab(): void
{
    // output tab.
    echo '<span class="nav-tab" title="'.__('Only in Pro.', 'wp-personio-integration').'">'.__('Permissions', 'wp-personio-integration').' <span class="pro-marker">Pro</span></span>';
}
add_action( 'personio_integration_settings_add_tab', 'personio_integration_settings_add_permissions_readonly_tab', 50 );