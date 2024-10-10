<?php
/**
 * Template for output the excerpts of a single position as simple text.
 *
 * @param array $attributes List of settings.
 * @param string $separator The separator for the list from plugin settings.
 * @param array $details The list of excerpts to show (list of labels of the terms).
 *
 * @package personio-integration-light
 * @version: 3.3.0
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

?>
<div class="entry-content <?php echo esc_attr( $attributes['classes'] ); ?>">
	<p><?php echo esc_html( implode( $separator, $details ) ); ?></p>
</div>
