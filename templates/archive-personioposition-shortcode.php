<?php

/**
 * Template for output of a list of positions via shortcode.
 */

use personioIntegration\Position;

defined( 'ABSPATH' ) || exit;

// embed filter
include "parts/part-filter.php";

// set the group-title
$groupTitle = '';

// loop through the list
if( $GLOBALS['personio_query_results']->have_posts() ) :
    while ( $GLOBALS['personio_query_results']->have_posts() ) : $GLOBALS['personio_query_results']->the_post();
        // get the Position as object
        $position = new Position(get_the_id());

        // get group title
        include 'parts/part-grouptitle.php';

        ?>
            <article id="post-<?php echo absint($position->ID); ?>" class="site-main post-<?php echo absint($position->ID); ?> <?php echo get_post_type($position->ID); ?> type-<?php echo get_post_type($position->ID); ?> status-<?php echo get_post_status($position->ID); ?> entry">
                <?php
                foreach( $personio_attributes["templates"] as $template ) {
                    do_action( 'personio_integration_get_'.$template, $position, $personio_attributes );
                }
                ?>
            </article>
        <?php
    endwhile;
    include "parts/part-pagination.php";
else:
    echo '<article><div class="entry-content"><p>'.__('No positions could be found.', 'wp-personio-integration').'</p></div></article>';
endif;