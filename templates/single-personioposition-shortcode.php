<?php

/**
 * Template for output of a single position.
 */

defined( 'ABSPATH' ) || exit;

?>
    <article id="post-<?php echo $position->ID; ?>" class="post-<?php echo $position->ID; ?> <?php echo get_post_type($position->ID); ?> type-<?php echo get_post_type($position->ID); ?> status-<?php echo get_post_status($position->ID); ?> entry">
        <?php
        foreach( $personio_attributes["templates"] as $template ) {
            do_action( 'personio_integration_get_'.$template, $position, $personio_attributes );
        }
        ?>
    </article>
<?php
