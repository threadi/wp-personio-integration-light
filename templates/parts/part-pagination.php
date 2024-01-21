<?php
/**
 * Template for output a pagination of positions.
 *
 * @version: 3.0.0
 * @package personio-integration-light
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output of pagination.
 */
?>
<div class="entry-content">
	<p>
		<?php
			$url = '';
		if ( ! empty( $form_id ) ) {
			$url .= '#' . $form_id;
		}
			$query = array(
				'base'    => str_replace( PHP_INT_MAX, '%#%', esc_url( get_pagenum_link( PHP_INT_MAX ) ) ) . $url,
				'format'  => '?paged=%#%',
				'current' => max( 1, get_query_var( 'paged' ) ),
				'total'   => $positions_obj->get_results()->max_num_pages,
			);
			echo wp_kses_post( paginate_links( $query ) );
			?>
	</p>
</div>
