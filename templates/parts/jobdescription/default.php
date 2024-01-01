<?php
/**
 * Template-file for job description with headers.
 *
 * @package personio-integration-light
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output of the content a single position with headers.
 *
 * @version: 1.0.1
 */

?><div class="entry-content"><?php
foreach ( $position->get_content_as_array() as $content ) {
	?><h3><?php echo esc_html( $content['name'] ); ?></h3><p><?php echo wp_kses_post( trim( $content['value'] ) ); ?></p><?php
}
?></div>
