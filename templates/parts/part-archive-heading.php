<?php
/**
 * Helper for archive templates which using a <ul>-list for display positions.
 *
 * @param Position $positions_obj       The object of the position.
 * @param array    $personio_attributes List of attribute for this listing.
 *
 * @version: 5.0.0
 * @package wp-personio-integration
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Position;
use PersonioIntegrationLight\Plugin\Templates;

$use_li = false;
if ( ! empty( $personio_attributes['groupby'] ) ) {
	$first_position_id = $GLOBALS['personio_query_results']->get_posts()[0];
	$position_obj      = $positions_obj->get_position( get_the_id() );
	$position_obj->set_lang( $personio_attributes['lang'] );

	// get group title.
	include Templates::get_instance()->get_template( 'parts/part-grouptitle.php' );

	// mark to use li-elements to wrap the group title.
	$use_li = true;
}
