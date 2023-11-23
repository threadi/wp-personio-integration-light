<?php
/**
 * Template for output of a list of positions via shortcode.
 *
 * @version: 2.0.0
 * @package personio-integration-light
 */

use personioIntegration\helper;

defined( 'ABSPATH' ) || exit;

// embed block-specific styling
include helper::getTemplate("parts/styling.php");

// embed filter
include helper::getTemplate("parts/part-filter.php");

if( !empty($personio_attributes["listing_template"]) && !empty($personio_attributes["templates"]) ) {

    // loop through the list by using set listing template.
    if( $GLOBALS['personio_query_results']->have_posts() ) :
		include helper::getTemplate('parts/archive/'.$personio_attributes['listing_template'].'.php');
        include helper::getTemplate("parts/part-pagination.php");
    else:
        ?><article class="site-main entry inside-article container qodef-container-inner site-content site-container content-bg content-area ht-container ht-container"><div class="entry-content"><p><?php _e('There are currently no positions available.', 'personio-integration-light'); ?></p></div></article><?php
    endif;
}
wp_reset_query();
