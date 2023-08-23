<?php

/**
 * Template for output of a single position.
 *
 * @version: 1.0.0
 */

defined( 'ABSPATH' ) || exit;

use personioIntegration\helper;

// embed block-specific styling
include helper::getTemplate("parts/styling.php");

?>
    <article id="post-<?php echo absint($position->ID); ?>" class="site-main post-<?php echo absint($position->ID); ?> <?php echo get_post_type($position->ID); ?> type-<?php echo get_post_type($position->ID); ?> status-<?php echo get_post_status($position->ID); ?> entry inside-article container qodef-container-inner site-content site-container content-bg content-area ht-container <?php echo esc_attr($personio_attributes['classes']); ?>" role="region" aria-label="position">
        <?php
        foreach( $personio_attributes["templates"] as $template ) {
            do_action( 'personio_integration_get_'.$template, $position, $personio_attributes );
        }
        ?>
    </article>
<?php
