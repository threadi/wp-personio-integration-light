<?php

/**
 * Add readonly tab in settings.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_settings_add_applications_readonly_tab(): void
{
    // output tab
    echo '<span class="nav-tab" title="'._x('Only in Pro.', 'wp-personio-integration').'">'.__('Applications', 'wp-personio-integration').' <span class="pro-marker">Pro</span></span>';
}
add_action( 'personio_integration_settings_add_tab', 'personio_integration_settings_add_applications_readonly_tab', 40);
