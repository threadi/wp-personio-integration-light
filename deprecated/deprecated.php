<?php
/**
 * File which holds all deprecated functions from preview versions
 * if a project is using custom plugins or themes based on it to mark them as deprecated
 *
 * @package personio-integration-light
 */

include_once 'classes/helper.php';
include_once 'classes/position.php';
include_once 'classes/positions.php';
include_once 'classes/positions_pro.php';

define( "WP_PERSONIO_INTEGRATION_TAXONOMIES", \PersonioIntegrationLight\PersonioIntegration\Taxonomies::get_instance()->get_taxonomies() );
const WP_PERSONIO_INTEGRATION_CPT_PM_PID = 'personioId';

/**
 * Mark as deprecated.
 *
 * @deprecated since 3.0.0
 * @return array
 */
function personio_integration_admin_categories_labels(): array {
	_deprecated_function( __FUNCTION__, '3.0.0', '\PersonioIntegrationLight\PersonioIntegration\Taxonomies::get_instance()->get_taxonomy_labels_for_settings()' );
	return \PersonioIntegrationLight\PersonioIntegration\Taxonomies::get_instance()->get_taxonomy_labels_for_settings();
}

function personio_integration_position_shortcode( $attributes = array() ): string {
	_deprecated_function( __FUNCTION__, '3.0.0', 'PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition::get_instance()->shortcode_single()' );
	return PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition::get_instance()->shortcode_single( $attributes );
}

/**
 * Load alias for setup.
 */
if( ! class_exists( '\wpEasySetup\Setup' ) ) {
	class_alias('\easySetupForWordPress\Setup', 'wpEasySetup\Setup');
}
