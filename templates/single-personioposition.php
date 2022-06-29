<?php

/**
 * Template for output of a single position.
 */

defined( 'ABSPATH' ) || exit;

get_header();

/**
 * Set arguments to load content of this position via shortcode-function
 */
$arguments = [
    'personioid' => get_post_meta(get_the_ID(), WP_PERSONIO_INTEGRATION_CPT_PM_PID, true)
];
echo personio_integration_position_shortcode($arguments);

get_footer();