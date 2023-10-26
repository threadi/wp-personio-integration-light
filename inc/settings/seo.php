<?php
/**
 * File to add tab for SEO-functions.
 *
 * @package wp-personio-integration
 */

/**
 * Add readonly tab in settings.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_settings_add_seo_readonly_tab(): void
{
    // output tab.
    echo '<span class="nav-tab" title="'.__('Only in Pro.', 'personio-integration-light').'">'.__('SEO', 'personio-integration-light').' <span class="pro-marker">Pro</span></span>';
}
add_action( 'personio_integration_settings_add_tab', 'personio_integration_settings_add_seo_readonly_tab', 48 );
