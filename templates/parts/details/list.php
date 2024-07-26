<?php
/**
 * Template for output the excerpts of a single position as list.
 *
 * @param string   $separator The separator for the list from plugin settings.
 * @param array $details The list of excerpts to show (list of labels of the terms).
 * @param string $colon Setting for colon after taxonomy-label.
 * @param string $line_break Setting for linke break after taxonomy-label.
 *
 * @package personio-integration-light
 * @version: 3.0.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div class="entry-content">
	<?php
	foreach ( $details as $taxonomy_name => $value ) {
		echo '<p><strong>' . esc_html( $taxonomy_name ) . esc_html( $colon ) . '</strong>' . wp_kses_post( $line_break . $value ) . '</p>';
	}
	?>
</div>
