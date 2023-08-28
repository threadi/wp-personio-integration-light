<?php
/**
 * Template: part-filter.php
 *
 * @version: 1.1.0
 */

use personioIntegration\helper;

defined( 'ABSPATH' ) || exit;

/**
 * Output of filter-list.
 */
if( !empty($personio_attributes["filter"]) && false !== $personio_attributes["showfilter"] && get_option('personioIntegrationPositionCount', 0) > 0 ) :
    // generate random id
    $form_id = "pif".md5(serialize($personio_attributes["filter"]));
    ?>
    <article id="<?php echo $form_id; ?>" class="site-main entry entry-content container inside-article container qodef-container-inner site-content site-content site-container content-bg content-area <?php echo esc_attr($personio_attributes['classes']); ?>" role="region" aria-label="<?php echo esc_html__('Filter for positions', 'wp-personio-integration' ); ?>">
        <form action="<?php echo esc_url(helper::get_current_url()); ?>#<?php echo $form_id; ?>" class="entry-content personio-position-filter personio-position-filter-<?php echo $personio_attributes['filtertype']; ?> qodef-container-inner site-content site-container content-bg content-area">
            <legend><?php echo __('Filter', 'wp-personio-integration'); ?></legend>
            <?php

            do_action('personio_integration_filter_pre', $personio_attributes);

            foreach ( $personio_attributes["filter"] as $filter ) :
                do_action( 'personio_integration_get_filter', $filter, $personio_attributes, $form_id );
            endforeach;

            do_action('personio_integration_filter_post', $personio_attributes);

            ?>
            <button type="submit"><?php echo __('Search', 'wp-personio-integration'); ?></button>
            <?php
            $url = remove_query_arg( 'personiofilter' );
            if( !empty($form_id) ) {
                $url .= '#'.$form_id;
            }
            ?>
            <a href="<?php echo esc_url($url); ?>" class="personio-position-filter-reset"><?php echo __('Reset Filter', 'wp-personio-integration'); ?></a>
        </form>
    </article>
<?php
endif;
