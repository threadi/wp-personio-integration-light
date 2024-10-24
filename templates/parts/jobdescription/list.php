<?php
/**
 * Template-file for job description as list.
 *
 * @param array    $attribute List of settings.
 * @param Position $position  The position as object.
 *
 * @package personio-integration-light
 * @version 4.0.0
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Position;

/**
 * Output of the content a single position as list.
 */
$content_array = $position->get_content_as_array();
if ( ! empty( $content_array ) ) {
	?><div class="entry-content <?php echo esc_attr( $attributes['classes'] ); ?>""><ul class="position-integration-jobdescription">
		<?php
		foreach ( $content_array as $content ) {
			?>
			<li><strong><?php echo esc_html( $content['name'] ); ?></strong><p><?php echo wp_kses_post( trim( $content['value'] ) ); ?></p></li>
			<?php
		}
		?>
	</ul></div>
	<?php
}
