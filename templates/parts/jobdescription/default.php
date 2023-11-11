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

$content_array = $position->getContentAsArray();
foreach( $content_array as $content ) {
    ?><h3><?php echo esc_html($content['name']); ?></h3><?php
    ?><p><?php echo trim(wp_kses_post($content['value'])); ?></p><?php
}
