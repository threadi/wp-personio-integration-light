<?php
/**
 * Selects and show the group-title if the list is grouped by a taxonomy
 *
 * @param array    $personio_attributes List of settings.
 * @param Position $personio_integration_position_obj       The object for a single position.
 * @param bool $personio_integration_use_li Marker to use <li>.
 *
 * @version: 5.5.0
 * @package personio-integration-light
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Position;

// only if group by is set.
if ( ! empty( $personio_attributes['groupby'] ) ) {
	// get the title of the given grouped taxonomy for this position.
	$personio_integration_new_group_title = $personio_integration_position_obj->get_term_by_field( PersonioIntegrationLight\PersonioIntegration\Taxonomies::get_instance()->get_taxonomy_name_by_slug( $personio_attributes['groupby'] ), 'name', true );

	// output title if it has been changed during the loop.
	if ( strcmp( $personio_integration_new_group_title, $personio_integration_group_title ) ) {
		$personio_integration_group_title = $personio_integration_new_group_title;
		if ( $personio_integration_use_li ) {
			echo '<li>';
		}
		echo '<h2>' . esc_html( $personio_integration_new_group_title ) . '</h2>';
		if ( $personio_integration_use_li ) {
			echo '</li>';
		}
	}
}
