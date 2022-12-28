<?php

/**
 * Template for output of a list of positions via shortcode.
 */

use personioIntegration\helper;
use personioIntegration\Position;

defined( 'ABSPATH' ) || exit;

// embed filter
include helper::getTemplate("parts/part-filter.php");

// set the group-title
$groupTitle = '';

// loop through the list
if( $GLOBALS['personio_query_results']->have_posts() ) :
    while ( $GLOBALS['personio_query_results']->have_posts() ) : $GLOBALS['personio_query_results']->the_post();
        // get the Position as object
        $position = new Position(get_the_id());
        $position->lang = $personio_attributes['lang'];

        // get group title
        include helper::getTemplate('parts/part-grouptitle.php');

        ?>
            <article id="post-<?php echo absint($position->ID); ?>" class="site-main post-<?php echo absint($position->ID); ?> <?php echo get_post_type($position->ID); ?> type-<?php echo get_post_type($position->ID); ?> status-<?php echo get_post_status($position->ID); ?> entry inside-article container qodef-container-inner site-content site-container content-bg content-area ht-container ht-container">
                <?php
                foreach( $personio_attributes["templates"] as $template ) {
                    do_action( 'personio_integration_get_'.$template, $position, $personio_attributes );
                }
                ?>
            </article>
        <?php
    endwhile;
    include helper::getTemplate("parts/part-pagination.php");
else:
    ?><article class="site-main entry inside-article container qodef-container-inner site-content site-container content-bg content-area ht-container ht-container"><div class="entry-content"><p><?php __('No positions could be found.', 'wp-personio-integration'); ?></p></div></article><?php
endif;