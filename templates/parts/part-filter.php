<?php

/**
 * Template: part-filter.php
 *
 * @version: 1.0.0
 */

use personioIntegration\helper;

defined( 'ABSPATH' ) || exit;

/**
 * Output of filter-list.
 */
if( !empty($personio_attributes["filter"]) && false !== $personio_attributes["showfilter"] && get_option('personioIntegrationPositionCount', 0) > 0 ) :
    // generate random id
    $formId = "pif".md5(serialize($personio_attributes["filter"]));
    ?>
    <article id="<?php echo $formId; ?>" class="site-main entry entry-content container inside-article container qodef-container-inner site-content site-content site-container content-bg content-area <?php echo esc_attr($personio_attributes['classes']); ?>">
        <form action="<?php echo esc_url(helper::get_current_url()); ?>#<?php echo $formId; ?>" class="entry-content personio-position-filter personio-position-filter-<?php echo $personio_attributes['filtertype']; ?> qodef-container-inner site-content site-container content-bg content-area">
            <legend><?php echo __('Filter', 'wp-personio-integration'); ?></legend>
            <?php

            do_action('personio_integration_filter_pre', $personio_attributes);

            foreach ( $personio_attributes["filter"] as $filter ) :
                do_action( 'personio_integration_get_filter', $filter, $personio_attributes );
            endforeach;

            do_action('personio_integration_filter_post', $personio_attributes);

            ?>
            <button type="submit"><?php echo __('Search', 'wp-personio-integration'); ?></button>
            <a href="<?php echo remove_query_arg('personiofilter'); ?>" class="personio-position-filter-reset"><?php echo __('Reset Filter', 'wp-personio-integration'); ?></a>
        </form>
    </article>
<?php
endif;