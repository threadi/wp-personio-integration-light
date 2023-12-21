<?php
/**
 * Template for output a position title.
 *
 * @version: 2.0.0
 * @package personio-integration-light
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output of the title of a single position.
 *
 * @version: 1.0.0
 */

?><h2><?php echo esc_html( $position->getTitle() ); ?></h2>
