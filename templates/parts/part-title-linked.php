<?php
/**
 * Template for output the title of a position.
 *
 * @param string $heading_size Number for heading (1 for h1, 2 for h2 ...)
 * @param Position $position   The object of the position.
 * @param array $attributes    Settings for output as array.
 *
 * @package personio-integration-light
 * @version: 4.0.0
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

use PersonioIntegrationLight\PersonioIntegration\Position;

?>
<header class="entry-content default-max-width">
	<h<?php echo absint( $heading_size ); ?> class="entry-title"><a href="<?php echo esc_url( $position->get_link() ); ?>"><?php echo esc_html( $position->get_title() ); ?></a></h<?php echo absint( $heading_size ); ?>>
</header>
