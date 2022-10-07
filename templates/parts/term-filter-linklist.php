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
            for( $t=0;$t<count($terms);$t++ ) {
                if( !empty($terms[$t]) ) {
                    $url = add_query_arg('personiofilter['.$filter.']', $terms[$t]->term_id);
                    ?><li><a href="<?php echo esc_url($url); ?>"<?php echo ($terms[$t]->term_id == $value ? ' class="personio-filter-selected"' : ''); ?>><?php echo esc_html($terms[$t]->name); ?></a></li><?php
                }
            }
            ?>
        </ul>
    </div>
<?php
