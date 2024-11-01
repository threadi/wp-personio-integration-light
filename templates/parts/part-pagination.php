<?php
/**
 * Template for output a pagination of positions.
 *
 * @param string    $pagination The pagination.
 *
 * @package personio-integration-light
 * @version 4.0.0
 */

// prevent direct access.
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
