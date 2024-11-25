<?php
/**
 * Simple hint-wrapper for any messages in frontend.
 *
 * @param string $wrapper_id ID for the wrapper.
 * @param string $message The message.
 * @param string $type The type.
 *
 * @package personio-integration-light
 * @version: 4.0.0
 */

// prevent direct access.
defined( 'ABSPATH' ) || exit;

?>
<div id="<?php echo esc_attr( $wrapper_id ); ?>" class="entry-content personio-application-hint <?php echo esc_attr( $type ); ?>">
	<p><?php echo wp_kses_post( $message ); ?></p>
</div>
