<?php
/**
 * File to add tab for SEO-functions.
 *
 * @package personio-integration-light
 */

/**
 * Add readonly tab in settings.
 *
 * @return void
 * @noinspection PhpUnused
 */
function personio_integration_settings_add_seo_readonly_tab(): void {
	// output tab.
	echo '<span class="nav-tab" title="' . esc_attr__( 'Only in Pro.', 'personio-integration-light' ) . '">' . esc_html__( 'SEO', 'personio-integration-light' ) . ' <span class="pro-marker">Pro</span></span>';
}
add_action( 'personio_integration_settings_add_tab', 'personio_integration_settings_add_seo_readonly_tab', 48 );
