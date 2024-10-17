<?php
/**
 * Selects and show the group-title if list is grouped by a taxonomy
 *
 * @param array    $personio_attributes List of settings.
 * @param Position $position       The positions object.
 *
 * @version: 3.3.0
 * @package personio-integration-light
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Position;

// only if group by is set.
if ( ! empty( $personio_attributes['groupby'] ) ) {
	// get the title of the given grouped taxonomy of this position.
	$new_group_title = $position->get_term_by_field( PersonioIntegrationLight\PersonioIntegration\Taxonomies::get_instance()->get_taxonomy_name_by_slug( $personio_attributes['groupby'] ), 'name', true );

	// output title if it has been changed during the loop.
	if ( strcmp( $new_group_title, $group_title ) ) {
		$group_title = $new_group_title;
		echo '<h2>' . esc_html( $new_group_title ) . '</h2>';
	}
}
