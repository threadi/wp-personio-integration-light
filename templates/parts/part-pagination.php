<?php

defined( 'ABSPATH' ) || exit;

/**
 * Output of pagination.
 *
 * @version: 1.1.0
 */
?>
<div class="entry-content">
    <p>
        <?php
            $url = '';
            if( !empty($form_id) ) {
                $url .= '#'.$form_id;
            }
            $query = [
                'base' => str_replace( PHP_INT_MAX, '%#%', esc_url( get_pagenum_link( PHP_INT_MAX ) ) ).$url,
                'format' => '?paged=%#%',
                'current' => max( 1, get_query_var('paged') ),
                'total' => $positionsObj->getResult()->max_num_pages
            ];
            echo paginate_links($query);
        ?>
    </p>
</div>