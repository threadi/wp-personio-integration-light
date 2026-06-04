<?php
/**
 * Template for output the excerpts of a single position as "list".
 *
 * @param array $attributes List of settings.
 * @param string $separator The separator for the list from plugin settings.
 * @param array $details The list of excerpts to show (list of terms).
 * @param string $colon Setting for colon after taxonomy-label.
 * @param string $line_break Setting for linke break after taxonomy-label.
 *
 * @package personio-integration-light
 * @version: 5.5.0
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

?>
<div class="entry-content <?php echo esc_attr( $attributes['classes'] ); ?>">
	<?php
	foreach ( $details as $personio_integration_taxonomy_name => $personio_integration_value ) {
		echo '<p><strong>' . esc_html( $personio_integration_taxonomy_name ) . esc_html( $colon ) . '</strong>' . wp_kses_post( $line_break . $personio_integration_value ) . '</p>';
	}
	?>
</div>
