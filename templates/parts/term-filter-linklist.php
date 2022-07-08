<?php

defined( 'ABSPATH' ) || exit;

/**
 * Template for output a single term filter a linklist.
 */
?>
    <div>
        <label><?php echo esc_html($filtername); ?></label>
        <ul>
            <?php
            foreach( $terms as $term ) {
                $url = add_query_arg('personiofilter['.$filter.']', $term->term_id);
                $selected = $term->term_id == $value ? ' class="personio-filter-selected"' : '';
                ?><li><a href="<?php echo esc_url($url); ?>"<?php echo $selected; ?>><?php echo esc_html($term->name); ?></a></li><?php
            }
            ?>
        </ul>
    </div>
<?php