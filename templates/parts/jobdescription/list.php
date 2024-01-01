<?php
/**
 * Template-file for job description as list.
 *
 * @package personio-integration-light
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output of the content a single position as list.
 *
 * @version: 1.0.0
 */

$content_array = $position->get_content_as_array();
if ( ! empty( $content_array ) ) {
	?><div class="entry-content"><ul class="position-integration-jobdescription"><?php
	foreach ( $content_array as $content ) {
		?>
		<li><strong><?php echo esc_html( $content['name'] ); ?></strong><p><?php echo wp_kses_post( trim( $content['value'] ) ); ?></p></li>
		<?php
	}
	?></ul></div><?php
}
