<?php

defined( 'ABSPATH' ) || exit;

/**
 * Output of the title of a single position.
 *
 * @version: 1.0.0
 */

?><h2><?php echo esc_html($position->getTitle()); ?></h2>