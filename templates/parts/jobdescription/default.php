<?php
/**
 * Template-file for job description with headers.
 *
 * @param array $attribute List of settings.
 * @param Position $position The position as an object.
 *
 * @version: 5.5.0
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
	foreach ( $position->get_content_as_array() as $personio_integration_content ) {
		?>
		<h3><?php echo esc_html( $personio_integration_content['name'] ); ?></h3><p><?php echo wp_kses_post( trim( is_string( $personio_integration_content['value'] ) ? $personio_integration_content['value'] : '' ) ); ?></p>
		<?php
	}
	?>
</div>
