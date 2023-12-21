<?php
/**
 * Show styles.
 *
 * @version: 1.0.0
 * @package personio-integration-light
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output of block-specific styles
 *
 * @version: 1.0.0
 */

if ( ! empty( $styles ) ) {
	?>
		<style>
			<?php echo $styles; ?>
		</style>
	<?php
}
