<?php
/**
 * Template for output of a single position.
 *
 * @version: 1.0.0
 * @package personio-integration-light
 */

use App\PersonioIntegration\PostTypes\PersonioPosition;

defined( 'ABSPATH' ) || exit;

get_header();

/**
 * Set arguments to load content of this position via shortcode-function
 */
$arguments = array(
	'personioid' => get_post_meta( get_the_ID(), WP_PERSONIO_INTEGRATION_CPT_PM_PID, true ),
);
echo PersonioPosition::get_instance()->shortcode_position( $arguments );

get_footer();
