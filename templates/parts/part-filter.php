<?php

defined( 'ABSPATH' ) || exit;

/**
 * Output of filter-list.
 */
if( !empty($personio_attributes["filter"]) && false !== $personio_attributes["showfilter"] ) :
    ?>
    <article class="site-main entry entry-content">
        <form action="" class="entry-content personio-position-filter personio-position-filter-<?php echo esc_attr($personio_attributes['filtertype']); ?>">
            <legend><?php echo __('Filter', 'wp-personio-integration'); ?></legend>
            <?php
            foreach ( $personio_attributes["filter"] as $filter ) :
                do_action( 'personio_integration_get_filter', $filter, $personio_attributes );
            endforeach;
            ?>
            <button type="submit"><?php echo __('Search', 'wp-personio-integration'); ?></button>
            <a href="<?php echo remove_query_arg('personiofilter'); ?>"><?php echo __('Reset Filter', 'wp-personio-integration'); ?></a>
        </form>
    </article>
<?php
endif;