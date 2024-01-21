<?php
/**
 * Template for output of a single position.
 *
 * @version: 3.0.0
 * @package personio-integration-light
 */

use PersonioIntegrationLight\PersonioIntegration\PostTypes\PersonioPosition;

defined( 'ABSPATH' ) || exit;

get_header();

/**
 * Set arguments to load content of this position via shortcode-function
 */
$arguments = array(
	'personioid' => get_post_meta( get_the_ID(), WP_PERSONIO_INTEGRATION_MAIN_CPT_PM_PID, true ),
);
echo wp_kses_post( PersonioPosition::get_instance()->shortcode_single( $arguments ) );

get_footer();
