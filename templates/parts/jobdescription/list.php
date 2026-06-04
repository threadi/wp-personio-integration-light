<?php
/**
 * Template-file for job description as "list".
 *
 * @param array    $attribute List of settings.
 * @param Position $position  The position as an object.
 *
 * @package personio-integration-light
 * @version: 5.5.0
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Position;

/**
 * Output of the content a single position as "list".
 */
$personio_integration_content_array = $position->get_content_as_array();
if ( ! empty( $personio_integration_content_array ) ) {
	?><div class="entry-content <?php echo esc_attr( $attributes['classes'] ); ?>"><ul class="position-integration-jobdescription">
		<?php
		foreach ( $personio_integration_content_array as $personio_integration_content ) {
			?>
			<li><strong><?php echo esc_html( $personio_integration_content['name'] ); ?></strong><p><?php echo wp_kses_post( trim( is_string( $personio_integration_content['value'] ) ? $personio_integration_content['value'] : '' ) ); ?></p></li>
			<?php
		}
		?>
	</ul></div>
	<?php
}
