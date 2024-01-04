<?php
/**
 * Selects and show the group-title if list is grouped by a taxonomy
 *
 * @version: 3.0.0
 * @package personio-integration-light
 */

// only if group by is set.
if ( ! empty( $personio_attributes['groupby'] ) ) {
	// get the title of the given grouped taxonomy of this position.
	$new_group_title = $position->get_term_name( $personio_attributes['groupby'], 'name' );

	// output title if it has been changed during the loop.
	if ( strcmp( $new_group_title, $group_title ) ) {
		$group_title = $new_group_title;
		echo '<h2>' . esc_html( $new_group_title ) . '</h2>';
	}
}
