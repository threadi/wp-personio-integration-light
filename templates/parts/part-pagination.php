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
			echo wp_kses_post( $pagination );
		?>
	</p>
</div>
