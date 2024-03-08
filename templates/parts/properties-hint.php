<?php
/**
 * Simple hint-wrapper for any messages.
 *
 * @version: 3.0.0
 */

defined( 'ABSPATH' ) || exit;

?>
<div id="<?php echo esc_attr($wrapper_id); ?>" class="entry-content personio-application-hint <?php echo esc_attr( $type ); ?>">
	<p><?php echo wp_kses_post( $message ); ?></p>
</div>
