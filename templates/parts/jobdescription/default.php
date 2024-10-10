<?php
/**
 * Template-file for job description with headers.
 *
 * @param array $attribute List of settings.
 * @param Position $position The position as object.
 *
 * @version 3.3.0
 * @package personio-integration-light
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Position;

/**
 * Output of the content a single position with headers.
 */

?><div class="entry-content <?php echo esc_attr( $attributes['classes'] ); ?>">
	<?php
	foreach ( $position->get_content_as_array() as $content ) {
		?>
		<h3><?php echo esc_html( $content['name'] ); ?></h3><p><?php echo wp_kses_post( trim( $content['value'] ) ); ?></p>
		<?php
	}
	?>
</div>
