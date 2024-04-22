<?php
/**
 * Template for output the title of a position.
 *
 * @param string   $heading_size Number for heading (1 for h1, 2 for h2 ...)
 * @param Position $position     The object of the position.
 * @param array    $attributes   Settings for output as array.
 *
 * @package personio-integration-light
 * @version: 3.0.0
 */

use PersonioIntegrationLight\PersonioIntegration\Position;

defined( 'ABSPATH' ) || exit;

?>
<header class="entry-content default-max-width">
	<h<?php echo absint( $heading_size ); ?> class="entry-title"><?php echo esc_html( $position->get_title() ); ?></h<?php echo absint( $heading_size ); ?>>
</header>
