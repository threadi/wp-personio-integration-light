<?php
/**
 * Template for output a position title.
 *
 * @version: 3.0.0
 * @package personio-integration-light
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output of the title of a single position.
 */

?><h2><?php echo esc_html( $position->getTitle() ); ?></h2>
